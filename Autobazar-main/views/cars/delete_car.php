<?php
session_start();
require_once '../../config/Database.php'; 

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: ../../index.php");
    exit();
}

$car_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// 1. JAVÍTÁS: Pontosított admin ellenőrzés (ugyanaz, mint az admin.php-ban)
$is_admin = (
    (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') || 
    (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1)
);

// Lekérjük az autót, admin esetén a pouzivatel_id megkötése nélkül
if ($is_admin) {
    $check_sql = "SELECT obraz FROM inzerati WHERE id = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("i", $car_id);
} else {
    $check_sql = "SELECT obraz FROM inzerati WHERE id = ? AND pouzivatel_id = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("ii", $car_id, $user_id);
}

$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    // KÉPEK TÖRLÉSE (Galéria)
    $gal_sql = "SELECT cesta_k_obrazku FROM galeria WHERE inzerat_id = ?";
    $stmt_gal = $conn->prepare($gal_sql);
    $stmt_gal->bind_param("i", $car_id);
    $stmt_gal->execute();
    $gal_res = $stmt_gal->get_result();
    
    while ($gal_row = $gal_res->fetch_assoc()) {
        $file_path = "../../" . $gal_row['cesta_k_obrazku']; 
        if (file_exists($file_path)) unlink($file_path);
    }

    // FŐ KÉP TÖRLÉSE
    $main_img = "../../" . $row['obraz'];
    if (file_exists($main_img) && strpos($main_img, 'default') === false) {
        unlink($main_img);
    }

    // TÖRLÉS AZ ADATBÁZISBÓL (Galéria)
    $conn->query("DELETE FROM galeria WHERE inzerat_id = $car_id");
    
    // TÖRLÉS AZ ADATBÁZISBÓL (Inzerát)
    if ($is_admin) {
        $delete_sql = "DELETE FROM inzerati WHERE id = ?";
        $stmt_del = $conn->prepare($delete_sql);
        $stmt_del->bind_param("i", $car_id);
    } else {
        $delete_sql = "DELETE FROM inzerati WHERE id = ? AND pouzivatel_id = ?";
        $stmt_del = $conn->prepare($delete_sql);
        $stmt_del->bind_param("ii", $car_id, $user_id);
    }
    
    if ($stmt_del->execute()) {
        // 2. JAVÍTÁS: Ha az admin felületről jött a kérés, oda megyünk vissza, különben a profilra
        if (isset($_GET['from']) && $_GET['from'] === 'admin') {
            header("Location: ../../admin.php?status=deleted");
        } else {
            header("Location: moje-inzeraty.php?status=deleted");
        }
        exit();
    } else {
        echo "Chyba: " . $conn->error;
    }
} else {
    die("Chyba: Nemáte oprávnenie na zmazanie tohto inzerátu alebo inzerát neexistuje.");
}
?>
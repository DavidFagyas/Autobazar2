<?php
session_start();

// 1. JAVÍTÁS: Két mappával visszalépünk (../../), hogy elérjük a configot a gyökérben
require_once '../../config/Database.php';

// Biztonság: Csak bejelentkezett admin törölhet
if (!isset($_SESSION['user_id']) || (isset($_SESSION['role']) && $_SESSION['role'] !== 'admin' && (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1))) {
    die("Nemáte oprávnenie.");
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $userId = intval($_GET['id']);

    // 1. LÉPÉS: Töröljük a felhasználó összes hirdetését (inzerati táblából)
    $deleteInzeraty = $conn->prepare("DELETE FROM inzerati WHERE pouzivatel_id = ?");
    $deleteInzeraty->bind_param("i", $userId);
    $deleteInzeraty->execute();

    // 2. LÉPÉS: Töröljük magát a felhasználót (users táblából)
    $deleteUser = $conn->prepare("DELETE FROM users WHERE id = ?");
    $deleteUser->bind_param("i", $userId);
    
    if ($deleteUser->execute()) {
        // 2. JAVÍTÁS: Két mappát visszalépve dobjuk vissza a gyökérben lévő admin.php-ra
        header("Location: ../../admin.php");
        exit();
    } else {
        echo "Chyba pri mazaní používateľa.";
    }
} else {
    echo "ID nebolo zadané.";
}
?>
<?php
session_start();
require '../../config/Database.php';

if (isset($_GET['id']) && isset($_SESSION['user_id'])) {
    $id = intval($_GET['id']);
    $user_id = $_SESSION['user_id'];

    // BIZTONSÁG: Csak akkor törölheted, ha az ID a tiéd!
    $stmt = $conn->prepare("DELETE FROM inzerati WHERE id = ? AND pouzivatel_id = ?");
    $stmt->bind_param("ii", $id, $user_id);
    
    if ($stmt->execute()) {
        header("Location: moje_inzeraty.php?status=deleted");
    } else {
        echo "Chyba pri mazaní.";
    }
} else {
    header("Location: moje_inzeraty.php");
}
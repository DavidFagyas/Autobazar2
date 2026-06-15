<?php
// 1. A munkamenet indítása MINDIG az első sor legyen!
session_start();

// 2. Adatbázis kapcsolat beemelése
require '../../config/Database.php';

// 3. JAVÍTOTT ÚTVONAL: Visszalépünk egy mappát (fel a views-ba), majd be a layout-ba
include_once '../layout/header.php'; 

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $phone = trim($_POST['phone']); // ÚJ: Telefonszám átvétele

    // Heslá kontrola
    if ($password != $confirm_password) {
        $error = "Heslá sa nezhodujú!";
    } else {

        // Kontrola používateľa
        $check = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $check->bind_param("s", $username);
        $check->execute();

        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $error = "Používateľ už existuje!";
        } else {

            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            // ÚJ: phone hozzáadva az INSERT-hez
            $stmt = $conn->prepare("INSERT INTO users (username, password, phone) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $hashed_password, $phone);

            if ($stmt->execute()) {
                header("Location: ../../views/auth/login.php?success=registered");
                exit();
            } else {
                $error = "Chyba pri registrácii!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="sk">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/Style1.css">
    <title>Autobazár Dávid - Registrácia</title>
</head>

<body>

<br><br>

<div style="max-width: 400px; margin: 0 auto; background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px;">
    <?php
    if (!empty($error)) {
        echo "<p style='color:red; font-weight:bold;'>$error</p>";
    }
    ?>

    <form method="post">
        Používateľské meno:<br>
        <input type="text" name="username" style="width: 100%; padding: 8px; margin-top: 5px;" required><br><br>

        Telefónne číslo:<br>
        <input type="tel" name="phone" placeholder="+421..." style="width: 100%; padding: 8px; margin-top: 5px;" required><br><br>

        Heslo:<br>
        <input type="password" name="password" style="width: 100%; padding: 8px; margin-top: 5px;" required><br><br>

        Potvrdenie hesla:<br>
        <input type="password" name="confirm_password" style="width: 100%; padding: 8px; margin-top: 5px;" required><br><br>

        <input type="submit" value="Registrovať sa" style="background: #cc0000; color: white; border: none; padding: 10px 20px; cursor: pointer; font-weight: bold; width: 100%; border-radius: 5px;">
    </form>

    <br>
    <a href="../../views/auth/login.php" style="color: #fff; text-decoration: underline;">Máte účet? Prihlásiť sa</a>
</div>

</body>
</html>
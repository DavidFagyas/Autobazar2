<?php
require '../../config/Database.php';;

session_start();

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

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

            $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $hashed_password);

            if ($stmt->execute()) {

                header("Location:views/auth/login.php");
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
    <title>Autobazár Dávid</title>
</head>

<body>

<header>

    <a href="index.php">
        <img src="../../assets/logo.jpg" height="75px" width="105px">
    </a>

    <h1>Registrácia</h1>

    <nav>
        <ul class="navbar-nav">

            <li><a class="nav-link" href="index.php">Domov</a></li>

            <li><a class="nav-link" href="dostupne-auta.php">Dostupné autá</a></li>

            <li><a class="nav-link" href="onas.php">O nás</a></li>

            <li><a class="nav-link" href="kontakt.php">KONTAKT</a></li>

        </ul>
    </nav>

</header>

<br><br>

<?php
if (!empty($error)) {
    echo "<p style='color:red;'>$error</p>";
}
?>

<form method="post">

    Používateľské meno:<br>
    <input type="text" name="username" required><br><br>

    Heslo:<br>
    <input type="password" name="password" required><br><br>

    Potvrdenie hesla:<br>
    <input type="password" name="confirm_password" required><br><br>

    <input type="submit" value="Registrovať sa">

</form>

<br>

<a href="views/auth/login.php">Máte účet? Prihlásiť sa</a>

</body>
</html>
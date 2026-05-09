<?php
require '../../config/Database.php';

session_start();

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {

        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {

            $_SESSION['user_id'] = $row['id'];
            $_SESSION['is_admin'] = $row['is_admin'];

            header("Location: index.php");
            exit();

        } else {
            $error = "Nesprávne heslo!";
        }

    } else {
        $error = "Používateľ neexistuje!";
    }
}
?>

<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../../assets/Style1.css">

    <title>Prihlásenie</title>
</head>

<body>

<header>

    <a href="index.php">
        <img src="../../assets/logo.jpg" height="75px" width="105px">
    </a>

    <h1>Prihlásenie</h1>

</header>

<br><br>

<?php
if (!empty($error)) {
    echo "<p style='color:red;'>$error</p>";
}
?>

<form method="post">

    <label>Používateľské meno:</label><br>
    <input type="text" name="username" required><br><br>

    <label>Heslo:</label><br>
    <input type="password" name="password" required><br><br>

    <input type="submit" value="Prihlásiť sa">

</form>

</body>
</html>
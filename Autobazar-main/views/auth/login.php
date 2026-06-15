<?php
// 1. Kapcsolat behívása abszolút útvonallal, hogy bárhonnan működjön
require_once '../../config/Database.php';

// 2. A fejléc behívása (visszalépünk az auth mappából, majd be a layout mappába)
include_once '../layout/header.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Különleges kezelés a fix admin fiókhoz
    if ($username === 'admin' && $password === 'admin') {
        $_SESSION['user_id'] = 0;
        $_SESSION['username'] = 'admin';
        $_SESSION['role'] = 'admin';
        header("Location: /Autobazar-main/admin.php"); // Javított útvonal
        exit();
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'] ?? 'user'; // Szerepkör mentése

            header("Location: /Autobazar-main/index.php");
            exit();
        } else {
            $error = "Nesprávne heslo!";
        }
    } else {
        $error = "Používateľ neexistuje!";
    }
}
?>

<!-- Mivel a header.php már tartalmazza a <head>-et és a <header> részt, itt csak a tartalom kell -->

<div style="max-width: 400px; margin: 50px auto; padding: 20px; background: #f9f9f9; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
    <h2 style="text-align: center;">Prihlásenie</h2>

    <?php if (!empty($error)): ?>
        <p style="color:red; text-align: center;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="post">
        <label>Používateľské meno:</label><br>
        <input type="text" name="username" style="width:100%; padding: 8px; margin: 8px 0;" required><br>

        <label>Heslo:</label><br>
        <input type="password" name="password" style="width:100%; padding: 8px; margin: 8px 0;" required><br><br>

        <input type="submit" value="Prihlásiť sa" style="width:100%; padding: 10px; background-color: #333; color: white; border: none; cursor: pointer;">
    </form>
    
    <p style="text-align: center; margin-top: 15px;">
        Nemáte účet? <a href="register.php">Zaregistrujte sa tu</a>
    </p>
</div>

</body>
</html>
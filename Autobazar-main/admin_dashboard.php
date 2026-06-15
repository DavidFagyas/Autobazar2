<?php
session_start();
require 'config/Database.php';
include_once 'views/layout/header.php';

// Csak az admin jöhet be
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: views/auth/login.php");
    exit();
}

// 1. Összes hirdetés lekérése
$cars = $conn->query("SELECT * FROM inzerati");

// 2. Összes felhasználó lekérése (feltételezve, hogy a táblád neve 'users')
$users = $conn->query("SELECT id, username, email FROM users WHERE username != 'admin'");
?>

<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="assets/Style1.css">
    <title>Admin Panel</title>
    <style>
        .admin-section { margin: 40px auto; max-width: 1000px; background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #333; color: white; }
        .btn-delete { background: red; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px; }
    </style>
</head>
<body>

<header>
    <h1>Adminisztrátori Felület</h1>
    <nav><a href="index.php">Späť na web</a> | <a href="views/auth/logout.php" style="color:red;">Odhlásiť sa</a></nav>
</header>

<div class="admin-section">
    <h2>Správa inzerátov (Összes hirdetés)</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Názov</th>
            <th>Cena</th>
            <th>Akcia</th>
        </tr>
        <?php while($car = $cars->fetch_assoc()): ?>
        <tr>
            <td><?php echo $car['id']; ?></td>
            <td><?php echo $car['Nazov']; ?></td>
            <td><?php echo $car['cena']; ?> €</td>
            <td>
                <a href="views/cars/delete_car.php?id=<?php echo $car['id']; ?>&admin=1" class="btn-delete" onclick="return confirm('Zmazať inzerát?')">Zmazať</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <h2>Registrovaní používatelia (Felhasználók)</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Meno</th>
            <th>Email</th>
            <th>Akcia</th>
        </tr>
        <?php while($user = $users->fetch_assoc()): ?>
        <tr>
            <td><?php echo $user['id']; ?></td>
            <td><?php echo $user['username']; ?></td>
            <td><?php echo $user['email']; ?></td>
            <td>
                <a href="admin_actions.php?delete_user=<?php echo $user['id']; ?>" class="btn-delete" onclick="return confirm('Zmazať používateľa?')">Zmazať</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>
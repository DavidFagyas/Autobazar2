<?php
session_start();
require 'config/Database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM inzerati WHERE pouzivatel_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="assets/Style1.css">
    <title>Moje inzeráty</title>
</head>
<body>
    <h1>Moje inzeráty</h1>
    <table>
        <tr>
            <th>Auto</th>
            <th>Cena</th>
            <th>Akcie</th>
        </tr>
        <?php while($car = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $car['Nazov']; ?></td>
            <td><?php echo number_format($car['cena'], 2); ?> €</td>
            <td>
                <a href="edit-car.php?id=<?php echo $car['id']; ?>" class="footerbtn">Upraviť</a>
                <a href="delete-car.php?id=<?php echo $car['id']; ?>" class="footerbtn" 
                   style="background: red;" onclick="return confirm('Naozaj vymazať?')">Zmazať</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
<?php
session_start();
require_once '../../config/Database.php';

// 1. BIZTONSÁGI ELLENŐRZÉS
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 2. LEKÉRDEZÉS (Figyelj az oszlopnévre: pouzivatel_id vagy user_id?)
$stmt = $conn->prepare("SELECT id, Nazov, cena, obraz FROM inzerati WHERE pouzivatel_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// 3. HEADER BEHÍVÁSA (Hogy legyen menüsorod)
include_once '../layout/header.php'; 
?>

<div style="max-width: 1100px; margin: 40px auto; padding: 20px; background-color: #fff; border-radius: 10px; color: #333;">
    <h2 style="text-align:center;">Správa vašich inzerátov</h2>
    <p style="text-align:center; color: #666;">Tu môžete upravovať vagy mazať svoje ponuky.</p>
    <hr>

    <?php if ($result && $result->num_rows > 0): ?>
        <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <thead style="background: #333; color: white;">
                <tr>
                    <th style="padding: 15px; border: 1px solid #ddd;">Foto</th>
                    <th style="padding: 15px; border: 1px solid #ddd;">Názov vozidla</th>
                    <th style="padding: 15px; border: 1px solid #ddd;">Cena</th>
                    <th style="padding: 15px; border: 1px solid #ddd;">Akcie</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr style="border-bottom: 1px solid #eee; text-align: center;">
                    <td style="padding: 10px;">
                        <!-- PRÓBÁLD KI: Ha nem jelenik meg a kép, töröld ki a ../../ részt az elejéről -->
                        <img src="../../<?php echo $row['obraz']; ?>" width="120" style="border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
                    </td>
                    <td style="padding: 10px; font-weight: bold;">
                        <?php echo htmlspecialchars($row['Nazov']); ?>
                    </td>
                    <td style="padding: 10px; color: #e44d26; font-weight: bold;">
                        <?php echo number_format($row['cena'], 0, ',', ' '); ?> €
                    </td>
                    <td style="padding: 10px;">
                        <a href="edit_car.php?id=<?php echo $row['id']; ?>" 
                           style="background: orange; color: white; text-decoration: none; padding: 8px 15px; border-radius: 5px; margin-right: 5px; display: inline-block;">
                           Upraviť
                        </a>
                        <a href="delete_car.php?id=<?php echo $row['id']; ?>" 
                           style="background: red; color: white; text-decoration: none; padding: 8px 15px; border-radius: 5px; display: inline-block;" 
                           onclick="return confirm('Naozaj chcete zmazať tento inzerát?')">
                           Zmazať
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div style="text-align: center; padding: 50px;">
            <p style="font-size: 1.2em;">Zatiaľ nemáte žiadne aktívne inzeráty.</p>
            <a href="add_car.php" style="color: #2ecc71; font-weight: bold;">Kliknite sem pre pridanie prvého auta!</a>
        </div>
    <?php endif; ?>
</div>

<style>
    /* Egy kis extra dizájn a táblázathoz */
    tr:hover { background-color: #f9f9f9; }
    body { background-color: #333 !important; } /* Sötét maradjon az oldal többi része */
</style>

</body>
</html>
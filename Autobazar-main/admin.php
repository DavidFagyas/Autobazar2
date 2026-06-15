<?php
session_start();
require_once 'config/Database.php';
require_once 'models/Inzerat.php';
require_once 'models/User.php';
require_once 'models/Sprava.php'; // 1. Beemeljük az üzenet modellt

// Ellenőrizzük, hogy az admin-e
if (!isset($_SESSION['user_id']) || (isset($_SESSION['role']) && $_SESSION['role'] !== 'admin' && (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1))) {
    die("<p style='color:red; text-align:center; margin-top:50px;'>Nemáte oprávnenie na zobrazenie tejto stránky.</p>");
}

// OOP Objektumok példányosítása és az adatok kinyerése
$inzeratManager = new Inzerat($conn);
$vsetkyInzeraty = $inzeratManager->getAllInzeratiWithUsers();

$userManager = new User($conn);
$vsetciUzivatelia = $userManager->getAllUsersExceptAdmin();

$spravaManager = new Sprava($conn); // 2. Példányosítjuk az üzenetkezelőt
$vsetkySpravy = $spravaManager->getAllSpravy(); // 3. Lekérjük az összes üzenetet

// A fejléc behívása
include_once 'views/layout/header.php';
?>

<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <style>
        /* Egy kis alap formázás a gomboknak, hogy jól nézzenek ki */
        .btn-edit {
            background: #007bff; 
            color: white; 
            padding: 5px 10px; 
            text-decoration: none; 
            border-radius: 3px; 
            margin-right: 5px; 
            display: inline-block;
        }
        .btn-edit:hover { background: #0056b3; }
        .btn-delete {
            background: #dc3545; 
            color: white; 
            padding: 5px 10px; 
            text-decoration: none; 
            border-radius: 3px; 
            display: inline-block;
        }
        .btn-delete:hover { background: #bd2130; }
        table td { padding: 8px; vertical-align: middle; }
        
        /* Új stílus az üzenet szövegének tördeléséhez */
        .msg-text {
            white-space: pre-line;
            max-width: 400px;
            word-wrap: break-word;
        }
    </style>
</head>
<body>

<div class="admin-container">
    
    <h2>Správa inzerátov (Všetky autá)</h2>
    <table>
        <thead>
            <tr>
                <th>Foto</th>
                <th>Názov / Model</th>
                <th>Cena</th>
                <th>Predajca (User)</th>
                <th>Akcie</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($vsetkyInzeraty)): ?>
                <?php foreach ($vsetkyInzeraty as $row): ?>
                    <tr>
                        <td><img src="<?php echo $row['obraz']; ?>" width="80"></td>
                        <td><strong><?php echo htmlspecialchars($row['Nazov']); ?></strong></td>
                        <td><?php echo number_format($row['cena'], 2); ?> €</td>
                        <td><?php echo htmlspecialchars($row['username'] ?? 'Neznámy'); ?></td>
                        <td>
                            <a href="views/cars/admin_edit_car.php?id=<?php echo $row['id']; ?>" class="btn-edit">Upraviť</a>
                            
                            <a href="views/cars/delete_car.php?id=<?php echo $row['id']; ?>&from=admin" class="btn-delete" onclick="return confirm('Naozaj vymazať inzerát?')">Vymazať</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5">V systéme nie sú žiadne inzeráty.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <h2>Registrovaní používatelia</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Používateľské meno</th>
                <th>Rola</th>
                <th>Akcie</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($vsetciUzivatelia)): ?>
                <?php foreach ($vsetciUzivatelia as $u): ?>
                    <tr>
                        <td><?php echo $u['id']; ?></td>
                        <td><?php echo htmlspecialchars($u['username']); ?></td>
                        <td><?php echo $u['is_admin'] ? 'Admin' : 'Používateľ'; ?></td>
                        <td>
                            <a href="views/cars/admin_edit_user.php?id=<?php echo $u['id']; ?>" class="btn-edit">Upraviť</a>
                            
                            <a href="views/cars/admin_delete_user.php?id=<?php echo $u['id']; ?>" class="btn-delete" onclick="return confirm('Vymazať používateľa aj s jeho inzerátmi?')">Odstrániť</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="4">Žiadni iní registrovaní používatelia.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <h2>Doručené správy (Kontakt)</h2>
    <table>
        <thead>
            <tr>
                <th>Meno</th>
                <th>E-mail</th>
                <th>Správa</th>
                <th>Dátum</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($vsetkySpravy)): ?>
                <?php foreach ($vsetkySpravy as $sprava): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($sprava['meno']); ?></strong></td>
                        <td>
                            <a href="mailto:<?php echo htmlspecialchars($sprava['email']); ?>" style="color: #007bff; text-decoration: none;">
                                <?php echo htmlspecialchars($sprava['email']); ?>
                            </a>
                        </td>
                        <td class="msg-text"><?php echo htmlspecialchars($sprava['sprava']); ?></td>
                        <td><small><?php echo date('d.m.Y H:i', strtotime($sprava['datum_odoslania'])); ?></small></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="4">Nenašli se žiadne doručené správy.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

</div>

</body>
</html>
<?php
session_start();

// 1. JAVÍTOTT ELÉRÉSI UTAK (Visszalépünk a főmappába)
require_once '../../config/Database.php';
require_once '../../models/User.php';

// Fejléc behívása (Egy mappát fel, majd be a layout-ba)
include_once '../layout/header.php';

// Biztonság: Csak admin léphet be
if (!isset($_SESSION['user_id']) || (isset($_SESSION['role']) && $_SESSION['role'] !== 'admin' && (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1))) {
    die("<p style='color:red; text-align:center; margin-top:50px;'>Nemáte oprávnenie na zobrazenie tejto stránky.</p>");
}

$userManager = new User($conn);
$message = "";
$error = "";

// 2. ID ellenőrzése
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("<div style='color:white; padding:20px;'>Chyba: ID používateľa nebolo zadané!</div>");
}

$userId = intval($_GET['id']);

// Lekérjük a felhasználó adatait
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    die("<div style='color:white; padding:20px;'>Chyba: Používateľ neexistuje!</div>");
}

// 3. Módosítás mentése
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_user'])) {
    $username = trim($_POST['username']);
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;

    if (empty($username)) {
        $error = "Používateľské meno nemôže byť prázdne!";
    } else {
        $updateStmt = $conn->prepare("UPDATE users SET username = ?, is_admin = ? WHERE id = ?");
        $updateStmt->bind_param("sii", $username, $is_admin, $userId);
        
        if ($updateStmt->execute()) {
            $message = "Používateľ bol úspešne upravený!";
            $user['username'] = $username;
            $user['is_admin'] = $is_admin;
        } else {
            $error = "Chyba pri aktualizácii v databáze!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <title>Upraviť používateľa</title>
    <style>
        body { background-color: #333 !important; color: #fff !important; font-family: Arial, sans-serif; }
        .edit-container { max-width: 500px; margin: 40px auto; background: #222; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.5); }
        h2 { color: #007bff; border-bottom: 2px solid #444; padding-bottom: 10px; margin-top: 0; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"] { width: 100%; padding: 10px; border: 1px solid #444; background: #2a2a2a; color: #fff; border-radius: 4px; box-sizing: border-box; outline: none; }
        .checkbox-group { display: flex; align-items: center; gap: 10px; }
        .checkbox-group input { width: 20px; height: 20px; cursor: pointer; }
        .btn-submit { background: #007bff; color: white; border: none; padding: 12px 20px; border-radius: 4px; cursor: pointer; font-weight: bold; width: 100%; font-size: 16px; }
        .btn-submit:hover { background: #0056b3; }
        .btn-back { display: inline-block; margin-top: 15px; color: #ccc; text-decoration: none; }
        .btn-back:hover { color: #fff; }
        .alert-success { background: #28a745; color: white; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        .alert-danger { background: #dc3545; color: white; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
    </style>
</head>
<body>

<div class="edit-container">
    <h2><i class="fas fa-user-edit"></i> Upraviť používateľa (ID: <?php echo $userId; ?>)</h2>

    <?php if (!empty($message)): ?>
        <div class="alert-success"><?php echo $message; ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="form-group">
            <label>Používateľské meno:</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>">
        </div>

        <div class="form-group checkbox-group">
            <input type="checkbox" name="is_admin" id="is_admin" <?php echo $user['is_admin'] ? 'checked' : ''; ?>>
            <label style="display:inline; cursor:pointer;" for="is_admin">Uplatniť ako Administrátor (Admin rola)</label>
        </div>

        <button type="submit" name="update_user" class="btn-submit">Uložiť zmeny</button>
    </form>

    <a href="../../admin.php" class="btn-back"><i class="fas fa-arrow-left"></i> Späť na Admin Panel</a>
</div>

</body>
</html>
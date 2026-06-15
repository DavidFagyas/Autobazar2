<?php
session_start();

require_once '../../config/Database.php'; 
require_once '../../models/Inzerat.php'; 

include_once '../layout/header.php'; 

if (!isset($_SESSION['user_id']) || (isset($_SESSION['role']) && $_SESSION['role'] !== 'admin' && (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1))) {
    die("<p style='color:red; text-align:center; margin-top:50px;'>Nemáte oprávnenie na zobrazenie tejto stránky.</p>");
}

$inzeratManager = new Inzerat($conn);
$message = "";
$error = "";

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("<div style='color:white; padding:20px;'>Chyba: ID vozidla nebolo zadané!</div>");
}

$carId = intval($_GET['id']);
$car = $inzeratManager->getInzeratById($carId); 

if (!$car) {
    die("<div style='color:white; padding:20px;'>Chyba: Vozidlo s týmto ID neexistuje!</div>");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_car'])) {
    $nazov = trim($_POST['nazov']);
    $cena = floatval($_POST['cena']);
    $popis = trim($_POST['popis']);
    $obrazok = trim($_POST['obrazok']);
    
    $palivo = $_POST['palivo'];
    $prevodovka = $_POST['prevodovka'];
    $km = intval($_POST['km']);
    $pohon = $_POST['pohon'];
    $tel_cislo = trim($_POST['tel_cislo']); // Új mező fogadása

    if (empty($nazov) || empty($cena)) {
        $error = "Názov a cena musia byť vyplnené!";
    } else {
        // SQL frissítés kiegészítve a tel_cislo-val
        $sql_update = "UPDATE inzerati SET Nazov = ?, cena = ?, popis = ?, obraz = ?, Palivo = ?, Prevodovka = ?, KM = ?, Pohon = ?, tel_cislo = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("sdssssisss", $nazov, $cena, $popis, $obrazok, $palivo, $prevodovka, $km, $pohon, $tel_cislo, $carId);
        
        if ($stmt_update->execute()) {
            $message = "Vozidlo bolo úspešne upravené!";
            $car = $inzeratManager->getInzeratById($carId);
        } else {
            $error = "Chyba pri aktualizácii v databáze: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <title>Upraviť vozidlo</title>
    <style>
        body { background-color: #333 !important; color: #fff !important; font-family: Arial, sans-serif; }
        .edit-container { max-width: 700px; margin: 40px auto; background: #222; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.5); }
        h2 { color: #007bff; border-bottom: 2px solid #444; padding-bottom: 10px; margin-top: 0; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="number"], select, textarea { width: 100%; padding: 10px; border: 1px solid #444; background: #2a2a2a; color: #fff; border-radius: 4px; box-sizing: border-box; outline: none; }
        textarea { height: 120px; resize: vertical; }
        .form-row { display: flex; gap: 15px; margin-bottom: 15px; }
        .form-row .form-group { flex: 1; margin-bottom: 0; }
        .btn-submit { background: #007bff; color: white; border: none; padding: 12px 20px; border-radius: 4px; cursor: pointer; font-weight: bold; width: 100%; font-size: 16px; margin-top: 10px; }
        .btn-submit:hover { background: #0056b3; }
        .btn-back { display: inline-block; margin-top: 15px; color: #ccc; text-decoration: none; }
        .btn-back:hover { color: #fff; }
        .alert-success { background: #28a745; color: white; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        .alert-danger { background: #dc3545; color: white; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
    </style>
</head>
<body>

<div class="edit-container">
    <h2><i class="fas fa-edit"></i> Upraviť vozidlo (ID: <?php echo $carId; ?>)</h2>

    <?php if (!empty($message)): ?>
        <div class="alert-success"><?php echo $message; ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        
        <div class="form-row">
            <div class="form-group">
                <label>Názov vozidla (Značka, Model, Rok):</label>
                <input type="text" name="nazov" value="<?php echo htmlspecialchars($car['Nazov'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label>Cena (€):</label>
                <input type="number" step="0.01" name="cena" value="<?php echo htmlspecialchars($car['cena'] ?? ''); ?>" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Palivo:</label>
                <select name="palivo" required>
                    <option value="Benzín" <?php echo (isset($car['Palivo']) && $car['Palivo'] == 'Benzín') ? 'selected' : ''; ?>>Benzín</option>
                    <option value="Diesel" <?php echo (isset($car['Palivo']) && $car['Palivo'] == 'Diesel') ? 'selected' : ''; ?>>Diesel</option>
                    <option value="LPG/CNG" <?php echo (isset($car['Palivo']) && $car['Palivo'] == 'LPG/CNG') ? 'selected' : ''; ?>>LPG/CNG</option>
                    <option value="Hybrid" <?php echo (isset($car['Palivo']) && $car['Palivo'] == 'Hybrid') ? 'selected' : ''; ?>>Hybrid</option>
                    <option value="Elektro" <?php echo (isset($car['Palivo']) && $car['Palivo'] == 'Elektro') ? 'selected' : ''; ?>>Elektromobil</option>
                </select>
            </div>
            <div class="form-group">
                <label>Prevodovka:</label>
                <select name="prevodovka" required>
                    <option value="Manuál (5st.)" <?php echo (isset($car['Prevodovka']) && $car['Prevodovka'] == 'Manuál (5st.)') ? 'selected' : ''; ?>>Manuál (5st.)</option>
                    <option value="Manuál (6st.)" <?php echo (isset($car['Prevodovka']) && $car['Prevodovka'] == 'Manuál (6st.)') ? 'selected' : ''; ?>>Manuál (6st.)</option>
                    <option value="Automat" <?php echo (isset($car['Prevodovka']) && $car['Prevodovka'] == 'Automat') ? 'selected' : ''; ?>>Automat</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Najazdené km:</label>
                <input type="number" name="km" value="<?php echo htmlspecialchars($car['KM'] ?? '0'); ?>" required>
            </div>
            <div class="form-group">
                <label>Pohon:</label>
                <select name="pohon" required>
                    <option value="Predný" <?php echo (isset($car['Pohon']) && $car['Pohon'] == 'Predný') ? 'selected' : ''; ?>>Predný</option>
                    <option value="Zadný" <?php echo (isset($car['Pohon']) && $car['Pohon'] == 'Zadný') ? 'selected' : ''; ?>>Zadný</option>
                    <option value="4x4" <?php echo (isset($car['Pohon']) && $car['Pohon'] == '4x4') ? 'selected' : ''; ?>>Pohon 4x4</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label style="color: #007bff;">Telefónny kontakt:</label>
            <input type="text" name="tel_cislo" value="<?php echo htmlspecialchars($car['tel_cislo'] ?? ''); ?>" placeholder="napr. +421..." required>
        </div>

        <div class="form-group">
            <label>Cesta k obrázku (URL / priečinok):</label>
            <input type="text" name="obrazok" value="<?php echo htmlspecialchars($car['obraz'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label>Popis vozidla:</label>
            <textarea name="popis" required><?php echo htmlspecialchars($car['popis'] ?? $car['Popis'] ?? ''); ?></textarea>
        </div>

        <button type="submit" name="update_car" class="btn-submit">Uložiť zmeny</button>
    </form>

    <a href="../../admin.php" class="btn-back"><i class="fas fa-arrow-left"></i> Späť na hlavnú stránku</a>
</div>

</body>
</html>
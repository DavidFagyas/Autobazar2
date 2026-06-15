<?php
session_start();

// 1. Biztonsági ellenőrzés: Be van-e lépve?
if (!isset($_SESSION['user_id'])) {
    die("Prosím, prihláste sa!");
}

if (!isset($_GET['id'])) {
    header("Location: moje-inzeraty.php");
    exit();
}

// 2. Központi adatbázis és a Modell behívása
require_once '../../config/Database.php'; 
require_once '../../models/Inzerat.php'; 

$id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// 3. OOP Objektum létrehozása és adatlekérés
$inzeratManager = new Inzerat($conn);
$row = $inzeratManager->getUserInzeratById($id, $user_id);

// Ha az autó nem létezik, vagy nem a belépett felhasználóé
if (!$row) {
    die("Inzerát sa nenašiel alebo nemáte oprávnenie na jeho úpravu.");
}

// 4. Frissítés (UPDATE) kezelése POST kérés esetén
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nazov = $_POST['nazov'];
    $cena = $_POST['cena'];
    $popis = $_POST['popis'];
    $palivo = $_POST['palivo'];
    $prevodovka = $_POST['prevodovka'];
    $km = intval($_POST['km']);
    $pohon = $_POST['pohon'];

    // Meghívjuk a modellünk frissítő funkcióját
    if ($inzeratManager->updateDetailedInzerat($id, $user_id, $nazov, $cena, $popis, $palivo, $prevodovka, $km, $pohon)) {
        $message = "<p style='color: green;'>Inzerát bol úspešne aktualizovaný!</p>";
        header("Refresh: 2; url=moje-inzeraty.php");
    } else {
        $message = "<p style='color: red;'>Chyba pri ukladaní zmien.</p>";
    }
}

// Fejléc behívása
include_once '../layout/header.php'; 
?>

<div style="max-width: 600px; margin: 40px auto; padding: 20px; background: #fff; border-radius: 10px; color: #333;">
    <h2>Upraviť inzerát</h2>
    <?php echo $message; ?>

    <form method="post" style="display: flex; flex-direction: column; gap: 10px;">
        <label>Názov vozidla:</label>
        <input type="text" name="nazov" value="<?php echo htmlspecialchars($row['Nazov']); ?>" required>

        <label>Cena (€):</label>
        <input type="number" name="cena" value="<?php echo $row['cena']; ?>" required>

        <label>Palivo:</label>
        <select name="palivo">
            <option value="Benzín" <?php if($row['Palivo'] == 'Benzín') echo 'selected'; ?>>Benzín</option>
            <option value="Diesel" <?php if($row['Palivo'] == 'Diesel') echo 'selected'; ?>>Diesel</option>
            <option value="Hybrid" <?php if($row['Palivo'] == 'Hybrid') echo 'selected'; ?>>Hybrid</option>
            <option value="Elektro" <?php if($row['Palivo'] == 'Elektro') echo 'selected'; ?>>Elektromobil</option>
        </select>

        <label>Prevodovka:</label>
        <select name="prevodovka">
            <option value="Manuál" <?php if($row['Prevodovka'] == 'Manuál') echo 'selected'; ?>>Manuál</option>
            <option value="Automat" <?php if($row['Prevodovka'] == 'Automat') echo 'selected'; ?>>Automat</option>
        </select>

        <label>Najazdené km:</label>
        <input type="number" name="km" value="<?php echo $row['KM']; ?>">

        <label>Pohon:</label>
        <input type="text" name="pohon" value="<?php echo htmlspecialchars($row['Pohon']); ?>">

        <label>Popis:</label>
        <textarea name="popis" rows="5"><?php echo htmlspecialchars($row['popis']); ?></textarea>

        <input type="submit" value="Uložiť zmeny" style="background: #333; color: white; padding: 10px; cursor: pointer; border: none; border-radius: 5px;">
        <a href="moje-inzeraty.php" style="text-align: center; text-decoration: none; color: #666;">Späť</a>
    </form>
</div>

<style>
    input, select, textarea { padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
    body { background-color: #333 !important; }
</style>
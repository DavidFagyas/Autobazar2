<?php
session_start();

/**
 * 1. BIZTONSÁGI ELLENŐRZÉS
 */
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

/**
 * 2. ADATBÁZIS KAPCSOLAT
 */
require '../../config/Database.php';

$error = "";

/**
 * 3. ŰRLAP FELDOLGOZÁSA (POST)
 */
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Alapadatok
    $make = trim($_POST['make']);
    $model = trim($_POST['model']);
    $year = intval($_POST['year']);
    $price = floatval($_POST['price']);
    
    // ÚJ MEZŐK
    $palivo = $_POST['palivo'];
    $prevodovka = $_POST['prevodovka'];
    $km = intval($_POST['km']);
    $pohon = $_POST['pohon'];
    $tel_cislo = trim($_POST['tel_cislo']); // Új telefonszám mező
    $popis_vlastny = trim($_POST['popis_vlastny']); 
    
    $user_id = $_SESSION['user_id'];
    
    // Név generálása
    $auto_neve = $make . " " . $model . " (" . $year . ")";
    
    // Fájlkezelés
    $target_dir = "../../assets/Auta/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // FŐKÉP FELTÖLTÉSE
    $main_image_name = time() . "_main_" . basename($_FILES["image"]["name"]);
    $main_target_file = $target_dir . $main_image_name;
    $db_main_path = "assets/Auta/" . $main_image_name;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $main_target_file)) {
        
        // JAVÍTOTT SQL: Belekerült a tel_cislo oszlop is!
        $sql = "INSERT INTO inzerati (Nazov, popis, cena, obraz, pouzivatel_id, Palivo, Prevodovka, KM, Pohon, tel_cislo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        // "ssdssissss" -> 10 darab paraméter (hozzáadva az utolsó 's' a telefonszámnak)
        $stmt->bind_param("ssdssissss", $auto_neve, $popis_vlastny, $price, $db_main_path, $user_id, $palivo, $prevodovka, $km, $pohon, $tel_cislo);

        if ($stmt->execute()) {
            $last_id = $conn->insert_id;

            // GALÉRIA KÉPEK FELTÖLTÉSE
            if (!empty($_FILES['gallery']['name'][0])) {
                foreach ($_FILES['gallery']['tmp_name'] as $key => $tmp_name) {
                    if ($_FILES['gallery']['error'][$key] == 0) {
                        $gen_name = time() . "_gal_" . $_FILES['gallery']['name'][$key];
                        $gen_target = $target_dir . $gen_name;
                        $db_gal_path = "assets/Auta/" . $gen_name;

                        if (move_uploaded_file($tmp_name, $gen_target)) {
                            $stmt_gal = $conn->prepare("INSERT INTO galeria (inzerat_id, cesta_k_obrazku) VALUES (?, ?)");
                            $stmt_gal->bind_param("is", $last_id, $db_gal_path);
                            $stmt_gal->execute();
                        }
                    }
                }
            }

            header("Location: ../../index.php");
            exit();
        } else {
            $error = "Chyba pri zápise do databázy: " . $conn->error;
        }
    } else {
        $error = "Nepodarilo sa nahrať hlavný obrázok!";
    }
}
?>

<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/Style1.css">
    <title>Pridať inzerát - Autobazár</title>
    <style>
        .form-group { margin-bottom: 15px; }
        .form-group label { font-weight: bold; display: block; margin-bottom: 5px; }
        .form-group input, .form-group select, .form-group textarea { 
            width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box;
        }
        .form-row { display: flex; gap: 15px; margin-bottom: 15px; }
        .form-row .form-group { flex: 1; margin-bottom: 0; }
    </style>
</head>

<body style="background-color: #333;"> 
<header style="text-align: center; color: white; padding: 20px;">
    <a href="../../index.php">
        <img src="../../assets/logo.jpg" height="75px" width="105px">
    </a>
    <h1>Pridať nový inzerát</h1>
</header>

<section style="padding: 30px; max-width: 700px; margin: 20px auto; background: #fff; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.3);">
    
    <?php if(!empty($error)): ?>
        <p style="background: #ffcccc; color: #cc0000; padding: 10px; border-radius: 5px;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form action="add_car.php" method="post" enctype="multipart/form-data">
        
        <div class="form-row">
            <div class="form-group">
                <label>Značka:</label>
                <input type="text" name="make" placeholder="napr. BMW" required>
            </div>
            <div class="form-group">
                <label>Model:</label>
                <input type="text" name="model" placeholder="napr. M4" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Rok výroby:</label>
                <input type="number" name="year" min="1900" max="2026" required>
            </div>
            <div class="form-group">
                <label>Cena (€):</label>
                <input type="number" name="price" step="0.01" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Palivo:</label>
                <select name="palivo" required>
                    <option value="Benzín">Benzín</option>
                    <option value="Diesel">Diesel</option>
                    <option value="LPG/CNG">LPG/CNG</option>
                    <option value="Hybrid">Hybrid</option>
                    <option value="Elektro">Elektromobil</option>
                </select>
            </div>
            <div class="form-group">
                <label>Prevodovka:</label>
                <select name="prevodovka" required>
                    <option value="Manuál (5st.)">Manuál (5st.)</option>
                    <option value="Manuál (6st.)">Manuál (6st.)</option>
                    <option value="Automat">Automat</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Najazdené km:</label>
                <input type="number" name="km" placeholder="napr. 155000" required>
            </div>
            <div class="form-group">
                <label>Pohon:</label>
                <select name="pohon" required>
                    <option value="Predný">Predný</option>
                    <option value="Zadný">Zadný</option>
                    <option value="4x4">Pohon 4x4</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label style="color: #d9534f;">Telefónny kontakt (Elérhetőség):</label>
            <input type="text" name="tel_cislo" placeholder="napr. +421 905 123 456" required>
        </div>

        <div class="form-group">
            <label>Popis inzerátu (vlastný text):</label>
            <textarea name="popis_vlastny" rows="6" placeholder="Sem napíšte podrobnosti o stave auta, výbave..." required></textarea>
        </div>

        <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">

        <div class="form-group">
            <label style="color: #4CAF50;">Hlavná titulná fotka:</label>
            <input type="file" name="image" accept="image/*" required>
        </div>

        <div class="form-group">
            <label style="color: #2196F3;">Ďalšie fotky do galérie:</label>
            <input type="file" name="gallery[]" accept="image/*" multiple>
            <small style="color: #888;">Podržte Ctrl pre výber viacerých fotiek.</small>
        </div>

        <input type="submit" value="Uložiť a zverejniť inzerát" class="footerbtn" style="width:100%; cursor:pointer; padding: 15px; font-size: 1.1em; background-color: #333; color: white; border: none; border-radius: 5px;">
    </form>
</section>

</body>
</html>
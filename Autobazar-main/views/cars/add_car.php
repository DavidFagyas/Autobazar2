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
    $make = trim($_POST['make']);
    $model = trim($_POST['model']);
    $year = intval($_POST['year']);
    $price = floatval($_POST['price']);
    
    $auto_neve = $make . " " . $model . " (" . $year . ")";
    $leiras = "Model: " . $model . ", Rok: " . $year;

    // 1. FŐKÉP KEZELÉSE (Ez megy az 'inzerati' táblába)
    $target_dir = "../../assets/Auta/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $main_image_name = time() . "_main_" . basename($_FILES["image"]["name"]);
    $main_target_file = $target_dir . $main_image_name;
    $db_main_path = "assets/Auta/" . $main_image_name;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $main_target_file)) {
        
        // BESZÚRÁS AZ INZERATI TÁBLÁBA
        $stmt = $conn->prepare("INSERT INTO inzerati (Nazov, popis, cena, obraz) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssds", $auto_neve, $leiras, $price, $db_main_path);

        if ($stmt->execute()) {
            $last_id = $conn->insert_id; // Megkapjuk az új autó ID-ját

            // 2. TÖBBI KÉP KEZELÉSE (Ha vannak feltöltve extra képek)
            if (!empty($_FILES['gallery']['name'][0])) {
                foreach ($_FILES['gallery']['tmp_name'] as $key => $tmp_name) {
                    if ($_FILES['gallery']['error'][$key] == 0) {
                        $gen_name = time() . "_gal_" . $_FILES['gallery']['name'][$key];
                        $gen_target = $target_dir . $gen_name;
                        $db_gal_path = "assets/Auta/" . $gen_name;

                        if (move_uploaded_file($tmp_name, $gen_target)) {
                            // Beszúrás a galéria táblába
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
    <title>Pridať inzerát</title>
</head>

<body>
<header>
    <a href="../../index.php">
        <img src="../../assets/logo.jpg" height="75px" width="105px">
    </a>
    <h1>Pridať nový inzerát</h1>
</header>

<section style="padding: 40px; max-width: 500px; margin: auto; background: #fff; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
    <?php if(!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>

    <form action="add_car.php" method="post" enctype="multipart/form-data">
        <label>Značka:</label><br>
        <input type="text" name="make" required style="width:100%;"><br><br>

        <label>Model:</label><br>
        <input type="text" name="model" required style="width:100%;"><br><br>

        <label>Rok výroby:</label><br>
        <input type="number" name="year" required style="width:100%;"><br><br>

        <label>Cena (€):</label><br>
        <input type="number" name="price" step="0.01" required style="width:100%;"><br><br>

        <label><strong>Hlavná fotka (titulná):</strong></label><br>
        <input type="file" name="image" accept="image/*" required><br><br>

        <label><strong>Ostatné fotky do galérie (viacero):</strong></label><br>
        <input type="file" name="gallery[]" accept="image/*" multiple><br>
        <small style="color: gray;">Tu môžete označiť naraz viac fotiek (Ctrl + klik)</small><br><br>

        <input type="submit" value="Uložiť inzerát" class="footerbtn" style="width:100%; cursor:pointer;">
    </form>
</section>
</body>
</html>
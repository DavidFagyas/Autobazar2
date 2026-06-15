<?php
session_start();
require_once 'config/Database.php'; 
require_once 'models/Sprava.php'; // 1. Beemeljük az új üzenet modellt

$success_msg = "";
$error_msg = "";

// 2. OOP Példányosítás
$spravaManager = new Sprava($conn);

// ŰRLAP FELDOLGOZÁSA (Ha megnyomták az elküldés gombot)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['send_message'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);

    if (!empty($name) && !empty($email) && !empty($message)) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            
            // 3. MEGHÍVJUK AZ OBJEKTUM METÓDUST (Nincs SQL kód a nézetben!)
            if ($spravaManager->ulozitSpravu($name, $email, $message)) {
                $success_msg = "Vaša správa bola úspešne odoslaná! Ďakujeme.";
            } else {
                $error_msg = "Chyba pri ukladaní správy do databázy.";
            }

        } else {
            $error_msg = "Zadali ste neplatný formát e-mailu!";
        }
    } else {
        $error_msg = "Prosím, vyplňte všetky povinné polia!";
    }
}

include_once 'views/layout/header.php';
?>

<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/Style1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <title>Autobazár David - Kontakt</title>
    <style>
        /* Kis extra stílus az értesítéseknek, hogy szépen mutassanak */
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
        }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-danger { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>

<section id="kontakt">
    <h4>Napíšte nám</h4>

    <?php if (!empty($success_msg)): ?>
        <div class="alert alert-success"><?php echo $success_msg; ?></div>
    <?php endif; ?>
    
    <?php if (!empty($error_msg)): ?>
        <div class="alert alert-danger"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <form action="" method="post">
        <label for="name">Meno:</label>
        <input type="text" id="name" name="name" required value="<?php echo isset($_POST['name']) && empty($success_msg) ? htmlspecialchars($_POST['name']) : ''; ?>">

        <label for="email">E-mail:</label>
        <input type="email" id="email" name="email" required value="<?php echo isset($_POST['email']) && empty($success_msg) ? htmlspecialchars($_POST['email']) : ''; ?>">

        <label for="message">Správa:</label>
        <textarea id="message" name="message" rows="4" required><?php echo isset($_POST['message']) && empty($success_msg) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
        
        <button type="submit" name="send_message" class="logininputbtn" style="width: 100%; cursor: pointer;">ODOSLAŤ</button>
    </form>
</section>



<div id="goToTop" class="hore">
    <i class="fas fa-arrow-up"></i>
</div>

<script>
    // A GoToTop funkció javítása
    document.getElementById('goToTop').addEventListener('click', function() {
        window.scroll({
            top: 0,
            left: 0,
            behavior: 'smooth'
        });
    });
</script>
<?php 
// 4. JAVÍTÁS: Az új közös lábléc behívása
include_once 'views/layout/footer.php'; 
?>
</body>
</html>
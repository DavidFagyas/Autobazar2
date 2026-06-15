<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// A projekt alapmappája
$base_url = "/Autobazar-main"; 
?>
<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/Style1.css">
    <title>Autobazár</title>
    <style>
        .header-container {
            background-color: #333;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #444;
        }
        
        .nav-links a { 
            margin: 0 15px; 
            text-decoration: none; 
            color: white; 
            font-weight: bold; 
            transition: 0.3s;
        }

        .nav-links a:hover { color: #cc0000; }

        .admin-link { color: #d35400 !important; }
        .logout-link { color: #c0392b !important; }
        .user-info { color: #ccc; font-size: 0.9em; margin-right: 10px; }
        
        .add-btn {
            color: #2ecc71 !important;
            border: 1px solid #2ecc71;
            padding: 5px 10px;
            border-radius: 5px;
        }
        .add-btn:hover {
            background-color: #2ecc71;
            color: white !important;
        }
    </style>
</head>
<body>

<header class="header-container">
    <div class="logo-section">
        <a href="<?php echo $base_url; ?>/index.php">
            <img src="<?php echo $base_url; ?>/assets/logo.jpg" height="75px" width="105px" alt="Logo">
        </a>
    </div>

    <nav class="nav-links">
        <a href="<?php echo $base_url; ?>/index.php">Domov</a>
        <a href="<?php echo $base_url; ?>/dostupne-auta.php">Dostupné autá</a>
        <a href="<?php echo $base_url; ?>/onas.php">O nás</a>
        <a href="<?php echo $base_url; ?>/kontakt.php">Kontakt</a>

        <?php if (isset($_SESSION['user_id'])): ?>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a href="<?php echo $base_url; ?>/admin.php" class="admin-link">ADMIN PANEL</a>
            <?php else: ?>
                <a href="<?php echo $base_url; ?>/views/cars/add_car.php" class="add-btn">+ Pridať inzerát</a>
                
                <a href="<?php echo $base_url; ?>/views/cars/moje-inzeraty.php">Moje inzeráty</a>
            <?php endif; ?>
            
            <span class="user-info">| Ahoj, <strong><?php echo htmlspecialchars($_SESSION['username'] ?? 'Používateľ'); ?></strong></span>
            <a href="<?php echo $base_url; ?>/views/auth/logout.php" class="logout-link">Odhlásiť sa</a>
        <?php else: ?>
            <a href="<?php echo $base_url; ?>/views/auth/login.php">Login</a>
            <a href="<?php echo $base_url; ?>/views/auth/register.php">Register</a>
        <?php endif; ?>
    </nav>
</header>
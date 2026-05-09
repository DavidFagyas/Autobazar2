<?php
session_start();
// Adatbázis kapcsolat - a főkönyvtárhoz képest
require 'config/Database.php'; 
?>
<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/Style1.css">
    <title>Dostupné autá</title>
    <style>
        .car-list {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            padding: 20px;
            justify-content: center;
        }
        .car-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            width: 300px;
            padding: 15px;
            text-align: center;
            background: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .car-card:hover {
            transform: scale(1.02);
        }
        .car-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 5px;
            cursor: pointer;
        }
        .price-tag {
            font-weight: bold;
            color: #4CAF50;
            font-size: 1.2em;
        }
        .car-link {
            text-decoration: none;
            color: inherit;
        }
        .detail-btn {
            display: inline-block;
            width: 100%;
            background-color: #333;
            color: white;
            padding: 10px 0;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px;
        }
        .detail-btn:hover {
            background-color: #555;
        }
    </style>
</head>

<body>
<header>
    <a href="index.php">
        <img src="assets/logo.jpg" height="75px" width="105px">
    </a>
    <h1>Dostupné autá</h1>
    <nav>
        <ul class="navbar-nav">
            <li><a class="nav-link" href="index.php">Domov</a></li>
            <li><a class="nav-link" href="dostupne-auta.php">Dostupné autá</a></li>
            <li><a class="nav-link" href="onas.php">O nás</a></li>
            <li><a class="nav-link" href="kontakt.php">KONTAKT</a></li>
        </ul>
    </nav>
</header>

<section>
    <h2 style="text-align:center; margin-top:20px;">Ponuka vozidiel</h2>

    <!-- Szűrő Form -->
    <div style="text-align:center; margin-bottom: 30px;">
        <form method="GET" action="dostupne-auta.php">
            <label for="price_order">Zoradiť podľa ceny:</label>
            <select name="price_order" id="price_order">
                <option value="asc" <?php if(isset($_GET['price_order']) && $_GET['price_order'] == 'asc') echo 'selected'; ?>>Vzostupne (Lacnejšie)</option>
                <option value="desc" <?php if(isset($_GET['price_order']) && $_GET['price_order'] == 'desc') echo 'selected'; ?>>Zostupne (Drahšie)</option>
            </select>
            <input type="submit" value="Filtrovať" class="footerbtn" style="padding: 5px 15px; cursor:pointer;">
        </form>
    </div>

    <div class="car-list">
        <?php
        $sql = "SELECT * FROM inzerati";
        $order = "asc";
        if(isset($_GET['price_order']) && $_GET['price_order'] == "desc") {
            $order = "desc";
        }
        $sql .= " ORDER BY cena $order";

        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                ?>
                <div class="car-card">
                    <!-- A képre kattintva is a részletekhez jutunk -->
                    <a href="car-details.php?id=<?php echo $row['id']; ?>">
                        <img src="<?php echo $row['obraz']; ?>" alt="Auto">
                    </a>
                    
                    <h3>
                        <a href="car-details.php?id=<?php echo $row['id']; ?>" class="car-link">
                            <?php echo htmlspecialchars($row['Nazov']); ?>
                        </a>
                    </h3>
                    
                    <p><em><?php echo htmlspecialchars($row['popis']); ?></em></p>
                    <p class="price-tag"><?php echo number_format($row['cena'], 2, ',', ' '); ?> €</p>
                    
                    <!-- Részletek gomb linkként -->
                    <a href="car-details.php?id=<?php echo $row['id']; ?>" class="detail-btn">Viac informácií</a>
                    
                    <p style="margin-top:10px;"><small>Pridané: <?php echo $row['datum']; ?></small></p>
                </div>
                <?php
            }
        } else {
            echo "<p style='text-align:center; width:100%;'>Momentálne nie sú k dispozícii žiadne inzeráty.</p>";
        }
        $conn->close();
        ?>
    </div>
</section>

<footer>
    <div class="col-25">
        <h3 class="footernadpis"> Tu sa nachádzame</h3>
        <iframe src="https://www.google.com/maps/embed?pb=..." width="650" height="280" style="border:0;" allowfullscreen="" loading="lazy"></iframe> 
    </div>
    <div class="footermenu2">
        <h3 class="footernadpis">SLEDUJTE NÁS!</h3>
        <div class="footerikony">
            <a href="#"><i class="fab fa-facebook-f"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>  
        </div> 
    </div>
</footer>

<div id="goToTop" class="hore" onclick="scrollToTop()">
    <i class="fas fa-arrow-up"></i>
</div>

<script>
function scrollToTop() {
    window.scroll({ top: 0, left: 0, behavior: 'smooth' });
}
</script>
</body>
</html>
<?php
session_start();
// Adatbázis kapcsolat - a főkönyvtárhoz képest
require 'config/Database.php'; 
include_once 'views/layout/header.php';
?>
<!DOCTYPE html>
<html lang="sk">

<body>


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


<div id="goToTop" class="hore" onclick="scrollToTop()">
    <i class="fas fa-arrow-up"></i>
</div>

<script>
function scrollToTop() {
    window.scroll({ top: 0, left: 0, behavior: 'smooth' });
}
</script>
<?php 
// 4. JAVÍTÁS: Az új közös lábléc behívása
include_once 'views/layout/footer.php'; 
?>
</body>
</html>
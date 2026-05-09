<?php
session_start();
require 'config/Database.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // 1. Lekérjük az autó adatait
    $stmt = $conn->prepare("SELECT * FROM inzerati WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $car = $result->fetch_assoc();

    if (!$car) {
        die("Inzerát nebol nájdený!");
    }

    // 2. Összegyűjtjük az összes képet egy tömbbe a JavaScript számára
    $all_images = [];
    $all_images[] = $car['obraz']; // A főkép az első

    $img_stmt = $conn->prepare("SELECT cesta_k_obrazku FROM galeria WHERE inzerat_id = ?");
    $img_stmt->bind_param("i", $id);
    $img_stmt->execute();
    $images_result = $img_stmt->get_result();

    while($img = $images_result->fetch_assoc()) {
        $all_images[] = $img['cesta_k_obrazku'];
    }
} else {
    die("Chýbajúce ID inzerátu!");
}
?>

<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="assets/Style1.css">
    <title><?php echo htmlspecialchars($car['Nazov']); ?> - Detail</title>
    <style>
        /* Slider tároló */
        .slider-container {
            position: relative;
            width: 100%;
            height: 450px;
            background: #f0f0f0;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 15px;
        }

        .slider-container img {
            width: 100%;
            height: 100%;
            object-fit: contain; /* Így nem torzul a kép, látszik az egész */
            background: #222;
        }

        /* Nyilak stílusa */
        .prev, .next {
            cursor: pointer;
            position: absolute;
            top: 50%;
            width: auto;
            padding: 16px;
            margin-top: -22px;
            color: white;
            font-weight: bold;
            font-size: 24px;
            transition: 0.3s;
            background: rgba(0, 0, 0, 0.4);
            border-radius: 5px;
            user-select: none;
            text-decoration: none;
        }

        .next { right: 10px; }
        .prev { left: 10px; }

        .prev:hover, .next:hover {
            background: rgba(0, 0, 0, 0.8);
        }

        /* Miniatűrök rácsa */
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 10px;
            margin-top: 15px;
        }

        .gallery-grid img {
            width: 100%;
            height: 70px;
            object-fit: cover;
            border-radius: 5px;
            cursor: pointer;
            opacity: 0.6;
            transition: 0.3s;
            border: 2px solid transparent;
        }

        .gallery-grid img:hover, .gallery-grid img.active {
            opacity: 1;
            border-color: #4CAF50;
        }
    </style>
</head>
<body>

<header>
    <a href="index.php"><img src="assets/logo.jpg" height="75px"></a>
    <h1>Detail inzerátu</h1>
    <nav><a href="dostupne-auta.php">Späť na ponuku</a></nav>
</header>

<div style="max-width: 800px; margin: 40px auto; padding: 20px; background: #fff; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.2);">
    
    <!-- SLIDER RÉSZ -->
    <div class="slider-container">
        <img src="<?php echo $all_images[0]; ?>" id="mainSliderImg">
        
        <?php if(count($all_images) > 1): ?>
            <a class="prev" onclick="moveSlide(-1)">&#10094;</a>
            <a class="next" onclick="moveSlide(1)">&#10095;</a>
        <?php endif; ?>
    </div>

    <!-- MINIATŰRÖK -->
    <div class="gallery-grid">
        <?php foreach($all_images as $index => $path): ?>
            <img src="<?php echo $path; ?>" 
                 class="thumb-img <?php echo $index === 0 ? 'active' : ''; ?>" 
                 onclick="setSlide(<?php echo $index; ?>)">
        <?php endforeach; ?>
    </div>
    
    <!-- ADATOK -->
    <h2 style="margin-top: 25px;"><?php echo htmlspecialchars($car['Nazov']); ?></h2>
    <hr>
    
    <p style="font-size: 1.5em; color: #4CAF50; font-weight: bold;">Cena: <?php echo number_format($car['cena'], 2, ',', ' '); ?> €</p>
    
    <div style="background: #f9f9f9; padding: 15px; border-radius: 5px; margin-top: 20px;">
        <h3>Popis a špecifikácia:</h3>
        <p><?php echo nl2br(htmlspecialchars($car['popis'])); ?></p>
    </div>
    
    <p style="margin-top: 20px; color: #777;">Pridané: <?php echo $car['datum']; ?></p>
    
    <div style="margin-top: 30px;">
        <a href="kontakt.php" class="footerbtn" style="padding: 10px 20px; text-decoration: none;">Mám záujem / Kontaktovať predajcu</a>
    </div>
</div>

<script>
    // Átadjuk a PHP tömböt a JavaScriptnek
    const images = <?php echo json_encode($all_images); ?>;
    let currentIndex = 0;
    const sliderImg = document.getElementById('mainSliderImg');
    const thumbs = document.getElementsByClassName('thumb-img');

    function updateDisplay() {
        // Kép frissítése
        sliderImg.src = images[currentIndex];
        
        // Aktív keret frissítése a miniatűrökön
        for (let i = 0; i < thumbs.length; i++) {
            thumbs[i].classList.remove('active');
        }
        thumbs[currentIndex].classList.add('active');
    }

    function moveSlide(step) {
        currentIndex += step;
        
        if (currentIndex >= images.length) {
            currentIndex = 0; // Vissza az elejére
        }
        if (currentIndex < 0) {
            currentIndex = images.length - 1; // Utolsóra ugrik
        }
        updateDisplay();
    }

    function setSlide(index) {
        currentIndex = index;
        updateDisplay();
    }
</script>

</body>
</html>
<?php
session_start();

// 1. ADATBÁZIS ÉS MODELL BEHÍVÁSA
require_once 'config/Database.php'; 
require_once 'models/Inzerat.php'; // Beemeljük az Inzerat osztályt
include_once 'views/layout/header.php';

if (!isset($_GET['id'])) {
    header("Location: dostupne-auta.php");
    exit();
}

$id = intval($_GET['id']);

// 2. OOP PÉLDÁNYOSÍTÁS
$inzeratManager = new Inzerat($conn);

// 3. ADATOK LEKÉRÉSE OBJEKTUMON KERESZTÜL (Nincs nyers SQL a nézetben!)
$car = $inzeratManager->getInzeratById($id);

if (!$car) {
    die("<div style='color:white; text-align:center; padding:50px;'>Inzerát sa nenašiel.</div>");
}

// 4. GALÉRIA LEKÉRÉSE A MODELLBŐL
$galeria_kepek = $inzeratManager->getGaleriaByInzeratId($id);

$kepek = [];
$kepek[] = $car['obraz']; // A fő kép az első
foreach ($galeria_kepek as $kep_ut) {
    $kepek[] = $kep_ut;
}
?>

<style>
    .detail-container {
        max-width: 1000px;
        margin: 30px auto;
        background: white;
        border-radius: 15px;
        overflow: hidden;
        display: flex;
        flex-wrap: wrap;
        box-shadow: 0 5px 20px rgba(0,0,0,0.5);
    }
    
    /* SLIDER STÍLUS */
    .detail-image {
        flex: 1;
        min-width: 400px;
        background: #fff;
        position: relative;
        height: 500px;
        overflow: hidden;
    }
    .slider-img-container {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }
    .slider-img-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s;
    }
    .slider-img-container img:hover {
        transform: scale(1.05);
    }
    .slider-arrow {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(0,0,0,0.5);
        color: white;
        padding: 15px;
        cursor: pointer;
        border: none;
        font-size: 24px;
        transition: 0.3s;
        z-index: 10;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .slider-arrow:hover { background: rgba(0,0,0,0.8); }
    .prev { left: 15px; }
    .next { right: 15px; }
    
    .image-counter {
        position: absolute;
        bottom: 15px;
        right: 15px;
        background: rgba(0,0,0,0.6);
        color: white;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 14px;
        z-index: 10;
    }

    /* LIGHTBOX (NAGYÍTÁS) STÍLUS */
    .lightbox {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.9);
        overflow: auto;
        align-items: center;
        justify-content: center;
    }
    .lightbox-content {
        max-width: 90%;
        max-height: 90%;
        object-fit: contain;
        border: 4px solid white;
        border-radius: 10px;
    }
    .lightbox-close {
        position: absolute;
        top: 20px;
        right: 35px;
        color: white;
        font-size: 40px;
        font-weight: bold;
        cursor: pointer;
    }
    .lightbox-close:hover { color: #bbb; }

    .detail-info {
        flex: 1;
        padding: 30px;
        min-width: 300px;
    }
    .price-big {
        font-size: 2em;
        color: #e44d26;
        font-weight: bold;
        margin-bottom: 20px;
    }
    .specs-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        margin: 20px 0;
        padding: 15px;
        background: #f9f9f9;
        border-radius: 10px;
    }
    .spec-item {
        font-size: 0.9em;
        color: #555;
    }
    .spec-item strong {
        display: block;
        color: #333;
        font-size: 1.1em;
    }
    .description-box {
        margin-top: 20px;
        line-height: 1.6;
        color: #444;
        border-top: 1px solid #eee;
        padding-top: 15px;
    }
    
    /* Elérhetőség doboz stílus */
    .contact-box {
        margin-top: 25px;
        padding: 15px;
        background: #eef5fc;
        border-left: 5px solid #007bff;
        border-radius: 4px;
        color: #333;
        margin-bottom: 15px;
    }
    .contact-box p { margin: 5px 0; font-size: 1em; }
</style>

<div style="background-color: #333; padding: 20px; min-height: 100vh;">
    <div class="detail-container">
        
        <div class="detail-image">
            <div class="slider-img-container" onclick="openLightbox()">
                <img id="mainSliderImg" src="<?php echo $kepek[0]; ?>" alt="Auto">
            </div>
            
            <?php if(count($kepek) > 1): ?>
                <button class="slider-arrow prev" onclick="event.stopPropagation(); changeImg(-1)">&#10094;</button>
                <button class="slider-arrow next" onclick="event.stopPropagation(); changeImg(1)">&#10095;</button>
                <div class="image-counter">
                    <span id="currentIdx">1</span> / <?php echo count($kepek); ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="detail-info">
            <h1 style="margin:0;"><?php echo htmlspecialchars($car['Nazov']); ?></h1>
            <div class="price-big"><?php echo number_format($car['cena'], 0, ',', ' '); ?> €</div>

            <h3>Technické údaje:</h3>
            <div class="specs-grid">
                <div class="spec-item">
                    <span>Palivo</span>
                    <strong><?php echo htmlspecialchars($car['Palivo'] ?? 'Nezadané'); ?></strong>
                </div>
                <div class="spec-item">
                    <span>Prevodovka</span>
                    <strong><?php echo htmlspecialchars($car['Prevodovka'] ?? 'Nezadané'); ?></strong>
                </div>
                <div class="spec-item">
                    <span>Najazdené km</span>
                    <strong><?php echo number_format($car['KM'] ?? 0, 0, ',', ' '); ?> km</strong>
                </div>
                <div class="spec-item">
                    <span>Pohon</span>
                    <strong><?php echo htmlspecialchars($car['Pohon'] ?? 'Nezadané'); ?></strong>
                </div>
            </div>

            <div class="description-box">
                <h4>Popis predajcu:</h4>
                <p><?php echo nl2br(htmlspecialchars($car['popis'])); ?></p>
            </div>

            <div class="contact-box">
                <h4 style="margin: 0 0 10px 0; color: #007bff;"><i class="fas fa-address-card"></i> Kontakt na predajcu</h4>
                <p><strong>Predajca (Meno):</strong> <?php echo htmlspecialchars($car['username'] ?? 'Neznámy predajca'); ?></p>
                <p><strong>Telefónne číslo:</strong> <span style="color: #333; font-weight: bold;"><?php echo htmlspecialchars($car['tel_cislo'] ?? 'Neuvedené'); ?></span></p>
            </div>

            <a href="dostupne-auta.php" style="display:block; text-align:center; color:#666; text-decoration:none; margin-top:15px;">← Späť na zoznam</a>
        </div>
    </div>
</div>

<div id="lightbox" class="lightbox" onclick="closeLightbox()">
    <span class="lightbox-close" onclick="closeLightbox()">&times;</span>
    <img id="lightboxImg" class="lightbox-content" src="" alt="Zväčšený obrázok">
</div>

<script>
    let currentImgIdx = 0;
    const carImages = <?php echo json_encode($kepek); ?>;

    function changeImg(direction) {
        currentImgIdx += direction;

        if (currentImgIdx >= carImages.length) {
            currentImgIdx = 0;
        }
        if (currentImgIdx < 0) {
            currentImgIdx = carImages.length - 1;
        }

        document.getElementById('mainSliderImg').src = carImages[currentImgIdx];
        document.getElementById('currentIdx').innerText = currentImgIdx + 1;
    }

    function openLightbox() {
        const lightbox = document.getElementById('lightbox');
        const lightboxImg = document.getElementById('lightboxImg');
        const currentImg = document.getElementById('mainSliderImg');
        
        lightboxImg.src = currentImg.src;
        lightbox.style.display = 'flex';
    }

    function closeLightbox() {
        document.getElementById('lightbox').style.display = 'none';
    }
</script>
<?php 
// 4. JAVÍTÁS: Az új közös lábléc behívása
include_once 'views/layout/footer.php'; 
?>

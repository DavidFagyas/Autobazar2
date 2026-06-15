<?php 
// 1. Kapcsolat és az új Hirdetés-kezelő osztály behívása
require_once 'config/Database.php'; 
require_once 'models/Inzerat.php'; 

// 2. OOP objektum példányosítása és az adatok lekérése
$inzeratManager = new Inzerat($conn);
$slides = $inzeratManager->getLatestSlides(3);

// 3. Header behívása
include_once 'views/layout/header.php'; 
?>

<style>
    /* Felülírjuk az összes lehetséges elemet, ami feleslegesen nyújtaná lefelé az oldalt */
    html, body, main, div, section, article {
        height: auto !important;
        min-height: auto !important;
        max-height: none !important;
    }

    body {
        background-color: #333 !important;
        color: #fff !important;
    }
    
    /* Cookie sáv stílusa */
    #cookies {
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 9999;
        width: 90%;
        max-width: 600px;
        background: #222;
        color: #fff;
        padding: 15px 25px;
        border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.5);
    }
    #cookies p { margin: 0; font-size: 14px; line-height: 1.5; color: #fff; }
    #cookies-btn {
        background: #007bff;
        color: white;
        border: none;
        padding: 6px 15px;
        margin-left: 15px;
        border-radius: 4px;
        cursor: pointer;
    }

    /* Szöveges rész: Szigorúan TILOS neki magasságot adni, és a margókat is lenullázzuk */
    .info-section, section {
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        max-width: 1000px;
        margin: 20px auto 0 auto !important;
        padding: 10px 20px !important;
        display: block !important;
    }

    .info-section p, section p {
        margin: 0 0 15px 0 !important;
        padding: 0 !important;
        line-height: 1.6;
    }

    /* Cím: Közvetlenül a szöveg alá tapad */
    .sekcia-nadpis {
        max-width: 1000px;
        margin: 20px auto 15px auto !important;
        padding: 0 20px;
        color: #fff !important;
        font-size: 24px;
        text-transform: uppercase;
        font-weight: bold;
        letter-spacing: 1px;
        display: block !important;
    }

    /* Slideshow tároló: Szorosan a cím alatt, és középre zárt */
    .slideshow-container {
        max-width: 1000px;
        position: relative;
        margin: 0 auto 40px auto !important;
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        display: block !important;
    }
    
    .mySlides {
        width: 100%;
        display: none;
        background: transparent !important;
        border: none !important;
    }

    .mySlides img {
        width: 100%;
        max-width: 1000px;
        height: 500px;
        object-fit: cover;
        border-radius: 8px;
        display: block;
    }

    .slide-caption {
        position: absolute;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0, 0, 0, 0.75);
        color: white;
        padding: 12px 25px;
        border-radius: 30px;
        font-size: 18px;
    }

    /* Footer fixek */
    footer {
        background: #1a1a1a !important;
        color: #ccc;
        padding: 40px 20px;
        display: flex;
        flex-wrap: wrap;
        justify-content: space-around;
        gap: 30px;
        border-top: 3px solid #222;
        margin-top: 40px;
    }
    footer iframe { border-radius: 6px; max-width: 100%; }
    .footernadpis { color: #fff; font-size: 18px; margin-bottom: 15px; text-transform: uppercase; }
    .footerikony a {
        color: #fff; background: #333; width: 40px; height: 40px;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: 50%; margin-right: 10px; text-decoration: none;
    }
    .footerinput { padding: 10px 15px; border: 1px solid #444; background: #2a2a2a; color: #fff; border-radius: 4px; width: 200px; }
    .footerbtn { padding: 10px 20px; background: #007bff; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; }
    .footer-bottom { background: #111; padding: 15px; text-align: center; color: #666; font-size: 14px; }
    .hore { position: fixed; bottom: 30px; right: 30px; background: #007bff; color: white; width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; border-radius: 50%; z-index: 999; }
</style>

<div id="cookies" style="display:none;">
    <p>Tento web používa na poskytovanie služieb a analýzu návštevnosti súbory cookie. 
        <a href="">Dozvedieť sa viac</a>
        <button id="cookies-btn">Rozumiem</button>
    </p>
</div>

<section class="info-section" style="height: auto !important; min-height: auto !important;">
    <p>Riadenie vzťahov so zákazníkmi: predajcovia automobilov musia rozvíjať úzke vzťahy so zákazníkmi. 
    Pomáhajú im vybrať si správne auto na základe ich potrieb a rozpočtu.</p>
    
    <p>Riadenie procesu predaja: predajcovia sú zodpovední za celkové riadenie procesu predaja. 
    To zahŕňa informovanie zákazníkov, prezentáciu automobilov, riadenie predajného procesu a vypracovanie predajných zmlúv.</p>
    
    <p>Analýza trhu: sledujú trendy na trhu s automobilmi a sú informovaní o aktuálnych cenách, modeloch a ďalších relevantných informáciách.</p>
</section>

<h2 class="sekcia-nadpis">Aktuálne ponuky</h2>

<div class="slideshow-container">
    <?php if (!empty($slides)): ?>
        <?php foreach ($slides as $slide): ?>
            <div class="mySlides" style="position: relative;">
                <a href="car-details.php?id=<?php echo $slide['id']; ?>">
                    <img src="<?php echo $slide['obraz']; ?>">
                </a>
                <div class="slide-caption">
                    <?php echo htmlspecialchars($slide['Nazov']); ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="mySlides"><img src="autoslideshow/car1_show.webp"></div>
        <div class="mySlides"><img src="autoslideshow/car2_show.jpeg"></div>
    <?php endif; ?>
</div>   

<script>
    let slideIndex = 0;
    function showSlides() {
        let i;
        const slides = document.getElementsByClassName("mySlides");
        if (slides.length === 0) return;
        for (i = 0; i < slides.length; i++) {
            slides[i].style.display = "none";
        }
        slideIndex++;
        if (slideIndex > slides.length) { slideIndex = 1 }
        slides[slideIndex - 1].style.display = "block";
        setTimeout(showSlides, 2000); 
    }
    showSlides();

    function scrollToTop() {
        window.scroll({ top: 0, left: 0, behavior: 'smooth' });
    }

    document.querySelector("#cookies-btn").addEventListener("click",()=>{
        document.querySelector("#cookies").style.display="none";
        let date = new Date();
        date.setTime(date.getTime() + (30*24*60*60*1000));
        document.cookie = "cookie=true; expires=" + date.toUTCString() + "; path=/";
    });

    window.addEventListener("load", () => {
        if (!document.cookie.includes("cookie=true")) {
            document.querySelector("#cookies").style.display = "block";
        }
    });
</script>

<?php 
// 4. JAVÍTÁS: Az új közös lábléc behívása
include_once 'views/layout/footer.php'; 
?>
</html>
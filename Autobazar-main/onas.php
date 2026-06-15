<?php
session_start();

// 1. Kapcsolat és a Modell behívása
require_once 'config/Database.php'; 
require_once 'models/Inzerat.php'; 

// Fejléc (Navigáció) behívása
include_once 'views/layout/header.php'; 

// 2. OOP példányosítás - lekérjük az autók darabszámát statisztikának az Accordionhoz
$inzeratManager = new Inzerat($conn);
$vsetkyInzeraty = $inzeratManager->getAllInzeratiWithUsers();
$pocetAut = count($vsetkyInzeraty);
?>

<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/Style1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <title>Dostupné autá & Kontakt</title>
    
    <style>
        body {
            background-color: #333 !important;
            color: #fff !important;
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        /* Kapcsolati oszlopok szép elrendezése */
        .kontakt-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            max-width: 1100px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .stplce {
            flex: 1;
            min-width: 280px;
            background: #222;
            padding: 25px; /* Kicsit növelve a belső tér */
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
            line-height: 1.8; /* Megnövelt sormagasság a jobb olvashatóságért */
        }

        .stplce h3 {
            margin-top: 0;
            color: #007bff;
            border-bottom: 2px solid #444;
            padding-bottom: 8px;
            text-transform: uppercase;
            font-size: 18px;
            margin-bottom: 15px;
        }

        /* Nyitvatartás listás elrendezése, hogy ne csússzon szét semmi */
        .oh-lista {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .oh-lista li {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .stplce a {
            color: #fff;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 8px;
            transition: color 0.2s;
        }

        .stplce a i {
            margin-right: 8px;
            color: #007bff;
        }

        .stplce a:hover {
            color: #007bff;
        }

        /* Accordion (Lenyíló menü) modernizálása */
        .accordion {
            max-width: 1100px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .accordion-item {
            background: #222;
            margin-bottom: 12px;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }

        .accordion-header {
            background: #2b2b2b;
            padding: 15px 20px;
            font-weight: bold;
            cursor: pointer;
            user-select: none;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background 0.2s;
        }

        .accordion-header:hover {
            background: #3a3a3a;
        }

        .accordion-content {
            padding: 20px;
            display: none;
            background: #1e1e1e;
            border-top: 1px solid #333;
            line-height: 1.6;
        }

        /* Footer dizájn */
        footer {
            background: #1a1a1a;
            color: #ccc;
            padding: 40px 20px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            gap: 30px;
            border-top: 3px solid #222;
            margin-top: 60px;
        }

        footer iframe {
            border-radius: 6px;
            max-width: 100%;
        }

        .footernadpis {
            color: #fff;
            font-size: 18px;
            margin-bottom: 15px;
            text-transform: uppercase;
        }

        .footerikony a {
            color: #fff;
            background: #333;
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin-right: 10px;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .footerikony a:hover {
            background: #007bff;
            transform: translateY(-3px);
        }

        .footermail {
            display: flex;
            gap: 5px;
            margin-top: 10px;
        }

        .footerinput {
            padding: 10px 15px;
            border: 1px solid #444;
            background: #2a2a2a;
            color: #fff;
            border-radius: 4px;
            width: 200px;
            outline: none;
        }

        .footerbtn {
            padding: 10px 20px;
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }

        .footer-bottom {
            background: #111;
            padding: 15px;
            text-align: center;
            color: #666;
            font-size: 14px;
        }

        .hore {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: #007bff;
            color: white;
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
            z-index: 999;
            cursor: pointer;
            transition: background 0.2s;
        }
        .hore:hover { background: #0056b3; }
    </style>
</head>
<body>

    <div class="kontakt-grid">
        
        <div class="stplce">
            <h3><i class="fas fa-clock"></i> Otváracie hodiny</h3>
            <ul class="oh-lista">
                <li><span>Pondelok:</span> <span>9:00 - 17:00</span></li>
                <li><span>Utorok:</span> <span>9:00 - 17:00</span></li>
                <li><span>Streda:</span> <span>9:00 - 17:00</span></li>
                <li><span>Štvrtok:</span> <span>9:00 - 17:00</span></li>
                <li><span>Piatok:</span> <span>9:00 - 17:00</span></li>
                <li><span>Sobota:</span> <span>9:00 - 13:00</span></li>
                <li style="color: #ff4d4d; font-weight: bold;"><span>Nedeľa:</span> <span>Zatvorené</span></li>
            </ul>
        </div>

        <div class="stplce">
            <h3><i class="fas fa-info-circle"></i> Kontakt o nás</h3>
            <strong>Tel.č:</strong> +421 956 569 585 <br>
            <strong>Email:</strong> autobazardavid@bazar.sk <br>
            <strong>IČO:</strong> 5415315131 <br>
            <strong>DIČ:</strong> 5534533331 <br>
            <strong>Adresa:</strong> Ulica akademická 15115
        </div>

        <div class="stplce">
            <h3><i class="fas fa-tools"></i> Zmluvný Servis</h3>
            <a href="https://auto-motiv.sk/" target="_blank"><i class="fas fa-wrench"></i> BMW Servis</a><br>
            <a href="https://www.galimex.sk/servis/" target="_blank"><i class="fas fa-wrench"></i> AUDI Servis</a><br>   
            <a href="https://borsauto.sk/" target="_blank"><i class="fas fa-wrench"></i> ŠKODA Servis</a>
        </div> 
    </div>

    <div class="accordion">
        <div class="accordion-item">
            <div class="accordion-header" onclick="toggleAccordion('section1')">
                <span>Aktuálny stav autobazáru</span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="accordion-content" id="section1">
                Momentálne máme v ponuke <strong><?php echo $pocetAut; ?></strong> dostupných vozidiel online. Všetky vozidlá sú skontrolované naším zmluvným servisom.
            </div>
        </div>
        
        <div class="accordion-item">
            <div class="accordion-header" onclick="toggleAccordion('section2')">
                <span>Garancia a garančné podmienky</span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="accordion-content" id="section2">
                Na každé zakúpené vozidlo poskytujeme 12-mesačnú záruku na skryté technické závady a garantujeme legálny pôvod najazdených kilometrov.
            </div>
        </div>
    </div>


    <div class="footer-bottom">
        <span class="autor">© Dávid Fagyas, 2026.</span>
    </div>

    <div id="goToTop" class="hore" onclick="scrollToTop()">
        <i class="fas fa-arrow-up"></i>
    </div>

    <script>
        function scrollToTop() {
            window.scroll({
                top: 0,
                left: 0,
                behavior: 'smooth'
            });
        } 
        
        function toggleAccordion(sectionId) {
            var content = document.getElementById(sectionId);
            var header = content.previousElementSibling;
            var icon = header.querySelector('i');
            
            if (content.style.display === "block") {
                content.style.display = "none";
                icon.className = "fas fa-chevron-down";
            } else {
                content.style.display = "block";
                icon.className = "fas fa-chevron-up";
            }
        }
    </script>
    <?php 
// 4. JAVÍTÁS: Az új közös lábléc behívása
include_once 'views/layout/footer.php'; 
?>
</body>
</html>
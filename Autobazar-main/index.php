<?php session_start(); ?>
<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/Style1.css">
    <title>Autobazár Dávid</title>
</head>

<body>
<header>
    <a href="index.php">
        <img src="assets/logo.jpg" height="75px" width="105px">
    </a>
    <h1>Autobazár Dávid</h1>
    
    <div class="header">
        <?php if (isset($_SESSION['user_id'])): ?>
            <!-- Ez látszik, ha BE VAN jelentkezve -->
            <?php echo htmlspecialchars($_SESSION['username'] ?? 'Používateľ'); ?>
            <a href="views/cars/add_car.php" style="background-color: #4CAF50; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none;">+ Pridať inzerát</a>
            <a href="views/auth/logout.php" style="color: red; margin-left: 15px;">Odhlásiť sa</a>
        <?php else: ?>
            <!-- Ez látszik, ha NINCS bejelentkezve -->
            <a href="views/auth/login.php">Login</a>
            <a href="views/auth/register.php">Register</a>
        <?php endif; ?>
    </div>

    <nav style="top: auto;">
        <ul class="navbar-nav">
            <li><a class="nav-link" href="index.php">Domov</a></li>
            <li><a class="nav-link" href="dostupne-auta.php">Dostupné autá</a></li>
            <li><a class="nav-link" href="onas.php">O nás</a></li>
            <li><a class="nav-link" href="kontakt.php">KONTAKT</a></li>
        </ul>
    </nav>
    
</header>

<!-- Cookie sáv -->
<div id="cookies">
    <div class="containter">
        <div class="subcontainer">
            <div class="cookies">
                <p>Tento web používa na poskytovanie služieb a analýzu návštevnosti súbory cookie. 
                    <a href="">Dozvedieť sa viac</a>
                    <button id="cookies-btn">Rozumiem</button>
                </p>
            </div>
        </div>
    </div>
</div>

<section style="padding: 20px;">
    <p>Riadenie vzťahov so zákazníkmi: predajcovia automobilov musia rozvíjať úzke vzťahy so zákazníkmi.<br> 
    Pomáhajú im vybrať si správne auto na základe ich potrieb a rozpočtu.<br><br>
    Riadenie procesu predaja: predajcovia sú zodpovední za celkové riadenie procesu predaja.<br> 
    To zahŕňa informovanie zákazníkov, prezentáciu automobilov, riadenie predajného procesu a vypracovanie predajných zmlúv.<br><br>
    Analýza trhu: sledujú trendy na trhu s automobilmi a sú informovaní o aktuálnych cenách, modeloch a ďalších relevantných informáciách.</p>
</section>

<div class="slideshow-container">
    <div class="mySlides">
        <img src="autoslideshow/car1_show.webp" style="width:100%">
    </div>
    <div class="mySlides">
        <img src="autoslideshow/car2_show.jpeg" style="width:100%">
    </div>
    <div class="mySlides">
        <img src="autoslideshow/car4_show.jpg" style="width:100%">
    </div>
</div>  

<script>
    // Slideshow logika
    let slideIndex = 0;
    function showSlides() {
        let i;
        const slides = document.getElementsByClassName("mySlides");
        for (i = 0; i < slides.length; i++) {
            slides[i].style.display = "none";
        }
        slideIndex++;
        if (slideIndex > slides.length) { slideIndex = 1 }
        if (slides.length > 0) {
            slides[slideIndex - 1].style.display = "block";
            setTimeout(showSlides, 2000); 
        }
    }
    showSlides();

    // Scroll to top
    function scrollToTop() {
        window.scroll({ top: 0, left: 0, behavior: 'smooth' });
    }

    // Cookie kezelés
    document.querySelector("#cookies-btn").addEventListener("click",()=>{
        document.querySelector("#cookies").style.display="none";
        // Itt volt egy kis hiba a kódodban (expDays), javítva:
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

<footer>
    <div class="col-25">
        <h3 class="footernadpis"> Tu sa nachádzame</h3>
        <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d10614.839764656655!2d18.0910518!3d48.3084298!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0xba2bad032d96b960!2sFakulta%20pr%C3%ADrodn%C3%BDch%20vied%20a%20informatiky!5e0!3m2!1ssk!2ssk!4v1669307792855!5m2!1ssk!2ssk" width="650" height="280" style="border:0;" allowfullscreen="" loading="lazy"></iframe> 
    </div>
    <div class="footermenu2">
        <h3 class="footernadpis">SLEDUJTE NÁS!</h3>
        <div class="footerikony">
            <a href="https://www.facebook.com/david.fagyas.3"><i class="fab fa-facebook-f"></i></a>
            <a href="https://www.instagram.com/_15david_/"><i class="fab fa-instagram"></i></a>  
        </div> 
    </div>
    <div class="footermail">
        <h3 class="footernadpis">Pripojte sa k Newsletteru</h3>
        <input type="email" placeholder="ZADAJTE VÁŠ EMAIL" class="footerinput">
        <button class="footerbtn">ODOSLAŤ</button>
    </div>
</footer>

<footer>
    <div class="footermenu2">
        <span class="autor">&copy; Dávid Fagyas, 2026.</span>
    </div>
</footer>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<div id="goToTop" class="hore" onclick="scrollToTop()" style="cursor: pointer;">
    <i class="fas fa-arrow-up"></i>
</div>
</body>
</html>
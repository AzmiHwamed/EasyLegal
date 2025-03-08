<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing Page - Easy Legal</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            text-align: center;
            background: #f8f4ef;
        }


        h1 {
            margin-top: 20px;
            font-size: 24px;
        }

        

        .features {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
            font-weight: bold;
        }

        .info {
            padding: 20px;
        }

        .info-box {
            background: white;
            margin: 20px auto;
            padding: 20px;
            width: 80%;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        .info-box img {
            width: 80px;
            display: block;
            margin: 10px auto;
        }

        .highlight {
            color: #e8a043;
        }

        .explore {
            margin: 20px 0;
        }

        .number {
            font-weight: bold;
            color: #e8a043;
        }

        .explore-btn {
            background: #e8a043;
            color: white;
            padding: 10px 20px;
            border: none;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
        }

        footer {
            background: #f0e6d6;
            padding: 10px;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <!-- nav bar -->
        <?php include('./nav/index.php') ?>
    <!-- end of nav bar -->

    <!-- hero section -->
    <section class="Hero">
        <h1>Votre compagnon legal</h1>
        <img src="./assets/logo.png" alt="Icône de la justice" class="hero-image">
        <div class="features">
            <div class="feature">Chercher</div>
            <div class="feature">Discuter</div>
            <div class="feature">Consulter</div>
        </div>
    </section>
    <!-- end of hero section  -->
    <!-- information boxes  -->
    <section class="info">
        <!-- info box  -->
        <div class="info-box">
            <h2 class="highlight">Chercher les lois qui vous concernent</h2>
            <p>Cherchez parmi des centaines des lois par catégorie, mots clés, et découvrez celles qui correspondent à
                vos besoins.</p>
            <img src="balance.png" alt="Balance de justice">
        </div>
        <!-- end of info box  -->


        <!-- info box  -->
        <div class="info-box">
            <h2 class="highlight">Discuter avec des experts</h2>
            <p>Trouvez le conseil juridique parfait fourni par un ensemble d’experts.</p>
            <img src="lawyer.png" alt="Avocat donnant des conseils">
        </div>
        <!-- end of info box  -->
    </section>
    <!-- end of information boxes -->

    <section class="explore">
        <h2>Plus que <span class="number">100</span> lois à explorer</h2>
        <button class="explore-btn">Explorer maintenant</button>
    </section>

    <footer>
        <p>Easy legal &copy; 2025 | All rights reserved</p>
    </footer>
</body>

</html>
<?php
$nom_utilisateur = isset($_SESSION['nom']) ? $_SESSION['nom'] : "Admin";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord Admin</title>
    <style>
        /* Reset + police */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', 'Arial', sans-serif;
        }

        body {
            display: flex;
            background-color: #f4f6f9;
            color: #333;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Sidebar */
        .sidebar {
            width: 260px;
            background: linear-gradient(135deg, #e89d3f, #e8a043);
            color: white;
            padding: 40px 25px;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.1);
        }

        .sidebar h2 {
            margin-bottom: 35px;
            font-size: 22px;
            text-align: center;
        }

        .sidebar nav ul {
            list-style: none;
        }

        .sidebar nav ul li {
            margin: 20px 0;
        }

        .sidebar nav ul li a {
            color: #fff;
            text-decoration: none;
            padding: 12px 18px;
            display: block;
            font-size: 16px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .sidebar nav ul li a:hover {
            background-color: rgba(255, 255, 255, 0.15);
            padding-left: 28px;
        }

        /* Contenu principal */
        .main-content {
            margin-left: 260px;
            padding: 40px;
            width: calc(100% - 260px);
            background-color: #fff;
        }

        .main-content h1 {
            font-size: 34px;
            margin-bottom: 15px;
            font-weight: 600;
            color: #222;
        }

        .main-content p {
            font-size: 18px;
            color: #555;
            margin-top: 10px;
        }

        /* Responsive */
        @media screen and (max-width: 768px) {
            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
                box-shadow: none;
            }

            .main-content {
                margin-left: 0;
                width: 100%;
                padding: 20px;
            }
        }
    </style>
</head>
<body>

    <aside class="sidebar">
        <h2>Bienvenue, <?php echo htmlspecialchars($nom_utilisateur); ?> 👋</h2>
        <nav>
            <ul>
                <li><a href="../user/index.php" onclick="return confirmNavigation(this.href);">Gérer les utilisateurs</a></li>
                <li><a href="../forum/index.php" onclick="return confirmNavigation(this.href);">Gérer le forum</a></li>
                <li><a href="../text/index.php" onclick="return confirmNavigation(this.href);">Textes juridiques</a></li>
                <li><a href="../expert/index.php" onclick="return confirmNavigation(this.href);">Gérer les experts</a></li>
                <li><a href="../../index.php" onclick="return confirmNavigation(this.href);">Page d'accueil</a></li>
            </ul>
        </nav>
    </aside>

    <main class="main-content">
        <h1>Tableau de bord</h1>
        <p>Bienvenue sur votre espace administrateur. Utilisez la barre latérale pour naviguer entre les différentes sections de gestion.</p>
    </main>

    <script>
        function confirmNavigation(url) {
            if (confirm("Voulez-vous vraiment accéder à cette page ?")) {
                window.location.href = url;
            }
            return false;
        }
    </script>
</body>
</html>

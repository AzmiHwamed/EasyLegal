<?php
session_start();
session_regenerate_id(true); // Sécurisation de la session

// Vérification de connexion de l'utilisateur
$nom_utilisateur = isset($_SESSION['nom']) ? $_SESSION['nom'] : "Admin";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f6f9;
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background-color: #34495e;
            color: white;
            height: 100%;
            position: fixed;
            top: 0;
            left: 0;
            padding: 40px 30px;
            box-shadow: 2px 0 20px rgba(0, 0, 0, 0.7);
        }

        .sidebar h2 {
            margin-bottom: 40px;
            font-size: 24px;
            font-weight: 700;
            text-align: center;
            letter-spacing: 1px;
            color: #ecf0f1;
        }

        .sidebar nav ul {
            padding-left: 0;
            list-style: none;
        }

        .sidebar nav ul li {
            margin: 25px 0;
        }

        .sidebar nav ul li a {
            color: #ecf0f1;
            text-decoration: none;
            font-size: 18px;
            padding: 12px 20px;
            display: block;
            border-radius: 30px;
            transition: all 0.3s ease;
        }

        .sidebar nav ul li a:hover {
            background-color: #1abc9c;
            color: white;
            padding-left: 25px;
            transform: translateX(10px);
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }

        .main-content {
            margin-left: 280px;
            padding: 40px;
            width: calc(100% - 280px);
            background-color: #fff;
            min-height: 100vh;
        }

        h1 {
            color: #2c3e50;
            font-size: 28px;
            margin-bottom: 20px;
        }

        @media screen and (max-width: 768px) {
            .sidebar {
                width: 100%;
                position: relative;
                padding: 15px;
            }

            .main-content {
                margin-left: 0;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Bienvenue, <?php echo htmlspecialchars($nom_utilisateur); ?> 👋</h2>
        <nav role="navigation">
            <ul>
                <li><a href="../user/index.php" onclick="return confirmNavigation(this.href);" aria-label="Gérer les utilisateurs">Gérer les utilisateurs</a></li>
                <li><a href="../forum/index.php" onclick="return confirmNavigation(this.href);" aria-label="Gérer le forum">Gérer le forum</a></li>
                <li><a href="../text/index.php" onclick="return confirmNavigation(this.href);" aria-label="Gérer les textes juridiques">Gérer les textes juridiques</a></li>
                <li><a href="../expert/index.php" onclick="return confirmNavigation(this.href);" aria-label="Gérer les experts">Gérer les experts</a></li>
            </ul>
        </nav>
    </div>

    <div class="main-content">
        <h1>Tableau de Bord</h1>
        <p>Bienvenue sur votre tableau de bord administrateur.</p>
    </div>

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

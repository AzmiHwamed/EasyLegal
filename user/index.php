<?php
session_start();

// Vérification de la session utilisateur
if (!isset($_SESSION['id']) || $_SESSION['id'] == null) {
    header('Location: ../auth/login.php');
    exit();
}

// Redirection selon le rôle de l'utilisateur
if ($_SESSION['role'] != 'user') {
    header('Location: ../' . $_SESSION['role'] . '/index.php');
    exit();
}

// Récupérer le nom de l'utilisateur (si disponible dans la session)
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';

// Définir la base de l'URL (à modifier si nécessaire)
$base_url = "http://127.0.0.1/pfe/EasyLegal";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        /* Réinitialisation */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        nav {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            max-width: 100%;
            height: 5vh;
            padding: 1%;
            background-color: #F3EEE5;
            box-shadow: 5px 12px 10px rgba(0, 0, 0, 0.2);
            position: sticky;
            top: 0;
        }

        nav a img {
            width: 4vw !important;
            max-height: 100%;
            min-height: 100%;
        }

        nav span {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            width: 20%;
        }

        nav span a {
            text-decoration: none;
            color: #000;
            font-weight: bolder;
        }

        /* Contenu principal */
        .container {
            text-align: center;
            padding: 50px 20px;
            background-color: #fdfbf7;
            min-height: 90vh;
        }

        .welcome-text {
            font-size: 24px;
            margin-bottom: 30px;
        }

        .username {
            font-weight: bold;
        }

        /* Cartes */
        .cards {
            display: flex;
            justify-content: center;
            gap: 40px;
        }

        .card {
            background-color: #fbc56c;
            padding: 30px;
            border-radius: 15px;
            width: 250px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card-icon {
            font-size: 50px;
            margin-bottom: 10px;
        }

        h3 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        /* Bouton */
        .btn {
            background-color: black;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            margin-top: 10px;
        }

        .btn:hover {
            background-color: #333;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .cards {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <!-- Barre de navigation -->
    <nav>
        <a href="#">
            <img src="./assets/logo.png" alt="Icône de la justice" class="hero-image">
        </a>
        <span>
            <a href="#">Rechercher</a>
            <a href="<?= $base_url ?>/forum/index.php">Forum</a>
            <a href="#">Discuter</a>
        </span>
        <a><img src="./assets/Male User.png" alt="Compte utilisateur" style="width: 3vw !important;"></a>
    </nav>

    <!-- Contenu principal -->
    <div class="container">
        <h2 class="welcome-text">Bienvenue <span class="username"><?= $username ?></span></h2>

        <div class="cards">
            <!-- Messagerie -->
            <div class="card">
                <div class="card-icon">💬</div>
                <h3>Messagerie</h3>
                <button class="btn">Voir</button>
            </div>

            <!-- Forum -->
            <div class="card">
                <div class="card-icon">🗨️</div>
                <h3>Forum</h3>
                <a href="<?= $base_url ?>/forum/index.php" class="btn">Voir</a>
            </div>
        </div>
    </div>
</body>
</html>

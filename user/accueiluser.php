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

        /* Barre de navigation */
        .navbar {
            background-color: #f8f1e4;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 40px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        .logo img {
            width: 50px;
            border-radius: 50%;
        }

        .nav-links {
            list-style: none;
            display: flex;
            align-items: center;
        }

        .nav-links li {
            margin: 0 15px;
        }

        .nav-links a {
            text-decoration: none;
            color: black;
            font-weight: bold;
            font-size: 16px;
        }

        .logout {
            display: flex;
            align-items: center;
        }

        .user-icon {
            font-size: 18px;
            margin-left: 5px;
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
    <nav class="navbar">
        <div class="logo">
            <img src="logo.png" alt="Logo">
        </div>
        <ul class="nav-links">
            <li><a href="#">Rechercher</a></li>
            <li><a href="#">Forum</a></li>
            <li><a href="#">Discuter</a></li>
            <li><a href="#">Accueil</a></li>
            <li class="logout"><a href="#">Déconnexion</a> <span class="user-icon">👤</span></li>
        </ul>
    </nav>

    <!-- Contenu principal -->
    <div class="container">
        <h2 class="welcome-text">Bienvenue <span class="username">Madame X</span></h2>
        
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
                <button class="btn">Voir</button>
            </div>
        </div>
    </div>

</body>
</html>

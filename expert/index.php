Les experts 
<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['user_id'] == null){
    header('Location: ../auth/login.php');
    exit();
}
if($_SESSION['type'] != 'expert'){
    header('Location: ../'.$_SESSION['type'].'/index.php');
}

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
            box-shadow:  5px 12px 10px rgba(0, 0, 0, 0.2);
            position: sticky;
            top: 0;
        }

        nav a img {
            width:  4vw !important;
            max-height: 100%;
            min-height: 100%;

        }
        nav span{
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            width: 20%;
        }
        nav span a{
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
            <a href="#">Forum</a>
            <a href="#">Disscuter</a>
        </span>
        <a><img src="./assets/Male User.png" alt="Account" style="width: 3vw !important;"></a>
</nav>

    <!-- Contenu principal -->
    <div class="container">
        <h2 class="welcome-text">Bienvenue <span class="username">Expert X</span></h2>
        
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

<?php
session_start();

// Vérification de la session utilisateur
if (!isset($_SESSION['id']) || $_SESSION['id'] == null) {
    header('Location: ../auth/login.php');
    exit();
}
include('../validateur.php');isUser();


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
    <title>Utilisateur</title>
    <style>
        
        /* Réinitialisation */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #fdfbf7;
            color: #333;
        }




        /* Contenu principal */
        .container {
            text-align: center;
            padding: 4rem 2rem;
            min-height: 90vh;
        }

        .cards {
            display: flex;
            justify-content: center;
            gap: 2rem;
            flex-wrap: wrap;
        }

        .card {
            background-color: #fbc56c;
            padding: 2rem;
            border-radius: 15px;
            width: 250px;
            text-align: center;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 16px rgba(0, 0, 0, 0.15);
        }

        .card-icon {
            font-size: 50px;
            margin-bottom: 15px;
        }

        h3 {
            font-size: 20px;
            margin-bottom: 1rem;
        }

        .btn {
            display: inline-block;
            background-color: #000;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #333;
        }

        /* Responsive */
        @media (max-width: 768px) {
            nav .nav-links {
                gap: 1rem;
            }

            .card {
                width: 80%;
            }
        }

    </style>
</head>
<body>
<?php include('../nav/index.php'); ?>

    <main class="container">
        <div class="cards">
            <!-- Messagerie -->
            <div class="card">
                <div class="card-icon">📬</div>
                <h3>Messagerie</h3>
                <a href="../messaging/index.php" class="btn">Voir</a>
            </div>

            <!-- Forum -->
            <div class="card">
                <div class="card-icon">🗨️</div>
                <h3>Forum</h3>
                <a href="../forum/index.php" class="btn">Voir</a>
            </div>
        </div>
    </main>

</body>
</html>

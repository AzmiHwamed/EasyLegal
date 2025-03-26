<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "easylegal";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Récupérer les posts du forum
$sql = "SELECT id, contenu ,id_personne FROM forum ORDER BY id DESC LIMIT 50";
$result = $conn->query($sql);
$posts = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forum</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        /* Styles généraux */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #ecf0f1;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            max-width: 800px;
            width: 100%;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h1 {
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 20px;
        }

        /* Bouton Ajouter */
        .add-btn {
            background: #3498db;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: 0.3s ease;
            display: inline-block;
            text-decoration: none;
        }

        .add-btn:hover {
            background: #2980b9;
            transform: scale(1.05);
        }

        /* Affichage des posts */
        .posts-container {
            margin-top: 20px;
            text-align: left;
        }

        .post {
            background: #f9fafc;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0px 3px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease-in-out;
        }

        .post:hover {
            transform: translateY(-2px);
        }

        .post p {
            margin: 0;
            color: #333;
            font-size: 14px;
            flex: 1;
            padding-right: 10px;
        }

        /* Bouton Supprimer */
        .delete-btn {
            background: #e74c3c;
            border: none;
            color: white;
            padding: 7px 12px;
            cursor: pointer;
            border-radius: 6px;
            transition: 0.3s ease;
        }

        .delete-btn:hover {
            background: #c0392b;
            transform: scale(1.1);
        }

        /* Responsive Design */
        @media (max-width: 600px) {
            .container {
                padding: 15px;
            }

            .post {
                flex-direction: column;
                align-items: flex-start;
            }

            .delete-btn {
                margin-top: 8px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Forum</h1>

    <!-- Bouton pour aller à la page create.php -->
    <a href="create.php" class="add-btn">➕ Ajouter un post</a>

    <!-- Affichage des posts -->
    <div class="posts-container">
        <h2>Discussions</h2>
        <?php foreach ($posts as $post): ?>
            <div class="post">
                <p><?= htmlspecialchars($post['contenu']) ?></p>
                <form action="delete.php" method="post">
                    <input type="hidden" name="id" value="<?= $post['id'] ?>">
                    <button type="submit" class="delete-btn">🗑 Supprimer</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>

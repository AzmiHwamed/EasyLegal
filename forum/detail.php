<?php
// Connexion à la base de données
try {
    $dsn = 'mysql:host=localhost:4306;dbname=easylegal;charset=utf8';
    $username = 'root'; 
    $password = '';
    $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

    $pdo = new PDO($dsn, $username, $password, $options);
} 
catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Récupérer les posts avec le nombre de likes
$forums = $pdo->query("
    SELECT forum.*, 
           (SELECT COUNT(*) FROM aime WHERE aime.id_forum = forum.id) as likes 
    FROM forum 
    ORDER BY id DESC
")->fetchAll(PDO::FETCH_ASSOC);
// Gérer les likes (AJAX)
if (isset($_POST['id_forum']) && isset($_POST['like'])) {
    $id_forum = $_POST['id_forum'];
    $id_personne = 1; // À modifier pour l'utilisateur connecté

    $check = $pdo->prepare("SELECT * FROM aime WHERE id_forum = ? AND id_personne = ?");
    $check->execute([$id_forum, $id_personne]);

    if ($check->rowCount() == 0) {
        $pdo->prepare("INSERT INTO aime (id_forum, id_personne) VALUES (?, ?)")->execute([$id_forum, $id_personne]);
    } else {
        $pdo->prepare("DELETE FROM aime WHERE id_forum = ? AND id_personne = ?")->execute([$id_forum, $id_personne]);
    }

    // Retourner le nouveau nombre de likes
    $likes = $pdo->prepare("SELECT COUNT(*) FROM aime WHERE id_forum = ?");
    $likes->execute([$id_forum]);
    echo json_encode(["success" => true, "likes" => $likes->fetchColumn()]);
    exit;
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forum Juridique</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #F8F4ED;
            margin: 0;
            padding: 0;
        }
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #F3EEE5;
            padding: 10px 20px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        nav a img {
            width: 50px;
        }
        nav span {
            display: flex;
            gap: 20px;
        }
        nav span a {
            text-decoration: none;
            font-weight: bold;
            color: black;
        }
        .container {
            display: flex;
            margin: 20px;
        }
        .sidebar {
            width: 10%;
            background: #F3EEE5;
            padding: 15px;
            border-radius: 10px;
            margin-right: 20px;
        }
        .content {
            width: 75%;
        }
        .question {
            background: #F8E2BE;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 15px;
            font-weight: bold;
            transform: translateY(-5px);

                }
        .response {
            background: #FFF;
            padding: 10px;
            border-radius: 10px;
            margin-bottom: 10px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
            position: relative;
        }
        .expert-response {
            background:rgb(215, 199, 155);
        }
        .response .like-btn {
            color: red;
            cursor: pointer;
        }
        .response .star {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 20px;
            color: gold;
        }
        .comment-box {
            display: flex;
            align-items: center;
            background: #FFF;
            padding: 10px;
            border-radius: 10px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
            margin-top: 15px;
        }
        .comment-box input {
            flex: 1;
            border: none;
            padding: 10px;
            border-radius: 5px;
        }
        .send-btn {
            background: #E7A63D;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-retour{
            color :#E7A63D ;
            align: right ;
            font-size: 25px;
            top: 20px;
            right: 20px;
            width: 40px;
            height: 40px;
            background-color: white;
            text-align: center;
            line-height: 40px;
            border-radius: 50%;
            text-decoration: none;
        }
       









    </style>
</head>
<body>
    <nav>
        <a href="#"><img src="../assets/logo.png" alt="Icône de la justice"></a>
        <span>
            <a href="#">Rechercher</a>
            <a href="#">Forum</a>
            <a href="#">Discuter</a>
        </span>
        <a href="#"><img src="../assets/Male User.png" alt="Compte"></a>
    </nav>

    <div class="container">
        <div class="sidebar">
            <h3>Utilisateur TOP 5:</h3>
            <p>1. Anonyme 12581</p>
            <p>2. Anonyme 1247</p>
            <p>3. Anonyme 52474</p>
            <h3>Expert TOP 5:</h3>
            <p>1. Expert 1024</p>
            <p>2. Expert 1027</p>
        </div>

        <div class="content">
            <div class="question">
                <p>La Question de Madame Anonyme 1257:</p>
                <p>Comment faire lorsque je veux faire une chose légale s'il vous plaît...</p>
                   <a  class="btn-retour" href="javascript:history.back()" >⬅</a>
                
            </div>
            
            <div class="response">
                <p><strong>La Réponse de Madame Anonyme 1258:</strong></p>
                <p>Voici ma réponse...</p>
                <span class="like-btn">❤️ J'aime</span>
            </div>

            <div class="response">
                <p><strong>La Réponse de Madame Anonyme 1259:</strong></p>
                <p>Une autre réponse...</p>
                <span class="like-btn">❤️ J'aime</span>
            </div>

            <div class="response expert-response">
                <p><strong>La Réponse de Expert 102:</strong></p>
                <p>Voici la réponse d'un expert...</p>
                <span class="star">⭐</span>
                <span class="like-btn">❤️ J'aime</span>
            </div>

            <div class="comment-box">
                <input type="text" placeholder="Ecrivez votre commentaire...">
                <button class="send-btn">Envoyer</button>
            </div>
        </div>
    </div>
</body>
</html>
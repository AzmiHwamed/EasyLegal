<?php
// Connexion à la base de données
try {
    $dsn = 'mysql:host=localhost;dbname=easylegal;charset=utf8';
    $username = 'root'; 
    $password = '';
    $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Récupérer la question et les réponses
$id_forum = isset($_GET['id']) ? (int)$_GET['id'] : 1; // Validation sécurisée de l'ID
$question = $pdo->prepare("SELECT forum.*, personne.nom AS auteur_nom FROM forum 
                            JOIN personne ON forum.id_personne = personne.id
                            WHERE forum.id = ?");
$question->execute([$id_forum]);
$question = $question->fetch(PDO::FETCH_ASSOC);

$reponses = $pdo->prepare("SELECT reponse.*, personne.role, personne.nom FROM reponse 
JOIN personne ON reponse.id_personne = personne.id
WHERE id_forum = ? ORDER BY reponse.id DESC");
$reponses->execute([$id_forum]);
$reponses = $reponses->fetchAll(PDO::FETCH_ASSOC);

// Ajouter une réponse
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reponse'])) {
    $reponse = htmlspecialchars($_POST['reponse']); // Éviter les injections XSS
    $id_personne = 1; // Exemple: Remplacer par l'ID réel de l'utilisateur connecté

    $stmt = $pdo->prepare("INSERT INTO reponse (id_forum, id_personne, contenu) VALUES (?, ?, ?)");
    $stmt->execute([$id_forum, $id_personne, $reponse]);

    header("Location: " . $_SERVER['PHP_SELF'] . "?id=$id_forum"); // Redirection pour éviter la soumission multiple
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails Forum</title>
    <style>
        /* style.css */
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
            width: 20%;
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
            background: rgb(215, 199, 155);
        }
        .response .like-btn {
            color: red;
            cursor: pointer;
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
        .btn-retour {
            color: #E7A63D;
            font-size: 25px;
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
                <p>La Question de <?php echo htmlspecialchars($question['auteur_nom'] ?? 'Auteur inconnu'); ?>:</p>
                <p><?php echo nl2br(htmlspecialchars($question['contenu'])); ?></p>
                <a class="btn-retour" href="javascript:history.back()">⬅</a>
            </div>

            <?php foreach ($reponses as $reponse): ?>
                <div class="response <?php echo ($reponse['role'] === 'expert') ? 'expert-response' : ''; ?>">
                    <p><strong>La Réponse de <?php echo htmlspecialchars($reponse['role']) . ' ' . htmlspecialchars($reponse['nom']); ?>:</strong></p>
                    <p><?php echo nl2br(htmlspecialchars($reponse['contenu'])); ?></p>
                    <span class="like-btn" data-id="<?php echo $reponse['id']; ?>">❤ J'aime</span>
                </div>
            <?php endforeach; ?>

            <!-- Formulaire pour ajouter une réponse -->
            <form method="POST" action="">
                <div class="comment-box">
                    <input type="text" name="reponse" placeholder="Écrivez votre réponse..." required>
                    <button class="send-btn" type="submit">Envoyer</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // script.js
        document.addEventListener('DOMContentLoaded', function () {
            // Gérer le clic sur le bouton "J'aime"
            document.querySelectorAll('.like-btn').forEach(function (button) {
                button.addEventListener('click', function () {
                    const reponseId = this.getAttribute('data-id');
                    if (reponseId) {
                        alert("Vous avez aimé la réponse " + reponseId); // Remplacer par l'appel AJAX pour enregistrer le like
                    }
                });
            });
        });
    </script>
</body>
</html>

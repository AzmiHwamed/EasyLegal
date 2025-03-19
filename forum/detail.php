<?php
session_start();
include('../dbconfig/index.php'); // Assurez-vous que la connexion à la base de données est correcte.

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION['id'])) {
    die('Vous devez être connecté pour ajouter un commentaire.');
}

// Récupérer l'ID du forum à partir de l'URL (GET)
if (isset($_GET['id_forum'])) {
    $id_forum = $_GET['id_forum'];
} else {
    die('ID du forum manquant.');
}

// Récupérer l'ID de l'utilisateur depuis la session
$id_personne = $_SESSION['id'];

// Ajout de commentaire
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['commentaire'])) {
    $commentaire = $_POST['commentaire'];

    // Vérification de l'existence du forum
    $stmt = $conn->prepare("SELECT id FROM forum WHERE id = ?");
    $stmt->bind_param('i', $id_forum);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $stmt = $conn->prepare("INSERT INTO commentaire (contenu, id_personne, id_forum) VALUES (?, ?, ?)");
        $stmt->bind_param('sii', $commentaire, $id_personne, $id_forum);
        
        if ($stmt->execute()) {
            header('Location: detail.php?id_forum=' . $id_forum);
            exit();
        } else {
            die('Erreur d\'exécution : ' . $stmt->error);
        }
    } else {
        die('L\'ID du forum est invalide.');
    }
}

// Récupérer les commentaires du forum
$stmt = $conn->prepare("SELECT commentaire.contenu, personne.nom FROM commentaire 
                        JOIN personne ON commentaire.id_personne = personne.id 
                        WHERE commentaire.id_forum = ?");
$stmt->bind_param('i', $id_forum);
$stmt->execute();
$result = $stmt->get_result();
$comments = $result->fetch_all(MYSQLI_ASSOC);

// Récupérer les informations du forum et le nombre de likes
$stmt = $conn->prepare("SELECT forum.*, 
                               (SELECT COUNT(*) FROM aime WHERE aime.id_forum = forum.id) as likes 
                        FROM forum 
                        WHERE forum.id = ?");
$stmt->bind_param('i', $id_forum);
$stmt->execute();
$forum = $stmt->get_result()->fetch_assoc();


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
    transition: color 0.3s;
}

nav span a:hover {
    color: #E7A63D; /* Changer la couleur au survol */
}

.container {
    display: flex;
    flex-wrap: wrap; /* Ajout de flex-wrap pour les petits écrans */
    margin: 20px;
}

.sidebar {
    width: 20%;
    background: #F3EEE5; /* Couleur de fond douce */
    padding: 20px;
    border-radius: 12px;
    margin-right: 20px;
    box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1); /* Ombre plus subtile pour un effet de profondeur */
    transition: box-shadow 0.3s ease, transform 0.3s ease; /* Transition douce pour les effets */
}

.sidebar:hover {
    transform: translateY(-5px); /* Légère élévation de la sidebar au survol */
    box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2); /* Ombre plus marquée au survol */
}

/* Titres des sections dans la sidebar */
.sidebar h3 {
    font-size: 1.3em;
    color: #333;
    margin-bottom: 15px;
    font-weight: bold;
    border-bottom: 2px solid #E7A63D;
    padding-bottom: 10px;
    letter-spacing: 0.5px; /* Ajout de l'espacement des lettres pour un effet plus aéré */
}

/* Listes dans la sidebar */
.sidebar ul {
    list-style-type: none;
    padding-left: 0;
    margin-top: 10px;
}

.sidebar ul li {
    padding: 12px 0;
    font-size: 1.1em;
    color: #555;
    border-bottom: 1px solid #ddd;
    transition: background-color 0.3s ease, padding-left 0.3s ease; /* Effet au survol */
}

.sidebar ul li:hover {
    background-color: #F8E2BE; /* Couleur de survol plus visible */
    padding-left: 15px; /* Décalage des éléments au survol */
    cursor: pointer;
}

/* Réactif pour les petits écrans */
@media (max-width: 768px) {
    .sidebar {
        width: 100%; /* Prend toute la largeur sur les petits écrans */
        margin-right: 0;
        margin-bottom: 20px;
    }

    .sidebar h3 {
        font-size: 1.1em; /* Réduction de la taille du texte sur petits écrans */
    }

    .sidebar ul li {
        font-size: 1em; /* Réduction de la taille du texte sur petits écrans */
        padding: 10px 0; /* Espacement réduit sur les petits écrans */
    }
}


.content {
    width: 75%;
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.question {
    background: #F8E2BE;
    padding: 15px;
    border-radius: 10px;
    margin-bottom: 15px;
    font-weight: bold;
    transform: translateY(-5px);
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
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
    font-size: 16px;
    margin-right: 10px;
    transition: box-shadow 0.3s;
}

.comment-box input:focus {
    outline: none;
    box-shadow: 0px 0px 8px rgba(231, 166, 61, 0.6);
}

.send-btn {
    background: #E7A63D;
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
    transition: background-color 0.3s;
    align:"right";
}

.send-btn:hover {
    background-color: #D28C2A; /* Changement de couleur au survol */
    
}

.btn-retour {
    color: #E7A63D;
    font-size: 25px;
    position: absolute;
    top: 20px;
    left: 20px;
    background-color: white;
    text-align: center;
    line-height: 40px;
    border-radius: 50%;
    text-decoration: none;
    transition: background-color 0.3s;
}

.btn-retour:hover {
    background-color: #F3EEE5;
}

.comment {
    margin-top: 10px;
    padding: 10px;
    background: #FFF;
    border-radius: 10px;
    box-shadow: 1px 1px 5px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s, background-color 0.3s;
}

.comment:hover {
    transform: translateY(-5px);
    background-color: #F8F4ED; /* Légère modification de couleur au survol */
}

.comment strong {
    font-size: 1.1em;
    color: #E7A63D;
}

.comments-section p {
    font-style: italic;
    color: #888;
}

@media (max-width: 768px) {
    .sidebar {
        width: 100%;
        margin-right: 0;
    }

    .content {
        width: 100%;
    }
}

    
    </style>
</head>
<body>

<nav>
    <a href="#"><img src="../assets/logo.png" alt="Logo"></a>
    <span>
        <a href="../search/result.php">Rechercher</a>
        <a href="../forum/index.php">Forum</a>
        <a href="#">Discuter</a>
    </span>
    <a href="#"><img src="../assets/Male User.png" alt="Compte"></a>
</nav>

<div class="container">
    <div class="sidebar">
        <h3>Utilisateur TOP 5:</h3>
        <ul>
            <li>1. Anonyme 12581</li>
            <li>2. Anonyme 1247</li>
            <li>3. Anonyme 52474</li>
        </ul>

        <h3>Expert TOP 5:</h3>
        <ul>
            <li>1. Expert 1024</li>
            <li>2. Expert 1027</li>
        </ul>
    </div>

    <div class="content">
        <div class="question">
            <p>La Question de <?php echo htmlspecialchars($forum['anonyme'] ? 'Anonyme' : $forum['nom']); ?>:</p>
            <p><?php echo htmlspecialchars($forum['contenu']); ?></p>
        </div>

        <div class="comment-box">
            <form method="POST" action="">
                <input type="text" name="commentaire" placeholder="Écrivez votre commentaire..." required>
                <button class="send-btn" type="submit">Envoyer</button>
            </form>
        </div>

        <div class="comments-section">
            <?php if (isset($comments) && count($comments) > 0): ?>
                <?php foreach ($comments as $comment): ?>
                    <div class="comment">
                        <strong><?php echo htmlspecialchars($comment['nom']); ?>:</strong>
                        <p><?php echo htmlspecialchars($comment['contenu']); ?></p>
                        <span class="like-btn" data-id="<?= $forum['id'] ?>">❤️ J'aime (<span class="like-count"><?= $forum['likes'] ?></span>)</span>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Aucun commentaire pour ce forum.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    // Script pour gérer les likes avec AJAX
    document.querySelectorAll('.like-btn').forEach(button => {
        button.addEventListener('click', function () {
            const forumId = this.dataset.id;
            const likeCountElement = this.querySelector('.like-count');

            fetch('like.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id_forum: forumId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    likeCountElement.textContent = data.likes;
                }
            });
        });
    });
</script>

</body>
</html>

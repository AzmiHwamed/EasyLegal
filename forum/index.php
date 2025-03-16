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

// Récupérer les posts avec le nombre de likes
$forums = $pdo->query("SELECT forum.*, (SELECT COUNT(*) FROM aime WHERE aime.id_forum = forum.id) as likes FROM forum ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les commentaires
$commentaires = $pdo->query("SELECT * FROM commentaire ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

// Ajouter un post
if (isset($_POST['contenu'])) {
    $contenu = $_POST['contenu'];
    $pdo->prepare("INSERT INTO forum (contenu) VALUES (?)")->execute([$contenu]);
    header("Location: index.php");
    exit;
}

// Gérer les likes
if (isset($_POST['like']) && isset($_POST['id_forum'])) {
    $id_forum = $_POST['id_forum'];
    $pdo->prepare("INSERT INTO aime (id_forum, id_personne) VALUES (?, 1)")->execute([$id_forum]);
    echo json_encode(["success" => true]);
    exit;
}

// Ajouter un commentaire
if (isset($_POST['commentaire']) && isset($_POST['id_forum'])) {
    $id_forum = $_POST['id_forum'];
    $commentaire = $_POST['commentaire'];
    $pdo->prepare("INSERT INTO commentaire (contenu, id_forum, id_personne) VALUES (?, ?, 1)")->execute([$commentaire, $id_forum]);
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forum Juridique</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f4ef;
            font-family: 'Arial', sans-serif;
        }
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            padding: 10px 5%;
            background-color: #F3EEE5;
            box-shadow: 5px 5px 10px rgba(0, 0, 0, 0.2);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        nav img {
            max-width: 100px;
            height: auto;
        }
        .container {
            max-width: 800px;
            margin-top: 40px;
        }
        .post-form, .comment-form {
            background: transparent;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .forum-card {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 10px;
            background: #fff;
        }
        .forum-actions button {
            background: none;
            border: none;
            cursor: pointer;
            color: #007bff;
        }
        .comment-section {
            margin-left: 20px;
            font-size: 0.9em;
            color: #333;
        }
    </style>
    <script>
        function likePost(id) {
            fetch('index.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'like=1&id_forum=' + id
            })
            .then(response => response.json())
            .then(data => { if (data.success) location.reload(); });
        }
    </script>
</head>
<body>
<nav>
    <a href="#">
        <img src="../assets/logo.png" alt="Icône de la justice">
    </a>
    <span>
        <a href="#">Rechercher</a>
        <a href="#">Forum</a>
        <a href="#">Discuter</a>
    </span>
    <a href="#">
        <img src="../assets/Male User.png" alt="Profil" style="width: 40px;">
    </a>
</nav>
<div class="container">
    <h1 class="forum-header">Forum de Discussion</h1>
    <h3 class="forum-header">Demandez, discutez et partagez votre avis sur notre forum juridique</h3>
    <div class="post-form">
        <form method="POST">
            <div class="mb-3">
                <textarea name="contenu" class="form-control" placeholder="Écrivez votre message..." required></textarea>
            </div>
            <button type="submit" class="btn btn-warning w-100">Publier</button>
        </form>
    </div>
    <?php foreach ($forums as $forum): ?>
        <div class="forum-card">
            <div class="forum-content">
                <strong>Anonyme <?= $forum['id'] ?></strong>
                <p><?= htmlspecialchars($forum['contenu']) ?></p>
            </div>
            <div class="forum-actions">
                <button onclick="likePost(<?= $forum['id'] ?>)">👍 <?= $forum['likes'] ?></button>
            </div>
            <div class="comment-section">
                <strong>Commentaires :</strong>
                <?php foreach ($commentaires as $commentaire): ?>
                    <?php if ($commentaire['id_forum'] == $forum['id']): ?>
                        <p>- <?= htmlspecialchars($commentaire['contenu']) ?></p>
                    <?php endif; ?>
                <?php endforeach; ?>
                <form method="POST" class="comment-form">
                    <input type="hidden" name="id_forum" value="<?= $forum['id'] ?>">
                    <textarea name="commentaire" class="form-control" placeholder="Ajouter un commentaire..." required></textarea>
                    <button type="submit" class="btn btn-primary mt-2">Commenter</button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
</div>
</body>
</html>
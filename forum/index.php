<?php
// Connexion à la base de données
try {
    $dsn = 'mysql:host=localhost;dbname=easylegal;charset=utf8';
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

// Récupérer les commentaires
$commentaires = $pdo->query("SELECT * FROM commentaire ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

// Ajouter un post (anonyme ou réel)
if (isset($_POST['contenu'])) {
    $contenu = $_POST['contenu'];
    $anonyme = isset($_POST['anonyme']) ? 1 : 0; // 1 = Anonyme, 0 = Utilisateur réel
    $pdo->prepare("INSERT INTO forum (contenu, anonyme) VALUES (?, ?)")->execute([$contenu, $anonyme]);
    header("Location: index.php");
    exit;
}

// Gérer les likes et dislikes
if (isset($_POST['id_forum'])) {
    $id_forum = $_POST['id_forum'];
    $id_personne = 1; // À modifier selon l'authentification

    if (isset($_POST['like'])) {
        // Vérifier si l'utilisateur a déjà liké ce post
        $check = $pdo->prepare("SELECT * FROM aime WHERE id_forum = ? AND id_personne = ?");
        $check->execute([$id_forum, $id_personne]);
        
        if ($check->rowCount() == 0) {
            $pdo->prepare("INSERT INTO aime (id_forum, id_personne) VALUES (?, ?)")->execute([$id_forum, $id_personne]);
        }
    } elseif (isset($_POST['dislike'])) {
        // Supprimer le like si déjà existant
        $pdo->prepare("DELETE FROM aime WHERE id_forum = ? AND id_personne = ?")->execute([$id_forum, $id_personne]);
    }
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

        function dislikePost(id) {
            fetch('index.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'dislike=1&id_forum=' + id
            })
            .then(response => response.json())
            .then(data => { if (data.success) location.reload(); });
        }
    </script>
</head>
<body>
<nav class="navbar navbar-light bg-light">
    <a class="navbar-brand" href="#">Forum Juridique</a>
</nav>
<div class="container">
    <h1>Forum de Discussion</h1>
    <form method="POST" class="mb-3">
        <textarea name="contenu" class="form-control" placeholder="Écrivez votre message..." required></textarea>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="anonyme" id="anonyme">
            <label class="form-check-label" for="anonyme">Poster en anonyme</label>
        </div>
        <button type="submit" class="btn btn-warning w-100 mt-2">Publier</button>
    </form>
    <?php foreach ($forums as $forum): ?>
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title"><?= $forum['anonyme'] ? 'Anonyme' : 'Utilisateur ' . $forum['id'] ?></h5>
                <p class="card-text"><?= htmlspecialchars($forum['contenu']) ?></p>
                <button onclick="likePost(<?= $forum['id'] ?>)">👍 <?= $forum['likes'] ?></button>
                <button onclick="dislikePost(<?= $forum['id'] ?>)">👎</button>
                <div class="mt-3">
                    <strong>Commentaires :</strong>
                    <?php foreach ($commentaires as $commentaire): ?>
                        <?php if ($commentaire['id_forum'] == $forum['id']): ?>
                            <p>- <?= htmlspecialchars($commentaire['contenu']) ?></p>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <form method="POST">
                        <input type="hidden" name="id_forum" value="<?= $forum['id'] ?>">
                        <textarea name="commentaire" class="form-control" placeholder="Ajouter un commentaire..." required></textarea>
                        <button type="submit" class="btn btn-primary mt-2">Commenter</button>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
</body>
</html>

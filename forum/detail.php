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

// Vérifier si un ID de forum est passé
if (!isset($_GET['id'])) {
    die("Aucun message sélectionné.");
}

$id_forum = (int)$_GET['id'];

// Récupérer le post sélectionné
$stmt = $pdo->prepare("SELECT * FROM forum WHERE id = ?");
$stmt->execute([$id_forum]);
$forum = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$forum) {
    die("Message introuvable.");
}

// Récupérer les commentaires associés
$commentaires = $pdo->prepare("SELECT * FROM commentaire WHERE id_forum = ? ORDER BY id DESC");
$commentaires->execute([$id_forum]);
$commentaires = $commentaires->fetchAll(PDO::FETCH_ASSOC);

// Ajouter un commentaire
if (isset($_POST['commentaire'])) {
    $commentaire = $_POST['commentaire'];
    $pdo->prepare("INSERT INTO commentaire (contenu, id_forum, id_personne) VALUES (?, ?, 1)")
        ->execute([$commentaire, $id_forum]);
    header("Location: discussion.php?id=$id_forum");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Discussion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 800px;
            margin: auto;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .btn {
            border-radius: 5px;
        }
    </style>
    <script>
        function likeComment(id) {
            fetch('like.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'id_commentaire=' + id
            })
            .then(response => response.json())
            .then(data => { if (data.success) location.reload(); });
        }
    </script>
</head>
<body>
<div class="container">
    <h1>Discussion</h1>
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title"><?= $forum['anonyme'] ? 'Anonyme' : 'Utilisateur ' . $forum['id'] ?></h5>
            <p class="card-text"><?= htmlspecialchars($forum['contenu']) ?></p>
        </div>
    </div>

    <h3>Réponses :</h3>
    <?php foreach ($commentaires as $commentaire): ?>
        <div class="card mb-2">
            <div class="card-body">
                <p><?= htmlspecialchars($commentaire['contenu']) ?></p>
                <button onclick="likeComment(<?= $commentaire['id'] ?>)">👍</button>
            </div>
        </div>
    <?php endforeach; ?>
    
    <form method="POST">
        <textarea name="commentaire" class="form-control" placeholder="Ajouter un commentaire..." required></textarea>
        <button type="submit" class="btn btn-primary mt-2">Répondre</button>
    </form>
    
    <a href="index.php" class="btn btn-secondary mt-3">Retour au forum</a>
</div>
</body>
</html>
<?php
// Connexion à la base de données
try {
    $dsn = 'mysql:host=localhost;dbname=easylegal;charset=utf8';
    $username = 'root'; // Modifier avec vos identifiants
    $password = '';
    $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}


// Récupérer les posts
$forums = $pdo->query("SELECT * FROM forum ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

// Ajouter un post
if (isset($_POST['contenu'])) {
    $contenu = $_POST['contenu'];
    $pdo->prepare("INSERT INTO forum (contenu) VALUES (?)")->execute([$contenu]);
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
            background-color: #eef2f5;
            font-family: 'Arial', sans-serif;
        }
        .container {
            max-width: 800px;
            margin-top: 40px;
        }
        .forum-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .forum-card {
            background: #ffffff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease-in-out;
        }
        .forum-card:hover {
            transform: scale(1.02);
        }
        .btn-like {
            color: #e74c3c;
            cursor: pointer;
            border: none;
            background: transparent;
            font-size: 16px;
            transition: color 0.2s;
        }
        .btn-like:hover {
            color: #c0392b;
        }
        .post-form textarea {
            resize: none;
            border-radius: 8px;
        }
        .post-form button {
            background: #007bff;
            color: white;
            font-size: 16px;
            border-radius: 8px;
            transition: background 0.3s;
        }
        .post-form button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="forum-header">Forum Juridique</h1>
        <div class="card p-4 mb-4 post-form">
            <form method="POST">
                <div class="mb-3">
                    <textarea name="contenu" class="form-control" placeholder="Écrivez votre message..." required></textarea>
                </div>
                <button type="submit" class="btn w-100">Publier</button>
            </form>
        </div>

        <h2>Discussions</h2>
        <?php foreach ($forums as $forum): ?>
            <div class="forum-card mb-3 p-3">
                <p><?= htmlspecialchars($forum['contenu']) ?></p>
                <button class="btn-like" data-id="<?= $forum['id'] ?>">👍 J'aime</button>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
        document.querySelectorAll('.btn-like').forEach(button => {
            button.addEventListener('click', function() {
                let forumId = this.getAttribute('data-id');
                fetch('like.php?id=' + forumId)
                    .then(response => response.text())
                    .then(data => alert(data));
            });
        });
    </script>
</body>
</html>


    
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

        nav a img {
            width: 50px;
            height: auto;
        }

        nav span {
            display: flex;
            gap: 20px;
        }

        nav span a {
            text-decoration: none;
            color: #000;
            font-weight: bold;
            font-size: 16px;
        }
        .container {
            max-width: 800px;
            margin-top: 40px;
        }
        .forum-header {
            font-weight: bold;
            font-size: 24px;
            margin-bottom: 20px;
        }
        .forum-card {
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow:#e67e22;
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
            cursor: pointer;
        }
        .forum-card:hover {
            transform: translateY(-5px); /* Déplace vers le haut de 5px */
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2); /* Ajoute une ombre plus marquée */
         }

        .forum-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e67e22;
        }
        .forum-content {
            flex: 1;
        }
        .forum-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .btn-like {
            color: #e67e22;
            cursor: pointer;
            font-size: 14px;
        }
        .post-form {
            background:transparent;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }
        .post-form button {
            background: #ff8c00; /* Orange */
            color: white;
            font-size: 16px;
            border-radius: 8px;
            transition: background 0.3s, transform 0.2s ease-in-out;
            border: none;
            padding: 10px;
        }

        .post-form button:hover {
            background: #e67e00; /* Orange plus foncé */
            transform: scale(1.05); 
        }
        
    </style>
</head>
<body>
<!-- Barre de navigation -->
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
        <h3 class="forum-header">demandez, discutez et partagez votre avis sur notre forum juridique </h3>

        <!-- Zone des discussions -->
        <?php foreach ($forums as $forum): ?>
            <div class="forum-card">
                <div class="forum-avatar"></div>
                <div class="forum-content">
                    <strong>Anonyme <?= $forum['id'] ?></strong>
                    <p><?= htmlspecialchars($forum['contenu']) ?></p>
                </div>
                <div class="forum-actions">
                    <span class="btn-like">👍 </span>
                    <span class="btn-like">💬 </span>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- Formulaire de publication -->
        <div class="post-form">
            <form method="POST">
                <div class="mb-3">
                    <textarea name="contenu" class="form-control" placeholder="Écrivez votre message..." required></textarea>
                </div>
                <button type="submit" class="btn btn-warning w-100">Publier</button>
            </form>
        </div>
    </div>
</body>
</html>

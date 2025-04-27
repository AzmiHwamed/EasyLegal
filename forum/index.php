<?php
session_start();
include('../dbconfig/index.php');
include('../validateur.php');
isAuthentiacted();

// Connexion à la base de données
$mysqli = new mysqli("localhost", "root", "", "easylegal");
if ($mysqli->connect_error) {
    die("Connexion échouée : " . $mysqli->connect_error);
}

// Ajouter un post
if (isset($_POST['contenu'])) {
    $contenu = $mysqli->real_escape_string($_POST['contenu']);
    $anonyme = isset($_POST['anonyme']) ? 1 : 0;
    $id_personne = $_SESSION['id'];

    $stmt = $mysqli->prepare("INSERT INTO forum (contenu, anonyme, id_personne) VALUES (?, ?, ?)");
    $stmt->bind_param("sii", $contenu, $anonyme, $id_personne);
    $stmt->execute();
    $stmt->close();

    header("Location: index.php");
    exit;
}

// Gérer les likes (requête AJAX)


// Récupérer les posts
$sql = "
    SELECT forum.*, personne.nom, 
           (SELECT COUNT(*) FROM aime WHERE aime.id_forum = forum.id) AS likes
    FROM forum
    JOIN personne ON forum.id_personne = personne.id
    ORDER BY forum.id DESC
";

$result = $mysqli->query($sql);
$forums = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $forums[] = $row;
    }
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Forum</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #F8F4ED;
        }
        .card {
            border-radius: 8px;
            background-color: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .like-btn {
            color: red;
            cursor: pointer;
        }
        .question {
            background-color: #fce6b6;
            padding: 15px;
            border-radius: 8px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<?php include('../nav/index.php'); ?>

<div class="container mt-4">
    <h1 class="mb-3">Forum</h1>

    <!-- Formulaire -->
    <form method="POST" class="mb-4">
        <textarea name="contenu" class="form-control" placeholder="Écrivez votre message..." required></textarea>
        <div class="form-check mt-2">
            <input class="form-check-input" type="checkbox" name="anonyme" id="anonyme">
            <label class="form-check-label" for="anonyme">Poster en anonyme</label>
        </div>
        <button type="submit" class="btn btn-warning w-100 mt-3">Publier</button>
    </form>

    <!-- Liste des posts -->
    <?php foreach ($forums as $forum): ?>
        <div class="card mb-3 p-3">
            <div class="card-body">
                <h5 class="card-title"><?= $forum['anonyme'] ? 'Anonyme' : htmlspecialchars($forum['nom']) ?></h5>
                <a href="detail.php?id_forum=<?= $forum['id'] ?>">
                    <p class="card-text question"><?= htmlspecialchars($forum['contenu']) ?></p>
                </a>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <span class="like-btn" data-id="<?= $forum['id'] ?>">❤️ <?= $forum['likes'] ?></span>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- jQuery pour Ajax -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function(){
    $(".like-btn").click(function(){
        const id_forum = $(this).data("id");
        const likeBtn = $(this);

        $.ajax({
            type: "POST",
            url: "like.php",
            data: {
                id_forum: id_forum,
                like: true
            },
            success: function(response){
                const data = JSON.parse(response);
                if (data.success) {
                    likeBtn.html('❤️ ' + data.likes);
                }
            }
        });
    });
});
</script>

</body>
</html>

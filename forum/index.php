<?php
include('../dbconfig/index.php');
include('../validateur.php');
isAuthentiacted();

// Connexion à la base de données
$mysqli = new mysqli("localhost", "root", "", "easylegal");
if ($mysqli->connect_error) {
    die("Connexion échouée : " . $mysqli->connect_error);
}

// Récupérer les posts avec le nombre de likes
$sql = "
    SELECT forum.*, personne.nom, 
           (SELECT COUNT(*) FROM aime WHERE aime.id_forum = forum.id) AS likes
    FROM forum
    JOIN personne ON forum.id_personne = personne.id
    ORDER BY id DESC
";

$result = $mysqli->query($sql);
$forums = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $forums[] = $row;
    }
}

// Ajouter un post
if (isset($_POST['contenu'])) {
    $contenu = $mysqli->real_escape_string($_POST['contenu']);
    $anonyme = isset($_POST['anonyme']) ? 1 : 0;
    $id_personne = $_SESSION['id']; // Id de l'utilisateur connecté

    $stmt = $mysqli->prepare("INSERT INTO forum (contenu, anonyme, id_personne) VALUES (?, ?, ?)");
    $stmt->bind_param("sii", $contenu, $anonyme, $id_personne);
    $stmt->execute();
    $stmt->close();

    header("Location: index.php");
    exit;
}

// Gérer les likes (AJAX)
if (isset($_POST['id_forum']) && isset($_POST['like'])) {
    $id_forum = (int) $_POST['id_forum'];
    $id_personne = 1; // À modifier pour l'utilisateur connecté

    // Vérifier si l'utilisateur a déjà aimé ce post
    $check = $mysqli->prepare("SELECT * FROM aime WHERE id_forum = ? AND id_personne = ?");
    $check->bind_param("ii", $id_forum, $id_personne);
    $check->execute();
    $check_result = $check->get_result();

    if ($check_result->num_rows == 0) {
        // Ajouter un like
        $insert = $mysqli->prepare("INSERT INTO aime (id_forum, id_personne) VALUES (?, ?)");
        $insert->bind_param("ii", $id_forum, $id_personne);
        $insert->execute();
        $insert->close();
    } else {
        // Retirer un like
        $delete = $mysqli->prepare("DELETE FROM aime WHERE id_forum = ? AND id_personne = ?");
        $delete->bind_param("ii", $id_forum, $id_personne);
        $delete->execute();
        $delete->close();
    }

    // Retourner le nouveau nombre de likes
    $likes = $mysqli->prepare("SELECT COUNT(*) FROM aime WHERE id_forum = ?");
    $likes->bind_param("i", $id_forum);
    $likes->execute();
    $likes_result = $likes->get_result();
    $likes_count = $likes_result->fetch_row()[0];

    echo json_encode(["success" => true, "likes" => $likes_count]);
    exit;
}

// Fermer la connexion à la base de données
$mysqli->close();
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        .expert-response {
            background-color: #d9c9a5;
            padding: 15px;
            border-radius: 8px;
            
        }

    </style>
</head>
<body>

<?php include('../nav/index.php'); ?>

<div class="container mt-4">
    <h1 class="mb-3">Forum de Discussion</h1>
    <form method="POST" class="mb-3">
        <textarea name="contenu" class="form-control" placeholder="Écrivez votre message..." required></textarea>
        <div class="form-check mt-2">
            <input class="form-check-input" type="checkbox" name="anonyme" id="anonyme">
            <label class="form-check-label" for="anonyme">Poster en anonyme</label>
        </div>
        <button type="submit" class="btn btn-warning w-100 mt-2">Publier</button>
    </form>

    <?php foreach ($forums as $forum): ?>
        
        <div class="card mb-3 p-3">
            <div class="card-body">
                <h5 class="card-title"><?= $forum['anonyme'] ? 'Anonyme' : $forum['nom'] ?></h5>
                <a href=<?php echo"./detail.php?id_forum=".$forum['id'] ?>>

                <p class="card-text question"><?= htmlspecialchars($forum['contenu']) ?></p>    </a>

                <span class="like-btn" data-id="<?= $forum['id'] ?>">❤️ J'aime (<span class="like-count"><?= $forum['likes'] ?></span>)</span>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".like-btn").forEach(button => {
        button.addEventListener("click", function () {
            let id_forum = this.getAttribute("data-id");
            let likeCount = this.querySelector(".like-count");

            fetch("index.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: new URLSearchParams({ id_forum: id_forum, like: 1 })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    likeCount.textContent = data.likes;
                }
            })
            .catch(error => console.error("Erreur:", error));
        });
    });
});
</script>

</body>
</html>

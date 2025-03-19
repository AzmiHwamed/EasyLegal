<?php
session_start();
include('../dbconfig/index.php'); // Vérifiez que la connexion est bien établie

<<<<<<< HEAD
if (isset($_GET['msg'])) {
    $stmt = $conn->prepare("INSERT INTO commentaire(contenu,id_personne,id_forum) values (? , ? , ?)");
    $stmt->bind_param('sss', $_GET['msg'],$_SESSION['id'],$_GET['id_forum']);
    $stmt->execute();
    header('Location: ../forum/detail.php?id_forum=' . $_GET['id_forum']);
    exit(); 
        
}
if (isset($_GET['id_forum'])) {
    $stmt = $conn->prepare("SELECT * FROM forum WHERE id = ?");
    $stmt->bind_param('s', $_GET['id_forum']);
    $stmt->execute();
    $result = $stmt->get_result();
    $forum = $result->fetch_object();
    $stmt = $conn->prepare("SELECT * FROM personne WHERE id = ?");
    $stmt->bind_param('s', $forum->id_personne);
    $stmt->execute();
    $result = $stmt->get_result();
    $personne = $result->fetch_object();

=======
if (isset($_POST['id_forum']) && isset($_POST['like'])) {
    $id_forum = $_POST['id_forum'];
    $id_personne = $_SESSION['id'];

    // Vérifier si l'utilisateur a déjà aimé ce forum
    $check_like = $conn->prepare("
        SELECT * FROM aime WHERE id_forum = ? AND id_personne = ?
    ");
    $check_like->bind_param("ii", $id_forum, $id_personne);
    $check_like->execute();
    $result = $check_like->get_result();

    if ($result->num_rows == 0) {
        // Si l'utilisateur n'a pas encore aimé ce forum, on insère un like
        $insert_like = $conn->prepare("
            INSERT INTO aime (id_forum, id_personne) VALUES (?, ?)
        ");
        $insert_like->bind_param("ii", $id_forum, $id_personne);
        $insert_like->execute();
    }
>>>>>>> cf276182d18eb2cbaca8bb6c71074d69f849eef3
}

// Récupérer les forums avec le nombre de likes associés et les commentaires
$forums = $conn->prepare("
    SELECT forum.*, 
           (SELECT COUNT(*) FROM aime WHERE aime.id_forum = forum.id) as likes
    FROM forum 
    ORDER BY id DESC
");
$forums->execute();
$result_forums = $forums->get_result();
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
            position: absolute;
            top: 20px;
            left: 20px;
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
        <a href="#"><img src="../assets/logo.png" alt="Icône de la justice"></a>
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
            <!-- Question -->
            <div class="question">
<<<<<<< HEAD
                <p>La Question de Madame <?php 
                if($forum->anonyme){echo "Anonyme ";}
                 else{
                 echo $personne->nom ;
                 }?></p>
                <p><?php echo $forum->contenu ?></p>
                   <a  class="btn-retour" href="javascript:history.back()" >⬅</a>
                
            </div>


            <?php
// Assuming $conn is already defined and connected

$stmt2 = $conn->prepare("SELECT * FROM commentaire WHERE id_forum = ?");
$stmt2->bind_param('s', $_GET['id_forum']);
$stmt2->execute();
$result2 = $stmt2->get_result();
while ($row = $result2->fetch_assoc()) {
    $stmt1 = $conn->prepare("SELECT * FROM personne WHERE id = ?");
    $stmt1->bind_param('s', $row['id_personne']);
    $stmt1->execute();
    $result1 = $stmt1->get_result();
    $personne1 = $result1->fetch_object();

    if ($personne1 && $personne1->role == "user") {
        echo "<div class='response'>
                <p><strong>La Réponse de Madame ".$personne1->nom."</strong></p>
                <p>".$row['contenu']."</p>
                <span class='like-btn'>❤️ J'aime</span>
              </div>";
    } else {
        echo "<div class='response expert-response'>
                <p><strong>La Réponse de Expert ".$personne1->nom.":</strong></p>
                <p>".$row['contenu']."</p>
                <span class='star'>⭐</span>
                <span class='like-btn'>❤️ J'aime</span>
              </div>";
    }

    // Close inner statement
    $stmt1->close();
}

// Close outer statement
$stmt2->close();
?>


            <form class="comment-box" method="get">
                <input type="text" name="msg"  placeholder="Ecrivez votre commentaire...">
                <input type="text" value=<?php echo $_GET['id_forum'] ?> name="id_forum" style="display:none">
                <button type="sumbit" class="send-btn">Envoyer</button>
            </form>
=======
                <p>La Question de Madame Anonyme 1257:</p>
                <p>Comment faire lorsque je veux faire une chose légale s'il vous plaît...</p>
                <a class="btn-retour" href="javascript:history.back()">⬅</a>
            </div>

            <!-- Formulaire de commentaire -->
            <div class="comment-box">
                <form method="POST" action="">
                    <input type="text" name="commentaire" placeholder="Ecrivez votre commentaire..." required>
                    <input type="hidden" name="id_forum" value="<?php echo isset($id_forum) ? $id_forum : ''; ?>"> <!-- ID du forum -->
                    <button class="send-btn" type="submit">Envoyer</button>
                </form>
            </div>
>>>>>>> cf276182d18eb2cbaca8bb6c71074d69f849eef3
        </div>
    </div>

</body>
</html>

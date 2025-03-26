<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "easylegal";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

$message = "";

// Vérifier si une suppression a été demandée
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $sql = "DELETE FROM forum WHERE id = $id";
    
    if ($conn->query($sql)) {
        $message = "Post supprimé avec succès.";
    } else {
        $message = "Erreur lors de la suppression du post.";
    }
} else {
    $message = "ID invalide.";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suppression de Post</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 400px;
        }
        h2 {
            color: #333;
        }
        .message {
            background: #dff0d8;
            color: #3c763d;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            display: <?php echo empty($message) ? 'none' : 'block'; ?>;
        }
        .btn {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .btn:hover {
            background-color: #c0392b;
        }
        .back {
            display: block;
            margin-top: 15px;
            color: #3498db;
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Suppression de Post</h2>
    
    <div class="message"><?php echo $message; ?></div>

    <button class="btn" onclick="confirmDeletion()">Confirmer la suppression</button>
    <a href="index.php" class="back">Retour au forum</a>
</div>

<script>
    function confirmDeletion() {
        if (confirm("Êtes-vous sûr de vouloir supprimer ce post ?")) {
            document.querySelector('.btn').disabled = true;
            window.location.href = "index.php";
        }
    }
</script>

</body>
</html>

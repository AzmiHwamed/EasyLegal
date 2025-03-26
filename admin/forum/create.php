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

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['contenu'])) {
    $contenu = $conn->real_escape_string($_POST['contenu']);
    $sql = "INSERT INTO forum (contenu) VALUES ('$contenu')";
    
    if ($conn->query($sql)) {
        $message = "Post ajouté avec succès !";
    } else {
        $message = "Erreur lors de l'ajout du post.";
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $message = "Veuillez entrer un message valide.";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Post</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        /* Styles généraux */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #ecf0f1;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 400px;
            max-width: 90%;
        }

        h2 {
            color: #333;
            margin-bottom: 15px;
            font-weight: 600;
        }

        /* Message de confirmation ou d'erreur */
        .message {
            background: #dff0d8;
            color: #3c763d;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 15px;
            display: <?php echo empty($message) ? 'none' : 'block'; ?>;
            font-size: 14px;
            text-align: center;
        }

        .message.error {
            background: #f2dede;
            color: #a94442;
        }

        /* Styles du textarea */
        textarea {
            width: 100%;
            height: 120px;
            padding: 15px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            resize: none;
            box-sizing: border-box;
            margin-bottom: 20px;
            transition: border-color 0.3s;
        }

        textarea:focus {
            border-color: #3498db;
            outline: none;
        }

        /* Styles du bouton publier */
        .btn {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease, transform 0.2s;
        }

        .btn:hover {
            background-color: #2980b9;
            transform: scale(1.05);
        }

        /* Message d'erreur */
        .error {
            color: red;
            font-size: 14px;
            display: none;
        }

        .back {
            display: block;
            margin-top: 20px;
            color: #3498db;
            text-decoration: none;
            font-size: 16px;
        }

        .back:hover {
            text-decoration: underline;
        }

    </style>
</head>
<body>

<div class="container">
    <h2>Ajouter un Post</h2>
    
    <!-- Message de retour -->
    <div class="message <?php echo (empty($message) || strpos($message, 'Erreur') !== false) ? 'error' : ''; ?>">
        <?php echo $message; ?>
    </div>

    <!-- Formulaire -->
    <form method="post" onsubmit="return validateForm()">
        <textarea name="contenu" id="contenu" placeholder="Écrire votre message..." required></textarea>
        <div class="error" id="error-message">Veuillez entrer du texte.</div>
        <button type="submit" class="btn">Publier</button>
    </form>
    
    <a href="index.php" class="back">Retour au forum</a>
</div>

<script>
    // Validation du formulaire
    function validateForm() {
        let contenu = document.getElementById('contenu').value.trim();
        let errorMessage = document.getElementById('error-message');

        // Si le champ est vide, afficher le message d'erreur
        if (contenu === "") {
            errorMessage.style.display = "block";
            return false;
        }
        errorMessage.style.display = "none";
        return true;
    }
</script>

</body>
</html>

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
    // Utilisation de requête préparée pour plus de sécurité
    $stmt = $conn->prepare("INSERT INTO forum (contenu) VALUES (?)");
    $stmt->bind_param("s", $contenu);
    
    if ($stmt->execute()) {
        $message = "Post ajouté avec succès !";
    } else {
        $message = "Erreur lors de l'ajout du post.";
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $message = "Veuillez entrer un message valide.";
}

$nom_utilisateur = isset($_SESSION['nom_utilisateur']) ? $_SESSION['nom_utilisateur'] : "Utilisateur"; // Exemple d'initialisation de la variable
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

        /* Sidebar */
        .sidebar {
            width: 280px;
            background-color: #34495e;
            color: white;
            height: 100%;
            position: fixed;
            top: 0;
            left: 0;
            padding: 40px 30px;
            box-shadow: 2px 0 20px rgba(0, 0, 0, 0.7);
        }

        .sidebar h2 {
            margin-bottom: 40px;
            font-size: 24px;
            font-weight: 700;
            text-align: center;
            letter-spacing: 1px;
            color: #ecf0f1;
        }

        .sidebar nav ul {
            padding-left: 0;
            list-style: none;
        }

        .sidebar nav ul li {
            margin: 25px 0;
        }

        .sidebar nav ul li a {
            color: #ecf0f1;
            text-decoration: none;
            font-size: 18px;
            padding: 12px 20px;
            display: block;
            border-radius: 30px;
            transition: all 0.3s ease;
        }

        .sidebar nav ul li a:hover {
            background-color: #1abc9c;
            color: white;
            padding-left: 25px;
            transform: translateX(10px);
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
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
    <!-- Sidebar intégrée -->
    <div class="sidebar">
        <h2>Bienvenue, <?php echo htmlspecialchars($nom_utilisateur); ?> 👋</h2>
        <nav role="navigation">
            <ul>
                <li><a href="../user/index.php" onclick="return confirmNavigation(this.href);" aria-label="Gérer les utilisateurs">Gérer les utilisateurs</a></li>
                <li><a href="../forum/index.php" onclick="return confirmNavigation(this.href);" aria-label="Gérer le forum">Gérer le forum</a></li>
                <li><a href="../text/index.php" onclick="return confirmNavigation(this.href);" aria-label="Gérer les textes juridiques">Gérer les textes juridiques</a></li>
                <li><a href="../expert/index.php" onclick="return confirmNavigation(this.href);" aria-label="Gérer les experts">Gérer les experts</a></li>
                <li><a href="../adminnav/index.php">Accueil</a></li>

            </ul>
        </nav>
    </div>

    <!-- Contenu principal -->
    <div class="main-content">
        <h2>Ajouter un Post</h2>
        <div class="message <?php echo (empty($message) || strpos($message, 'Erreur') !== false) ? 'error' : ''; ?>">
            <?php echo $message; ?>
        </div>

        <form method="post" onsubmit="return validateForm()">
            <textarea name="contenu" id="contenu" placeholder="Écrire votre message..." required></textarea>
            <div class="error" id="error-message">Veuillez entrer du texte.</div>
            <button type="submit" class="btn">Publier</button>
        </form>

        <a href="index.php" class="back">Retour au forum</a>
    </div>

    <script>
        function validateForm() {
            let contenu = document.getElementById('contenu').value.trim();
            let errorMessage = document.getElementById('error-message');

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

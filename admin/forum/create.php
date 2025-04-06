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
    /* Réinitialisation + police moderne */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Poppins', 'Arial', sans-serif;
    }

    body {
        background-color: #f4f6f9;
        display: flex;
        min-height: 100vh;
        color: #333;
        justify-content: center;
        align-items: center;
        margin: 0;
    }

    /* Sidebar */
    .sidebar {
        width: 260px;
        background: linear-gradient(135deg, #e89d3f, #e8a043);
        color: white;
        height: 100%;
        position: fixed;
        top: 0;
        left: 0;
        padding: 40px 25px;
        box-shadow: 4px 0 15px rgba(0, 0, 0, 0.1);
        transition: width 0.3s ease;
    }

    .sidebar h2 {
        margin-bottom: 35px;
        font-size: 22px;
        font-weight: 600;
        text-align: center;
        color: #fffdd0;
        letter-spacing: 1px;
    }

    .sidebar nav ul {
        list-style: none;
        padding-left: 0;
    }

    .sidebar nav ul li {
        margin: 20px 0;
    }

    .sidebar nav ul li a {
        color: #fff;
        text-decoration: none;
        padding: 12px 18px;
        display: block;
        font-size: 16px;
        border-radius: 8px;
        transition: background 0.3s, padding-left 0.3s;
    }

    .sidebar nav ul li a:hover {
        background-color: rgba(255, 255, 255, 0.15);
        padding-left: 28px;
    }

    /* Conteneur principal */
    .main-content {
        margin-left: 280px;
        padding: 40px;
        width: calc(100% - 280px);
        background-color: #fff;
        min-height: 100vh;
        transition: margin-left 0.3s ease;
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
        font-size: 14px;
        text-align: center;
        display: <?php echo empty($message) ? 'none' : 'block'; ?>;
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

    /* Responsivité */
    @media screen and (max-width: 768px) {
        .sidebar {
            position: relative;
            width: 100%;
            height: auto;
            box-shadow: none;
            padding: 20px;
        }

        .main-content {
            margin-left: 0;
            width: 100%;
            padding: 20px;
        }
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

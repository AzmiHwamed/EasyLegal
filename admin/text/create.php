<?php
session_start();

// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "easylegal";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Vérifier si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérification des champs avant l'insertion
    $date = !empty($_POST['Date']) ? $_POST['Date'] : null;
    $contenu = !empty($_POST['Contenu']) ? $_POST['Contenu'] : null;
    $theme = !empty($_POST['Theme']) ? $_POST['Theme'] : null;
    $type = !empty($_POST['Type']) ? $_POST['Type'] : null;
    $titre = !empty($_POST['Titre']) ? $_POST['Titre'] : null;

    if ($date && $contenu && $theme && $type && $titre) {
        // Préparer et exécuter la requête d'insertion
        $sql = "INSERT INTO textjuridique (Date, Contenu, Theme, Type, Titre) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $date, $contenu, $theme, $type, $titre);

        if ($stmt->execute()) {
            echo "<script>alert('Texte juridique ajouté avec succès.'); window.location.href = 'index.php';</script>";
        } else {
            echo "<script>alert('Erreur lors de l\'ajout du texte juridique.'); window.location.href = 'create.php';</script>";
        }
    } else {
        echo "<script>alert('Tous les champs doivent être remplis.'); window.location.href = 'create.php';</script>";
    }
}

// Fermer la connexion à la base de données
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Texte Juridique</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <style>
      /* Réinitialisation et styles globaux */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', 'Arial', sans-serif;
}

body {
    background-color: #f4f6f9;
    display: flex;
    min-height: 100vh;
    overflow-x: hidden;
    color: #333;
}

/* Barre latérale */
.sidebar {
    width: 260px;
    background: linear-gradient(135deg, #e89d3f, #e8a043);
    color: white;
    height: 100vh;
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
    padding: 0;
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

/* Contenu principal */
.main-content {
    margin-left: 260px;
    padding: 40px;
    width: calc(100% - 260px);
    background-color: #fff;
    min-height: 100vh;
    transition: margin-left 0.3s ease;
}

.main-content h1 {
    font-size: 34px;
    margin-bottom: 20px;
    font-weight: 600;
    color: #222;
}

/* Responsive design */
@media screen and (max-width: 768px) {
    .sidebar {
        width: 100%;
        height: auto;
        position: relative;
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
    

    <!-- Sidebar -->
    <div class="sidebar">
    <?php

$nom_utilisateur = isset($_SESSION['nom']) ? $_SESSION['nom'] : 'Admin';
?>
<h2>Bienvenue, <?php echo htmlspecialchars($nom_utilisateur); ?> 👋</h2>    <nav role="navigation">
            <ul>
                <li><a href="../user/index.php" onclick="return confirmNavigation(this.href);" aria-label="Gérer les utilisateurs">Gérer les utilisateurs</a></li>
                <li><a href="../forum/index.php" onclick="return confirmNavigation(this.href);" aria-label="Gérer le forum">Gérer le forum</a></li>
                <li><a href="../text/index.php" onclick="return confirmNavigation(this.href);" aria-label="Gérer les textes juridiques">Gérer les textes juridiques</a></li>
                <li><a href="../expert/index.php" onclick="return confirmNavigation(this.href);" aria-label="Gérer les experts">Gérer les experts</a></li>
                <li><a href="../adminnav/index.php">Accueil</a></li>

            </ul>
        </nav>
    </div>

    <!-- Main content -->
    <div class="main-content">
        <h1>Ajouter un Texte Juridique</h1>

        <div class="card mt-3">
            <div class="card-header">Formulaire d'Ajout</div>
            <div class="card-body">
                <form action="create.php" method="POST">
                    <div class="mb-3">
                        <label for="Date" class="form-label">Date</label>
                        <input type="date" id="Date" name="Date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="Contenu" class="form-label">Contenu</label>
                        <textarea id="Contenu" name="Contenu" class="form-control" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="Theme" class="form-label">Thème</label>
                        <input type="text" id="Theme" name="Theme" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="Type" class="form-label">Type</label>
                        <input type="text" id="Type" name="Type" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="Titre" class="form-label">Titre</label>
                        <input type="text" id="Titre" name="Titre" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Ajouter</button>
                    <a href="index.php" class="btn btn-secondary">Retour à la Liste</a>
                </form>
            </div>
        </div>
    </div>

    <script>
        function confirmNavigation(url) {
            if (confirm("Voulez-vous vraiment accéder à cette page ?")) {
                window.location.href = url;
            }
            return false;
        }
    </script>
</body>
</html>

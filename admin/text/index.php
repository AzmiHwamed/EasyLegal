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

// Récupérer les textes juridiques
$result_textes = $conn->query("SELECT * FROM textjuridique LIMIT 50");
$textes_juridiques = $result_textes->fetch_all(MYSQLI_ASSOC);

// Vérification de la session utilisateur
$nom_utilisateur = isset($_SESSION['nom']) ? $_SESSION['nom'] : "Admin";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Textes Juridiques</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    
    <!-- CSS personnalisé -->
    <style>
      * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: 'Arial', sans-serif;
    background-color: #f4f6f9;
    display: flex;
    min-height: 100vh;
    overflow-x: hidden;
    color: #333;
}

/* Sidebar */
.sidebar {
    width: 280px;
    background-color: #34495e;
    color: white;
    height: 100vh;
    position: fixed;
    top: 0;
    left: 0;
    padding: 40px 30px;
    box-shadow: 2px 0 20px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease-in-out;
}

.sidebar h2 {
    margin-bottom: 40px;
    font-size: 24px;
    font-weight: 700;
    text-align: center;
    letter-spacing: 1px;
    color: #ecf0f1;
    text-transform: uppercase;
}

/* Sidebar navigation */
.sidebar nav ul {
    list-style: none;
    padding: 0;
}

.sidebar nav ul li {
    margin: 20px 0;
}

.sidebar nav ul li a {
    color: #ecf0f1;
    text-decoration: none;
    font-size: 18px;
    padding: 12px 20px;
    display: block;
    border-radius: 25px;
    transition: background-color 0.3s, padding-left 0.3s, transform 0.3s;
}

.sidebar nav ul li a:hover {
    background-color: #1abc9c;
    color: white;
    padding-left: 25px;
    transform: translateX(10px);
    box-shadow: 2px 0 15px rgba(0, 0, 0, 0.1);
}

/* Main Content */
.main-content {
    margin-left: 280px;
    padding: 40px;
    width: calc(100% - 280px);
    background-color: #fff;
    min-height: 100vh;
    transition: margin-left 0.3s ease, padding 0.3s ease;
}

h1 {
    color: #2c3e50;
    font-size: 30px;
    margin-bottom: 20px;
    font-weight: 600;
    text-align: center;
    text-transform: uppercase;
}

/* Button and interactive elements */
button, .add-btn {
    background: #3498db;
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 16px;
    transition: transform 0.3s, background 0.3s;
}

button:hover, .add-btn:hover {
    background: #2980b9;
    transform: scale(1.05);
}

button:focus, .add-btn:focus {
    outline: none;
}

/* Post and Card styling */
.posts-container {
    margin-top: 20px;
    text-align: left;
}

.post {
    background: #f9fafc;
    padding: 15px;
    border-radius: 8px;
    margin-top: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.post:hover {
    transform: translateY(-3px);
}

.post p {
    margin: 0;
    color: #333;
    font-size: 14px;
    flex: 1;
    padding-right: 10px;
}

.delete-btn {
    background: #e74c3c;
    border: none;
    color: white;
    padding: 7px 12px;
    cursor: pointer;
    border-radius: 6px;
    transition: transform 0.3s, background 0.3s;
}

.delete-btn:hover {
    background: #c0392b;
    transform: scale(1.1);
}

/* Card shadows */
.card {
    box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
}

/* Typography */
h2, h3, p {
    font-family: 'Arial', sans-serif;
    line-height: 1.5;
}

/* Responsive Sidebar */
@media screen and (max-width: 1024px) {
    .sidebar {
        width: 250px;
    }
}

@media screen and (max-width: 768px) {
    .sidebar {
        width: 100%;
        position: relative;
        padding: 15px;
    }

    .main-content {
        margin-left: 0;
        padding: 20px;
    }
}


    </style>

    <!-- JavaScript personnalisé -->
    <script>
        // Fonction de confirmation avant suppression
        function confirmDelete(textId) {
            if (confirm("Êtes-vous sûr de vouloir supprimer ce texte juridique ?")) {
                window.location.href = "delete.php?id=" + textId;
            }
        }

        // Fonction de confirmation avant navigation
        function confirmNavigation(url) {
            if (confirm("Voulez-vous vraiment accéder à cette page ?")) {
                window.location.href = url;
            }
            return false;
        }
    </script>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Bienvenue, <?php echo htmlspecialchars($nom_utilisateur); ?> 👋</h2>
        <nav role="navigation">
            <ul>
                <li><a href="../user/index.php" onclick="return confirmNavigation(this.href);" aria-label="Gérer les utilisateurs">Gérer les utilisateurs</a></li>
                <li><a href="../forum/index.php" onclick="return confirmNavigation(this.href);" aria-label="Gérer le forum">Gérer le forum</a></li>
                <li><a href="../text/index.php" onclick="return confirmNavigation(this.href);" aria-label="Gérer les textes juridiques">Gérer les textes juridiques</a></li>
                <li><a href="../expert/index.php" onclick="return confirmNavigation(this.href);" aria-label="Gérer les experts">Gérer les experts</a></li>
            </ul>
        </nav>
    </div>

    <!-- Contenu principal -->
    <div class="main-content">
        <h1>Gestion des Textes Juridiques</h1>

        <!-- Affichage des messages d'erreur ou de succès -->
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['error_message'] ?>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <!-- Bouton Ajouter en dehors du tableau -->
        <div class="d-flex justify-content-end mb-3">
            <a href="create.php" class="btn btn-primary">Ajouter</a>
        </div>

        <!-- Liste des textes juridiques -->
        <div class="card mt-3">
            <div class="card-header">Liste des Textes Juridiques</div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Contenu</th>
                            <th>Thème</th>
                            <th>Type</th>
                            <th>Titre</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($textes_juridiques as $texte): ?>
                        <tr>
                            <td><?= htmlspecialchars($texte['id']) ?></td>
                            <td><?= htmlspecialchars($texte['Date']) ?></td>
                            <td><?= htmlspecialchars($texte['Contenu']) ?></td>
                            <td><?= htmlspecialchars($texte['Theme']) ?></td>
                            <td><?= htmlspecialchars($texte['Type']) ?></td>
                            <td><?= htmlspecialchars($texte['Titre']) ?></td>
                            <td>
                                <button type="button" onclick="confirmDelete(<?= htmlspecialchars($texte['id']) ?>)" class="btn btn-danger btn-sm">Supprimer</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>

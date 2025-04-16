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
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Segoe UI', 'Arial', sans-serif;
    }

    body {
        display: flex;
        background-color: #f4f6f9;
        color: #333;
        min-height: 100vh;
        overflow-x: hidden;
    }

    .sidebar {
        width: 260px;
        background: linear-gradient(135deg, #e89d3f, #e8a043);
        color: white;
        padding: 40px 25px;
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        box-shadow: 4px 0 15px rgba(0, 0, 0, 0.1);
        transition: width 0.3s ease;
    }

    .sidebar h2 {
        margin-bottom: 35px;
        font-size: 22px;
        font-weight: 600;
        text-align: center;
        color: #fffdd0;
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
        transition: all 0.3s ease;
    }

    .sidebar nav ul li a:hover {
        background-color: rgba(255, 255, 255, 0.15);
        padding-left: 28px;
    }

    .main-content {
        margin-left: 260px;
        padding: 40px;
        width: calc(100% - 260px);
        background-color: #fff;
        min-height: 100vh;
    }

    .main-content h1 {
        font-size: 34px;
        margin-bottom: 20px;
        font-weight: 600;
        color: #222;
    }

    /* Bouton Ajouter */
    .add-btn {
        background-color: #ff9800;
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 16px;
        transition: all 0.3s ease;
        display: inline-block;
        text-decoration: none;
    }

    .add-btn:hover {
        background-color: #e68900;
        transform: scale(1.05);
    }

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
        box-shadow: 0px 3px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s ease-in-out;
    }

    .post:hover {
        transform: translateY(-2px);
    }

    .post p {
        margin: 0;
        color: #333;
        font-size: 14px;
        flex: 1;
        padding-right: 10px;
    }

    /* Bouton Supprimer */
    .delete-btn {
        background-color: #ff9800;
        border: none;
        color: white;
        padding: 7px 12px;
        cursor: pointer;
        border-radius: 6px;
        transition: 0.3s ease;
    }

    .delete-btn:hover {
        background-color: #e68900;
        transform: scale(1.1);
    }

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

        .add-btn {
            width: 100%;
            text-align: center;
        }

        .post {
            flex-direction: column;
            align-items: flex-start;
        }
    }
    </style>

    <!-- JavaScript personnalisé -->
    <script>
        function confirmDelete(textId) {
            if (confirm("Êtes-vous sûr de vouloir supprimer ce texte juridique ?")) {
                window.location.href = "delete.php?id=" + textId;
            }
        }

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
                <li><a href="../adminnav/index.php">Accueil</a></li>
            </ul>
        </nav>
    </div>

    <!-- Contenu principal -->
    <div class="main-content">
        <h1>Gestion des Textes Juridiques</h1>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['error_message'] ?>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <!-- Bouton Ajouter -->
        <div class="d-flex justify-content-end mb-3">
            <a href="create.php" class="add-btn">Ajouter</a>
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
                                <button type="button" onclick="confirmDelete(<?= htmlspecialchars($texte['id']) ?>)" class="delete-btn">Supprimer</button>
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

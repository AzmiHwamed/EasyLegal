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

// Fonction pour supprimer un texte juridique
function supprimerTexte($id) {
    global $conn;
    $sql = "DELETE FROM textjuridique WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

// Gestion de la suppression du texte juridique
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer_texte']) && isset($_POST['id'])) {
    $id = intval($_POST['id']); // S'assurer que l'ID est un entier
    if (supprimerTexte($id)) {
        echo "<script>alert('Texte juridique supprimé avec succès.'); window.location.href = 'index.php';</script>";
    } else {
        echo "<script>alert('Erreur lors de la suppression du texte juridique.');</script>";
    }
}

// Récupérer le nom de l'utilisateur, ou afficher "Admin" par défaut
$nom_utilisateur = isset($_SESSION['nom']) ? $_SESSION['nom'] : "Admin";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supprimer text juridique</title>
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
            background-color: #f4f7fa;
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
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

        .main-content {
            margin-left: 280px;
            padding: 40px;
            width: calc(100% - 280px);
            background-color: #fff;
            min-height: 100vh;
        }

        h1 {
            color: #2c3e50;
            font-size: 28px;
            margin-bottom: 20px;
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

        .container {
            max-width: 1200px;
        }

        .card-header {
            background-color: #007bff;
            color: white;
            font-size: 1.2rem;
        }

        .card-body {
            background-color: white;
            padding: 20px;
        }

        table th, table td {
            text-align: center;
        }

        .btn-sm {
            margin-right: 5px;
        }

        .btn-danger {
            background-color: #e3342f;
            border-color: #e3342f;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
    </style>
</head>
<body>
    
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

    <div class="main-content">
        <h1>Supprimer un Texte Juridique</h1>

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
                            <th>Action</th>
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
                                <form method="POST" style="display:inline;" onsubmit="return confirmDelete();">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($texte['id']) ?>">
                                    <button type="submit" name="supprimer_texte" class="btn btn-danger btn-sm">Supprimer</button>
                                </form>
                                <a href="index.php" class="btn btn-primary btn-sm">Annuler</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
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

        // Fonction de confirmation avant suppression
        function confirmDelete() {
            return confirm("Êtes-vous sûr de vouloir supprimer ce texte juridique ?");
        }
    </script>
</body>
</html>

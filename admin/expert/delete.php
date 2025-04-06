<?php
session_start();
session_regenerate_id(true); // Sécurisation de la session

// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "easylegal";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Récupérer les experts uniquement
$result_experts = $conn->query("SELECT * FROM personne WHERE role = 'expert' LIMIT 50");
$experts = $result_experts->fetch_all(MYSQLI_ASSOC);

// Fonction pour supprimer un expert
function supprimerExpert($id) {
    global $conn;
    $sql = "DELETE FROM personne WHERE id = ? AND role = 'expert'";  // Vérifier que l'utilisateur a le rôle 'expert'
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

// Gestion de la suppression de l'expert
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer_utilisateur']) && isset($_POST['id'])) {
    $id = intval($_POST['id']); // S'assurer que l'ID est un entier
    if (supprimerExpert($id)) {
        echo "<script>alert('Expert supprimé avec succès.'); window.location.href = 'index.php';</script>";
    } else {
        echo "<script>alert('Erreur lors de la suppression de l\'expert.');</script>";
    }
}

// Vérification de la connexion de l'utilisateur
$nom_utilisateur = isset($_SESSION['nom']) ? $_SESSION['nom'] : "Admin";

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supprimer des experts</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    
    <!-- CSS personnalisé -->
    <style>
    /* Réinitialisation + police moderne */
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
    color: #fff;
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

/* Messages */
.message {
    background-color: #f2f2f2;
    color: #333;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 16px;
    text-align: center;
}

.message.success {
    background-color: #dff0d8;
    color: #3c763d;
}

.message.error {
    background-color: #f2dede;
    color: #a94442;
}

/* Boutons */
.btn {
    background-color: #3498db;
    color: white;
    padding: 12px 25px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s ease, transform 0.2s;
    border: none;
}

.btn:hover {
    background-color: #2980b9;
    transform: scale(1.05);
}

/* Lien de retour */
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

    <!-- JavaScript personnalisé -->
    <script>
        // Fonction de confirmation avant suppression
        function confirmDelete() {
            return confirm("Êtes-vous sûr de vouloir supprimer cet expert ?");
        }
    </script>
</head>
<body class="container mt-4">
    

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

    <!-- Main Content -->
    <div class="main-content">
        <h1>Supprimer des Experts</h1>

        <!-- Liste des experts -->
        <div class="card mt-3">
            <div class="card-header">Liste des Experts</div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($experts as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['id']) ?></td>
                            <td><?= htmlspecialchars($user['nom']) ?></td>
                            <td><?= htmlspecialchars($user['Email']) ?></td>
                            <td><?= htmlspecialchars($user['telephone']) ?></td>
                            <td>
                                <form method="POST" style="display:inline;" onsubmit="return confirmDelete();">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($user['id']) ?>">
                                    <button type="submit" name="supprimer_utilisateur" class="btn btn-danger btn-sm">Supprimer</button>
                                </form>
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

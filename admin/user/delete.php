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

// Récupérer les utilisateurs
$result_users = $conn->query("SELECT * FROM personne LIMIT 50");
$utilisateurs = $result_users->fetch_all(MYSQLI_ASSOC);

// Vérification de connexion de l'utilisateur
$nom_utilisateur = isset($_SESSION['nom']) ? $_SESSION['nom'] : "Admin";

// Fonction pour supprimer un utilisateur
function supprimerUtilisateur($id) {
    global $conn;
    $sql = "DELETE FROM personne WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

// Gestion de la suppression de l'utilisateur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer_utilisateur']) && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    if (supprimerUtilisateur($id)) {
        echo "<script>alert('Utilisateur supprimé avec succès.'); window.location.href = 'index.php';</script>";
    } else {
        echo "<script>alert('Erreur lors de la suppression de l\'utilisateur.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Utilisateurs</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f6f9;
            display: flex;
            min-height: 100vh;
        }
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
            text-align: center;
            color: #ecf0f1;
        }
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
            border-radius: 30px;
            transition: all 0.3s;
        }
        .sidebar nav ul li a:hover {
            background-color: #1abc9c;
            transform: translateX(10px);
        }
        .main-content {
            margin-left: 280px;
            padding: 40px;
            width: calc(100% - 280px);
            background-color: #fff;
        }
    </style>
    <script>
        function confirmDelete() {
            return confirm("Êtes-vous sûr de vouloir supprimer cet utilisateur ?");
        }
    </script>
</head>
<body>
    <div class="sidebar">
        <h2>Bienvenue, <?php echo htmlspecialchars($nom_utilisateur); ?> 👋</h2>
        <nav>
            <ul>
                <li><a href="../user/index.php">Gérer les utilisateurs</a></li>
                <li><a href="../forum/index.php">Gérer le forum</a></li>
                <li><a href="../text/index.php">Gérer les textes juridiques</a></li>
                <li><a href="../expert/index.php">Gérer les experts</a></li>
            </ul>
        </nav>
    </div>

    <div class="main-content">
        <h2 class="text-center">Gestion des Utilisateurs</h2>
        <div class="card mt-3">
            <div class="card-header">Liste des utilisateurs</div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Rôle</th>
                            <th>Téléphone</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($utilisateurs as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['id']) ?></td>
                            <td><?= htmlspecialchars($user['nom']) ?></td>
                            <td><?= htmlspecialchars($user['Email']) ?></td>
                            <td><?= htmlspecialchars($user['role']) ?></td>
                            <td><?= htmlspecialchars($user['telephone']) ?></td>
                            <td>
                                <form method="POST" onsubmit="return confirmDelete();">
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

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

// Récupérer les utilisateurs avec le rôle "expert"
$sql = "SELECT * FROM personne WHERE role = 'expert' LIMIT 50";
$result_users = $conn->query($sql);
$experts = $result_users->fetch_all(MYSQLI_ASSOC);

$nom_utilisateur = isset($_SESSION['nom']) ? $_SESSION['nom'] : "Admin";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Experts</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    
    <style>
        /* Réinitialisation et styles globaux */
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    font-family: 'Arial', sans-serif;
}

body {
    background-color: #f8f9fa;
    display: flex;
    min-height: 100vh;
    overflow-x: hidden;
    color: #2c3e50;
}

/* Barre latérale */
.sidebar {
    width: 280px;
    background: linear-gradient(135deg, #2c3e50, #34495e);
    color: white;
    height: 100vh;
    position: fixed;
    top: 0;
    left: 0;
    padding: 40px 30px;
    box-shadow: 4px 0 15px rgba(0, 0, 0, 0.5);
    transition: width 0.3s ease;
}

.sidebar h2 {
    text-align: center;
    color: #ecf0f1;
    margin-bottom: 40px;
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
    display: block;
    padding: 12px;
    border-radius: 8px;
    transition: all 0.3s ease-in-out;
}

.sidebar nav ul li a:hover {
    background-color: #1abc9c;
    padding-left: 30px;
    box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
}

/* Contenu principal */
.main-content {
    margin-left: 280px;
    padding: 40px;
    width: calc(100% - 280px);
    background-color: white;
    min-height: 100vh;
    transition: margin-left 0.3s ease;
}

h1, h2 {
    font-size: 28px;
    margin-bottom: 20px;
    font-weight: bold;
    text-align: center;
}

/* Tableau stylisé */
.table th, .table td {
    text-align: center;
    vertical-align: middle;
    padding: 12px;
}

.table thead {
    background-color: #34495e;
    color: white;
}

.btn {
    transition: all 0.3s ease;
}

.btn:hover {
    transform: scale(1.05);
}

/* Media Queries pour écrans mobiles */
@media screen and (max-width: 768px) {
    .sidebar {
        width: 100%;
        position: relative;
        padding: 20px;
    }

    .main-content {
        margin-left: 0;
        width: 100%;
        padding: 20px;
    }
}

    </style>

    <script>
        function confirmDelete(userId) {
            if (confirm("Êtes-vous sûr de vouloir supprimer cet expert ?")) {
                window.location.href = "delete.php?id=" + userId;
            }
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
                <li><a href="../adminnav/index.php">Accueil</a></li>

            </ul>
        </nav>
    </div>

    <div class="main-content">
        <h2 class="text-center">Gestion des Experts</h2>
        
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['error_message'] ?>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>
        
        <div class="d-flex justify-content-end mb-3">
            <a href="create.php" class="btn btn-primary">Ajouter</a>
        </div>
        
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
                                <button type="button" onclick="confirmDelete(<?= htmlspecialchars($user['id']) ?>)" class="btn btn-danger btn-sm">Supprimer</button>
                                <a href="update.php?id=<?= htmlspecialchars($user['id']) ?>" class="btn btn-warning btn-sm">Modifier</a>
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

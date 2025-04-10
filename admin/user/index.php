<?php 
session_start();
session_regenerate_id(true);

$nom_utilisateur = isset($_SESSION['nom']) ? $_SESSION['nom'] : "Admin";

// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "easylegal";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}   

// Récupérer uniquement les utilisateurs
$result_users = $conn->query("SELECT * FROM personne WHERE role = 'user' LIMIT 50");

if ($result_users) {
    $utilisateurs = $result_users->fetch_all(MYSQLI_ASSOC);
} else {
    echo "Erreur lors de la récupération des utilisateurs : " . $conn->error;
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
        <h2 class="text-center">Gestion des Utilisateurs</h2>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['error_message'] ?>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

       

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
                            <a href="suspendus.php?id=<?= htmlspecialchars($user['id']) ?>" class="btn btn-warning btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir suspendre cet user ?');">Suspendre</a>
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

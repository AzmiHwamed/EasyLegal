<?php
session_start();
session_regenerate_id(true); // Sécurisation de la session

// Vérification de connexion de l'utilisateur
$nom_utilisateur = isset($_SESSION['nom']) ? $_SESSION['nom'] : "Admin";

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "easylegal";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Récupérer les posts du forum
$sql = "SELECT id, contenu ,id_personne FROM forum ORDER BY id DESC LIMIT 50";
$result = $conn->query($sql);
$posts = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forum</title>
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
    background: linear-gradient(135deg, #34495e, #2c3e50);
    color: white;
    height: 100%;
    position: fixed;
    top: 0;
    left: 0;
    padding: 40px 30px;
    box-shadow: 2px 0 20px rgba(0, 0, 0, 0.7);
    transition: width 0.3s ease;
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

/* Main Content */
.main-content {
    margin-left: 280px;
    padding: 40px;
    width: calc(100% - 280px);
    background-color: #fff;
    min-height: 100vh;
    transition: margin-left 0.3s ease;
}

h1 {
    color: #2c3e50;
    font-size: 28px;
    margin-bottom: 20px;
    font-weight: bold;
}

/* Add Button */
.add-btn {
    background: #3498db;
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
    background: #2980b9;
    transform: scale(1.05);
}

/* Posts Container */
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

/* Delete Button */
.delete-btn {
    background: #e74c3c;
    border: none;
    color: white;
    padding: 7px 12px;
    cursor: pointer;
    border-radius: 6px;
    transition: 0.3s ease;
}

.delete-btn:hover {
    background: #c0392b;
    transform: scale(1.1);
}

/* Responsiveness */
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
        <h1>Forum</h1>

        <a href="create.php" class="add-btn">➕ Ajouter un post</a>

        <div class="posts-container">
            <h2>Discussions</h2>
            <?php foreach ($posts as $post): ?>
                <div class="post">
                    <p><?= htmlspecialchars($post['contenu']) ?></p>
                    <form action="delete.php" method="post">
                        <input type="hidden" name="id" value="<?= $post['id'] ?>">
                        <button type="submit" class="delete-btn">🗑 Supprimer</button>
                    </form>
                </div>
            <?php endforeach; ?>
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

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

// Vérification de connexion de l'utilisateur
$nom_utilisateur = isset($_SESSION['nom']) ? $_SESSION['nom'] : "Admin";

// Ajouter un utilisateur
if (isset($_POST['ajouter_utilisateur'])) {
    $nom = trim($_POST['nom']);
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $role = trim($_POST['role']);
    $telephone = trim($_POST['telephone']);
    $motdepasse = password_hash($_POST['motdepasse'], PASSWORD_BCRYPT);

    if ($email && $nom && $role && $telephone && $motdepasse) {
        $stmt = $conn->prepare("INSERT INTO personne (nom, Email, role, motdepasse, telephone) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nom, $email, $role, $motdepasse, $telephone);
        $stmt->execute();
        $stmt->close();
        header("Location: ./index.php");
    } else {
        $_SESSION['error_message'] = "L'email n'est pas valide ou les champs sont manquants.";
    }
}


?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Utilisateurs</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
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
        <h2>Ajouter un utilisateur</h2>
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger"> <?= $_SESSION['error_message'] ?> </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>
        <form method="POST">
            <label>Nom :</label>
            <input type="text" name="nom" class="form-control" required>
            <label>Email :</label>
            <input type="email" name="email" class="form-control" required>
            <label>Rôle :</label>
            <select name="role" class="form-select" required>
                <option value="user">User</option>
                <option value="expert">Expert</option>
            </select>
            <label>Mot de passe :</label>
            <input type="password" name="motdepasse" class="form-control" required>
            <label>Téléphone :</label>
            <input type="text" name="telephone" class="form-control" required>
            <button type="submit" name="ajouter_utilisateur" class="btn btn-primary mt-2">Ajouter</button>
        </form>
        
    </div>
</body>
</html>

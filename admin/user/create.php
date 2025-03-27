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
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Arial', sans-serif; background-color: #f4f6f9; display: flex; min-height: 100vh; }
        .sidebar { width: 280px; background-color: #34495e; color: white; height: 100%; position: fixed; top: 0; left: 0; padding: 40px 30px; box-shadow: 2px 0 20px rgba(0, 0, 0, 0.7); }
        .sidebar h2 { margin-bottom: 40px; font-size: 24px; text-align: center; color: #ecf0f1; }
        .sidebar nav ul { padding-left: 0; list-style: none; }
        .sidebar nav ul li { margin: 25px 0; }
        .sidebar nav ul li a { color: #ecf0f1; text-decoration: none; font-size: 18px; padding: 12px 20px; display: block; border-radius: 30px; transition: all 0.3s ease; }
        .sidebar nav ul li a:hover { background-color: #1abc9c; padding-left: 25px; transform: translateX(10px); }
        .main-content { margin-left: 280px; padding: 40px; width: calc(100% - 280px); background-color: #fff; min-height: 100vh; }
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

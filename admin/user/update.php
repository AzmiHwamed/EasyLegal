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

// Fonction pour mettre à jour le profil d'un utilisateur
function mettreAJourProfil($id, $nom, $Email, $motdepasse, $telephone, $role) {
    global $conn;

    if (!empty($motdepasse)) {
        $sql = "UPDATE personne SET nom=?, Email=?, motdepasse=?, telephone=?, role=? WHERE id=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssssi", $nom, $Email, $motdepasse, $telephone, $role, $id);
    } else {
        $sql = "UPDATE personne SET nom=?, Email=?, telephone=?, role=? WHERE id=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssssi", $nom, $Email, $telephone, $role, $id);
    }

    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $result;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $id = $_POST['id'];
    $nom = $_POST['nom'];
    $Email = $_POST['Email'];
    $motdepasse = $_POST['motdepasse'];
    $telephone = $_POST['telephone'];
    $role = $_POST['role'];

    if ($id) {
        if (mettreAJourProfil($id, $nom, $Email, $motdepasse, $telephone, $role)) {
            $_SESSION['success_message'] = "Profil mis à jour avec succès !";
        } else {
            $_SESSION['error_message'] = "Erreur lors de la mise à jour.";
        }
    }
    header("Location: ".$_SERVER['PHP_SELF']."?id=".$id);
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $result_user = $conn->query("SELECT * FROM personne WHERE id = $id LIMIT 1");
    $utilisateur = $result_user->fetch_assoc();
} else {
    $_SESSION['error_message'] = "Aucun utilisateur spécifié.";
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            display: flex;
            min-height: 100vh;
            font-family: 'Arial', sans-serif;
            background-color: #f4f6f9;
        }
        .sidebar {
            width: 280px;
            background-color: #34495e;
            color: white;
            padding: 40px 30px;
            position: fixed;
            height: 100%;
            box-shadow: 2px 0 20px rgba(0, 0, 0, 0.7);
        }
        .sidebar h2 {
            text-align: center;
            color: #ecf0f1;
        }
        .sidebar nav ul {
            list-style: none;
            padding-left: 0;
        }
        .sidebar nav ul li {
            margin: 25px 0;
        }
        .sidebar nav ul li a {
            color: #ecf0f1;
            text-decoration: none;
            font-size: 18px;
            display: block;
            padding: 12px;
            border-radius: 30px;
        }
        .main-content {
            margin-left: 280px;
            padding: 40px;
            width: calc(100% - 280px);
            background-color: #fff;
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
        <h2 class="text-center">Mise à jour de l'Utilisateur</h2>
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>
        <div class="card mt-3">
            <div class="card-header">Mise à jour du profil</div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="id" value="<?= $utilisateur['id'] ?>">
                    <input type="text" name="nom" class="form-control" value="<?= $utilisateur['nom'] ?>" required>
                    <input type="email" name="Email" class="form-control" value="<?= $utilisateur['Email'] ?>" required>
                    <input type="password" name="motdepasse" class="form-control" placeholder="Mot de passe (laisser vide pour ne pas changer)">
                    <input type="text" name="telephone" class="form-control" value="<?= $utilisateur['telephone'] ?>" required>
                    <select name="role" class="form-select" required>
                        <option value="user" <?= ($utilisateur['role'] == 'user') ? 'selected' : '' ?>>User</option>
                        <option value="expert" <?= ($utilisateur['role'] == 'expert') ? 'selected' : '' ?>>Expert</option>
                    </select>
                    <button type="submit" name="update" class="btn btn-primary mt-2">Mettre à jour</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

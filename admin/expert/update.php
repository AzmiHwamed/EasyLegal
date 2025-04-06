<?php
session_start();

// Définir le nom de l'utilisateur
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

// Fonction pour mettre à jour le profil d'un utilisateur
function mettreAJourProfil($id, $nom, $Email, $motdepasse, $telephone, $role) {
    global $conn;

    if (!empty($motdepasse)) {
        $sql = "UPDATE personne SET nom=?, Email=?, motdepasse=?, telephone=?, role=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $nom, $Email, password_hash($motdepasse, PASSWORD_DEFAULT), $telephone, $role, $id);
    } else {
        $sql = "UPDATE personne SET nom=?, Email=?, telephone=?, role=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $nom, $Email, $telephone, $role, $id);
    }

    if ($stmt->execute()) {
        return true;
    } else {
        $_SESSION['error_message'] = "Erreur SQL: " . $stmt->error;
        return false;
    }
}

// Vérification si le formulaire de mise à jour a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $id = $_POST['id']; 
    $nom = $_POST['nom'];
    $Email = $_POST['Email'];
    $motdepasse = $_POST['motdepasse'];
    $telephone = $_POST['telephone'];
    $role = $_POST['role'];

    if ($id && !empty($nom) && !empty($Email) && !empty($telephone) && !empty($role)) {
        if (mettreAJourProfil($id, $nom, $Email, $motdepasse, $telephone, $role)) {
            $_SESSION['success_message'] = "Profil mis à jour avec succès !";
        } else {
            $_SESSION['error_message'] = "Erreur lors de la mise à jour.";
        }
    } else {
        $_SESSION['error_message'] = "Tous les champs doivent être remplis!";
    }

    header("Location: ".$_SERVER['PHP_SELF']."?id=".$id);
    exit();
}

// Vérifier si l'ID de l'utilisateur est passé dans l'URL
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
    <title>Mise à jour de l'Expert</title>
    
    <!-- CSS personnalisé -->
    <style>
     <style>
       /* Réinitialisation et styles globaux */
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    font-family: 'Arial', sans-serif;
}

body {
    background-color: #f4f6f9;
    display: flex;
    min-height: 100vh;
    overflow-x: hidden;
    color:#f0e6d6;
}

/* Barre latérale */
.sidebar {
    width: 280px;
    background: linear-gradient(135deg,#e8a043,#e8a043);
    color: white;
    height: 100vh;
    position: fixed;
    top: 0;
    left: 0;
    padding: 40px 30px;
    box-shadow: 4px 0 15px #f0e6d6;
    transition: width 0.3s ease;
}

.sidebar h2 {
    margin-bottom: 40px;
    font-size: 24px;
    font-weight: 700;
    text-align: center;
    letter-spacing: 1px;
    color: #f0e6d6;
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
    border-radius: 8px;
    transition: all 0.3s ease-in-out;
}

.sidebar nav ul li a:hover {
    background-color:rgb(203, 173, 38);
    color: white;
    padding-left: 30px;
    box-shadow: 2px 2px 10px #f0e6d6;
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

h1 {
    font-size: 40px;
    margin-bottom:30px;
    font-weight: bold;
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
    
    <!-- JavaScript personnalisé -->
    <script>
        // Fonction de validation avant la soumission du formulaire
        function validateForm() {
            var nom = document.forms["updateForm"]["nom"].value;
            var email = document.forms["updateForm"]["Email"].value;
            var telephone = document.forms["updateForm"]["telephone"].value;
            var role = document.forms["updateForm"]["role"].value;

            if (nom == "" || email == "" || telephone == "" || role == "") {
                alert("Tous les champs doivent être remplis!");
                return false;
            }
            return true;
        }

        // Fonction de confirmation avant mise à jour
        function confirmUpdate() {
            return confirm("Êtes-vous sûr de vouloir mettre à jour ce profil?");
        }

        // Fonction de confirmation de navigation pour la sidebar
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

    <!-- Main content -->
    <div class="main-content">
        <h1>Mise à jour de l'Expert</h1>

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

        <!-- Formulaire de mise à jour de l'utilisateur spécifique -->
        <div class="card mt-3">
            <div class="card-header">Mise à jour du profil</div>
            <div class="card-body">
                <form method="POST" name="updateForm" onsubmit="return validateForm() && confirmUpdate();">
                    <input type="hidden" name="id" value="<?= $utilisateur['id'] ?>">
                    <input type="text" name="nom" class="form-control" value="<?= $utilisateur['nom'] ?>" required>
                    <input type="email" name="Email" class="form-control" value="<?= $utilisateur['Email'] ?>" required>
                    <input type="password" name="motdepasse" class="form-control" placeholder="Mot de passe (laisser vide pour ne pas changer)">
                    <input type="text" name="telephone" class="form-control" value="<?= $utilisateur['telephone'] ?>" required>
                    <select name="role" class="form-select" required>
                        <option value="utilisateur" <?= ($utilisateur['role'] == 'utilisateur') ? 'selected' : '' ?>>Utilisateur</option>
                        <option value="expert" <?= ($utilisateur['role'] == 'expert') ? 'selected' : '' ?>>Expert</option>
                    </select>
                    <button type="submit" name="update" class="btn btn-primary mt-2">Mettre à jour</button>
                </form>
            </div>
        </div>
    </div>

</body>
</html>























































    

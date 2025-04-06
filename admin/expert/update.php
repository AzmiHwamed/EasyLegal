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
    
    <style>
/* Réinitialisation + base typographique */
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
    line-height: 1.6;
}

/* Sidebar */
.sidebar {
    width: 260px;
    background: linear-gradient(135deg, #e89d3f, #e8a043);
    color: white;
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    padding: 40px 25px;
    box-shadow: 4px 0 15px rgba(0, 0, 0, 0.08);
}

.sidebar h2 {
    margin-bottom: 35px;
    font-size: 1.4rem;
    text-align: center;
}

.sidebar nav ul {
    list-style: none;
}

.sidebar nav ul li {
    margin: 20px 0;
}

.sidebar nav ul li a {
    color: white;
    text-decoration: none;
    font-size: 1rem;
    padding: 10px 16px;
    display: block;
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
}

.main-content h1 {
    font-size: 2rem;
    margin-bottom: 25px;
}

/* Formulaire */
.card {
    background-color: #f9f9f9;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
    max-width: 600px;
}

.card input,
.card select {
    width: 100%;
    padding: 12px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 8px;
}

.card button {
    background-color: #e89d3f;
    border: none;
    color: white;
    padding: 12px 20px;
    font-size: 1rem;
    border-radius: 8px;
    cursor: pointer;
    transition: background 0.3s ease;
}

.card button:hover {
    background-color: #d88828;
}

.alert {
    padding: 15px;
    border-radius: 6px;
    margin-bottom: 20px;
}

.alert-danger {
    background-color: #f8d7da;
    color: #842029;
}

.alert-success {
    background-color: #d1e7dd;
    color: #0f5132;
}

/* Responsive */
@media screen and (max-width: 768px) {
    .sidebar {
        position: relative;
        width: 100%;
        height: auto;
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
    <div class="main-content">
    <h1>Mise à jour de l'Expert</h1>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?></div>
    <?php endif; ?>

    <div class="card">
        <form method="POST" name="updateForm" onsubmit="return validateForm() && confirmUpdate();">
            <input type="hidden" name="id" value="<?= htmlspecialchars($utilisateur['id']) ?>">
            <input type="text" name="nom" placeholder="Nom complet" value="<?= htmlspecialchars($utilisateur['nom']) ?>" required>
            <input type="email" name="Email" placeholder="Adresse email" value="<?= htmlspecialchars($utilisateur['Email']) ?>" required>
            <input type="password" name="motdepasse" placeholder="Nouveau mot de passe (laisser vide pour ne pas changer)">
            <input type="text" name="telephone" placeholder="Téléphone" value="<?= htmlspecialchars($utilisateur['telephone']) ?>" required>
            <select name="role" required>
                <option value="">-- Sélectionner un rôle --</option>
                <option value="utilisateur" <?= ($utilisateur['role'] === 'utilisateur') ? 'selected' : '' ?>>Utilisateur</option>
                <option value="expert" <?= ($utilisateur['role'] === 'expert') ? 'selected' : '' ?>>Expert</option>
            </select>
            <button type="submit" name="update">Mettre à jour</button>
        </form>
    </div>
</div>


</body>
</html>























































    

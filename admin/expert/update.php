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

// Fonction pour mettre à jour le profil d'un utilisateur
function mettreAJourProfil($id, $nom, $Email, $motdepasse, $telephone, $role) {
    global $conn;

    if (!empty($motdepasse)) {
        // Si un mot de passe est fourni, on l'inclut dans la requête
        $sql = "UPDATE personne SET nom=?, Email=?, motdepasse=?, telephone=?, role=? WHERE id=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssssi", $nom, $Email, $motdepasse, $telephone, $role, $id);
    } else {
        // Sinon, on met à jour sans le mot de passe
        $sql = "UPDATE personne SET nom=?, Email=?, telephone=?, role=? WHERE id=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssssi", $nom, $Email, $telephone, $role, $id);
    }

    $result = mysqli_stmt_execute($stmt);
    if (!$result) {
        // Afficher les erreurs pour le débogage
        $_SESSION['error_message'] = "Erreur SQL: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
    return $result;
}

// Vérification si le formulaire de mise à jour a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $id = $_POST['id']; // L'id de l'utilisateur à mettre à jour
    $nom = $_POST['nom'];
    $Email = $_POST['Email']; // Utilisez Email avec la majuscule "E"
    $motdepasse = $_POST['motdepasse'];
    $telephone = $_POST['telephone'];
    $role = $_POST['role'];

    if ($id) {
        // Vérifier si l'utilisateur a les droits pour mettre à jour
        // Si vous avez une session d'utilisateur, vous pouvez comparer l'ID avec l'ID de l'utilisateur connecté
        // Exemple: if ($_SESSION['user_id'] == $id || $_SESSION['role'] == 'admin')
        if (mettreAJourProfil($id, $nom, $Email, $motdepasse, $telephone, $role)) {
            $_SESSION['success_message'] = "Profil mis à jour avec succès !";
        } else {
            $_SESSION['error_message'] = "Erreur lors de la mise à jour.";
        }
    }
    header("Location: ".$_SERVER['PHP_SELF']."?id=".$id); // Rafraîchir la page avec l'id de l'utilisateur mis à jour
    exit();
}

// Vérifier si l'ID de l'utilisateur est passé dans l'URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $result_user = $conn->query("SELECT * FROM personne WHERE id = $id LIMIT 1");
    $utilisateur = $result_user->fetch_assoc();
} else {
    // Rediriger si l'ID n'est pas spécifié
    $_SESSION['error_message'] = "Aucun utilisateur spécifié.";
    header("Location: index.php"); // Modifier vers la page appropriée
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mise à jour de l'Utilisateur</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    
    <!-- CSS personnalisé -->
    <style>
        body {
            background-color: #f4f7fa;
            font-family: 'Arial', sans-serif;
        }
        .container {
            max-width: 1200px;
        }
        .card-header {
            background-color: #007bff;
            color: white;
            font-size: 1.2rem;
        }
        .card-body {
            background-color: white;
            padding: 20px;
        }
        table th, table td {
            text-align: center;
        }
        .btn-sm {
            margin-right: 5px;
        }
        .btn-danger {
            background-color: #e3342f;
            border-color: #e3342f;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .alert {
            margin-top: 20px;
        }
        form input, form select {
            margin-bottom: 10px;
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
    </script>
</head>
<body class="container mt-4">

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

</body>
</html>

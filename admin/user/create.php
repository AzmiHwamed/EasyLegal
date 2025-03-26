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

// Ajouter un utilisateur
if (isset($_POST['ajouter_utilisateur'])) {
    $nom = trim($_POST['nom']);
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL); // Validation de l'email
    $role = trim($_POST['role']);
    $telephone = trim($_POST['telephone']);
    $motdepasse = password_hash($_POST['motdepasse'], PASSWORD_BCRYPT);

    // Vérifier si l'email est valide avant d'insérer
    if ($email && $nom && $role && $telephone && $motdepasse) {
        $stmt = $conn->prepare("INSERT INTO personne (nom, Email, role, motdepasse, telephone) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nom, $email, $role, $motdepasse, $telephone);
        $stmt->execute();
        $stmt->close();
    } else {
        $_SESSION['error_message'] = "L'email n'est pas valide ou les champs sont manquants.";
    }
}

// Récupérer les utilisateurs
$result_users = $conn->query("SELECT * FROM personne LIMIT 50");
$utilisateurs = $result_users->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter Utilisateurs</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    
    <!-- CSS personnalisé -->
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }
        .container {
            max-width: 1200px;
        }
        .table th, .table td {
            text-align: center;
        }
        .btn-sm {
            margin-right: 5px;
        }
        .alert {
            margin-top: 20px;
        }
        .card-header {
            background-color: #007bff;
            color: white;
        }
        .card-body {
            background-color: #ffffff;
        }
    </style>

    <!-- JavaScript personnalisé -->
    <script>
        // Fonction de validation du formulaire d'ajout d'utilisateur
        function validateForm() {
            var nom = document.forms["addUserForm"]["nom"].value;
            var email = document.forms["addUserForm"]["email"].value;
            var telephone = document.forms["addUserForm"]["telephone"].value;
            var motdepasse = document.forms["addUserForm"]["motdepasse"].value;

            if (nom == "" || email == "" || telephone == "" || motdepasse == "") {
                alert("Tous les champs doivent être remplis !");
                return false;
            }

            var emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
            if (!email.match(emailRegex)) {
                alert("Veuillez entrer un email valide.");
                return false;
            }
        }

        // Fonction de confirmation avant suppression
        function confirmDelete(userId) {
            if (confirm("Êtes-vous sûr de vouloir supprimer cet utilisateur ?")) {
                window.location.href = "delete.php?id=" + userId;
            }
        }
    </script>
</head>
<body class="container mt-4">

    <h2 class="text-center">Ajouter users</h2>

    <!-- Affichage du message d'erreur ou de succès -->
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger">
            <?= $_SESSION['error_message'] ?>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <!-- Formulaire d'ajout d'utilisateur -->
    <div class="card mt-3">
        <div class="card-header">Ajouter un utilisateur</div>
        <div class="card-body">
            <form method="POST" name="addUserForm" onsubmit="return validateForm()">
                <div class="mb-2">
                    <label>Nom :</label>
                    <input type="text" name="nom" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label>Email :</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label>Rôle :</label>
                    <select name="role" class="form-select" required>
                        <option value="utilisateur">Utilisateur</option>
                        <option value="expert">Expert</option>
                    </select>
                </div>
                <div class="mb-2">
                    <label>Mot de passe :</label>
                    <input type="password" name="motdepasse" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label>Téléphone :</label>
                    <input type="text" name="telephone" class="form-control" required>
                </div>
                <button type="submit" name="ajouter_utilisateur" class="btn btn-primary">Ajouter</button>
            </form>
        </div>
    </div>

    <!-- Liste des utilisateurs -->
    <div class="card mt-3">
        <div class="card-header">Liste des utilisateurs</div>
        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Téléphone</th>
                    <th>Action</th>
                </tr>
                <?php foreach ($utilisateurs as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['id']) ?></td>
                    <td><?= htmlspecialchars($user['nom']) ?></td>
                    <td><?= htmlspecialchars($user['Email']) ?></td>
                    <td><?= htmlspecialchars($user['role']) ?></td>
                    <td><?= htmlspecialchars($user['telephone']) ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($user['id']) ?>">
                            <button type="submit" name="supprimer_utilisateur" class="btn btn-danger btn-sm" onclick="confirmDelete(<?= htmlspecialchars($user['id']) ?>)">Supprimer</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

</body>
</html>

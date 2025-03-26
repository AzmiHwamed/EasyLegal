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

// Récupérer les utilisateurs
$result_users = $conn->query("SELECT * FROM personne LIMIT 50");
$utilisateurs = $result_users->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Utilisateurs</title>
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
    </style>

    <!-- JavaScript personnalisé -->
    <script>
        // Fonction de confirmation avant suppression
        function confirmDelete(userId) {
            if (confirm("Êtes-vous sûr de vouloir supprimer cet utilisateur ?")) {
                window.location.href = "delete.php?id=" + userId;
            }
        }
    </script>
</head>
<body class="container mt-4">

    <h2 class="text-center">Gestion des Utilisateurs</h2>

    <!-- Affichage des messages d'erreur ou de succès -->
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger">
            <?= $_SESSION['error_message'] ?>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <!-- Bouton Ajouter en dehors du tableau -->
    <div class="d-flex justify-content-end mb-3">
        <a href="create.php" class="btn btn-primary">Ajouter</a>
    </div>

    <!-- Liste des utilisateurs -->
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
                            <button type="button" onclick="confirmDelete(<?= htmlspecialchars($user['id']) ?>)" class="btn btn-danger btn-sm">Supprimer</button>
                            <a href="update.php?id=<?= htmlspecialchars($user['id']) ?>" class="btn btn-warning btn-sm">Modifier</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
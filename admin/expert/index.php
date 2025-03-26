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

// Récupérer les utilisateurs avec le rôle "expert"
$sql = "SELECT * FROM personne WHERE role = 'expert' LIMIT 50";
$result_users = $conn->query($sql);
$experts = $result_users->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Experts</title>
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
            if (confirm("Êtes-vous sûr de vouloir supprimer cet expert ?")) {
                window.location.href = "delete.php?id=" + userId;
            }
        }
    </script>
</head>
<body class="container mt-4">

    <h2 class="text-center">Gestion des Experts</h2>

    <!-- Affichage des messages d'erreur ou de succès -->
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger">
            <?= $_SESSION['error_message'] ?>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <!-- Liste des experts -->
    <div class="card mt-3">
        <div class="card-header">Liste des Experts</div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($experts as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['id']) ?></td>
                        <td><?= htmlspecialchars($user['nom']) ?></td>
                        <td><?= htmlspecialchars($user['Email']) ?></td>
                        <td><?= htmlspecialchars($user['telephone']) ?></td>
                        <td>
                            <!-- Boutons d'action -->
                            <a href="create.php" class="btn btn-primary btn-sm">Ajouter</a> 
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

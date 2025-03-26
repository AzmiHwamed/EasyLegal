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

// Récupérer les textes juridiques
$result_textes = $conn->query("SELECT * FROM textjuridique LIMIT 50");
$textes_juridiques = $result_textes->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Textes Juridiques</title>
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
        function confirmDelete(textId) {
            if (confirm("Êtes-vous sûr de vouloir supprimer ce texte juridique ?")) {
                window.location.href = "delete.php?id=" + textId;
            }
        }
    </script>
</head>
<body class="container mt-4">

    <h2 class="text-center">Gestion des Textes Juridiques</h2>

    <!-- Affichage des messages d'erreur ou de succès -->
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger">
            <?= $_SESSION['error_message'] ?>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <!-- Liste des textes juridiques -->
    <div class="card mt-3">
        <div class="card-header">Liste des Textes Juridiques</div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Contenu</th>
                        <th>Thème</th>
                        <th>Type</th>
                        <th>Titre</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($textes_juridiques as $texte): ?>
                    <tr>
                        <td><?= htmlspecialchars($texte['id']) ?></td>
                        <td><?= htmlspecialchars($texte['Date']) ?></td>
                        <td><?= htmlspecialchars($texte['Contenu']) ?></td>
                        <td><?= htmlspecialchars($texte['Theme']) ?></td>
                        <td><?= htmlspecialchars($texte['Type']) ?></td>
                        <td><?= htmlspecialchars($texte['Titre']) ?></td>
                        <td>
                            <!-- Boutons d'action -->
                            <a href="create.php" class="btn btn-primary btn-sm">Ajouter</a> 
                            <button type="button" onclick="confirmDelete(<?= htmlspecialchars($texte['id']) ?>)" class="btn btn-danger btn-sm">Supprimer</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Ajouter ici d'autres éléments de page si nécessaire -->
</body>
</html>

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

// Récupérer les enregistrements de la table textjuridique
$sql = "SELECT * FROM textjuridique";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Affichage des Textes Juridiques</title>
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
    </style>
    <!-- JavaScript personnalisé -->
    <script>
        // Fonction de confirmation avant suppression
        function confirmDelete(userId) {
            if (confirm("Êtes-vous sûr de vouloir supprimer ce texte juridique ?")) {
                window.location.href = "delete.php?id=" + userId;
            }
        }
    </script>
</head>
<body class="container mt-4">

    <h2 class="text-center">Liste des Textes Juridiques</h2>

    <!-- Affichage des messages de session -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['message']; ?>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?= $_SESSION['error']; ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Liste des textes juridiques -->
    <div class="card mt-3">
        <div class="card-header">Table des Textes Juridiques</div>
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
                    <?php
                    if ($result->num_rows > 0) {
                        // Afficher les enregistrements
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['Date']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['Contenu']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['Theme']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['Titre']) . "</td>";
                            echo "<td>
                                    <a href='create.php' class='btn btn-success btn-sm'>Ajouter</a>
                                    <a href='delete.php' class='btn btn-success btn-sm'>supprimer</a>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7' class='text-center'>Aucun texte juridique trouvé.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Lien pour ajouter un texte juridique -->
    <div class="text-center mt-3">
        <a href="create.php" class="btn btn-primary">Ajouter un Nouveau Texte Juridique</a>
    </div>

</body>
</html>

<?php
// Fermer la connexion à la base de données
$conn->close();
?>

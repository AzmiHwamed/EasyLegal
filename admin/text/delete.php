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

// Fonction pour supprimer un texte juridique
function supprimerTexte($id) {
    global $conn;
    $sql = "DELETE FROM textjuridique WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

// Gestion de la suppression du texte juridique
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer_texte']) && isset($_POST['id'])) {
    $id = intval($_POST['id']); // S'assurer que l'ID est un entier
    if (supprimerTexte($id)) {
        echo "<script>alert('Texte juridique supprimé avec succès.'); window.location.href = 'index.php';</script>";
    } else {
        echo "<script>alert('Erreur lors de la suppression du texte juridique.');</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supprimer text juridique</title>
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
    </style>
    
    <!-- JavaScript personnalisé -->
    <script>
        // Fonction de confirmation avant suppression
        function confirmDelete() {
            return confirm("Êtes-vous sûr de vouloir supprimer ce texte juridique ?");
        }
    </script>
</head>
<body class="container mt-4">

    <h2 class="text-center">Supprimer un Texte Juridique</h2>

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
                        <th>Action</th>
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
                            <form method="POST" style="display:inline;" onsubmit="return confirmDelete();">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($texte['id']) ?>">
                                <button type="submit" name="supprimer_texte" class="btn btn-danger btn-sm">Supprimer</button>
                            </form>
                            <a href="index.php" class="btn btn-primary btn-sm">Annuler</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>

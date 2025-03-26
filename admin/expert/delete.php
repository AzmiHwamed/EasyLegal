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

// Récupérer les experts uniquement
$result_experts = $conn->query("SELECT * FROM personne WHERE role = 'expert' LIMIT 50");
$experts = $result_experts->fetch_all(MYSQLI_ASSOC);

// Fonction pour supprimer un expert
function supprimerExpert($id) {
    global $conn;
    $sql = "DELETE FROM personne WHERE id = ? AND role = 'expert'";  // Vérifier que l'utilisateur a le rôle 'expert'
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

// Gestion de la suppression de l'expert
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer_utilisateur']) && isset($_POST['id'])) {
    $id = intval($_POST['id']); // S'assurer que l'ID est un entier
    if (supprimerExpert($id)) {
        echo "<script>alert('Expert supprimé avec succès.'); window.location.href = 'index.php';</script>";
    } else {
        echo "<script>alert('Erreur lors de la suppression de l\'expert.');</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supprimer des experts</title>
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
            return confirm("Êtes-vous sûr de vouloir supprimer cet expert ?");
        }
    </script>
</head>
<body class="container mt-4">

    <h2 class="text-center">Supprimer un Expert</h2>

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
                            <form method="POST" style="display:inline;" onsubmit="return confirmDelete();">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($user['id']) ?>">
                                <button type="submit" name="supprimer_utilisateur" class="btn btn-danger btn-sm">Supprimer</button>
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

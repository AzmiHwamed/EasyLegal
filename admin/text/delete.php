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

// Vérifier si l'ID est passé en paramètre
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Préparer la requête pour supprimer le texte juridique
    $sql = "DELETE FROM textjuridique WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Si la suppression a réussi
        $_SESSION['message'] = "Le texte juridique a été supprimé avec succès.";
    } else {
        // En cas d'échec de la suppression
        $_SESSION['error'] = "Erreur lors de la suppression du texte juridique.";
    }

    // Fermer la requête
    $stmt->close();
} else {
    // Si l'ID n'est pas fourni, rediriger vers la page d'accueil
    $_SESSION['error'] = "Aucun texte juridique à supprimer.";
}

// Fermer la connexion
$conn->close();

// Rediriger vers la page d'index
header("Location: index.php");
exit();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de suppression</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-4">
    <h2 class="text-center">Suppression d'un Texte Juridique</h2>

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

    <div class="text-center mt-3">
        <a href="index.php" class="btn btn-primary">Retour à la liste des textes juridiques</a>
    </div>
</body>
</html>

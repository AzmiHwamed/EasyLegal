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

// Vérifier si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = $_POST['Date'];
    $contenu = $_POST['Contenu'];
    $theme = $_POST['Theme'];
    $type = $_POST['Type'];
    $titre = $_POST['Titre'];

    // Préparer et exécuter la requête d'insertion
    $sql = "INSERT INTO textjuridique (Date, Contenu, Theme, Type, Titre) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $date, $contenu, $theme, $type, $titre);

    if ($stmt->execute()) {
        echo "<script>alert('Texte juridique ajouté avec succès.'); window.location.href = 'index.php';</script>";
    } else {
        echo "<script>alert('Erreur lors de l\'ajout du texte juridique.'); window.location.href = 'create.php';</script>";
    }
}

// Fermer la connexion à la base de données
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Texte Juridique</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-4">

    <h2 class="text-center">Ajouter un Texte Juridique</h2>

    <div class="card mt-3">
        <div class="card-header">Formulaire d'Ajout</div>
        <div class="card-body">
            <form action="create.php" method="POST">
                <div class="mb-3">
                    <label for="Date" class="form-label">Date</label>
                    <input type="date" id="Date" name="Date" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="Contenu" class="form-label">Contenu</label>
                    <textarea id="Contenu" name="Contenu" class="form-control" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="Theme" class="form-label">Thème</label>
                    <input type="text" id="Theme" name="Theme" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="Type" class="form-label">Type</label>
                    <input type="text" id="Type" name="Type" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="Titre" class="form-label">Titre</label>
                    <input type="text" id="Titre" name="Titre" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Ajouter</button>
                <a href="index.php" class="btn btn-secondary">Retour à la Liste</a>
            </form>
        </div>
    </div>

</body>
</html>

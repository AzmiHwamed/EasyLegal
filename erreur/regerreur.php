<?php
session_start();
include('../dbconfig/index.php'); // Assurez-vous que la connexion est bien établie

$error_message = ""; // Variable pour stocker le message d'erreur

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['nom']) && !empty($_POST['telephone']) && !empty($_POST['Email']) && !empty($_POST['motdepasse'])) {
        $nom = $_POST['nom'];
        $telephone = $_POST['telephone'];
        $role = 'user';
        $Email = $_POST['Email'];
        $motdepasse = password_hash($_POST['motdepasse'], PASSWORD_DEFAULT); // Sécuriser le mot de passe

        // Préparer et exécuter la requête sécurisée
        $stmt = $conn->prepare("INSERT INTO personne (nom, telephone, role, Email, motdepasse) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nom, $telephone, $role, $Email, $motdepasse);

        if ($stmt->execute()) {
            // Inscription réussie
            echo "<script>
                    alert('Inscription réussie !');
                    window.location.href = 'login.php';  // Redirige vers la page de login
                  </script>";
            exit();
        } else {
            $error_message = "Erreur lors de l'inscription. Veuillez réessayer.";
        }

        $stmt->close();
    } else {
        $error_message = "Veuillez remplir tous les champs.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erreur - Inscription</title>
    <style>
        /* Style global */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7fa;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #333;
        }

        .container {
            text-align: center;
            background-color: #ffffff;
            padding: 40px 60px;
            border-radius: 10px;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
        }

        h1 {
            font-size: 36px;
            color:rgba(152, 47, 35, 0.62);
            margin-bottom: 20px;
        }

        .error-message {
            font-size: 18px;
            color: #555;
            margin-bottom: 30px;
        }

        .button {
            text-decoration: none;
            background-color: #e8a043;
            color: #fff;
            padding: 15px 30px;
            font-size: 18px;
            border-radius: 5px;
            transition: background-color 0.3s ease, transform 0.3s ease;
            display: inline-block;
        }

        .button:hover {
            background-color: #d18f38;
            transform: scale(1.05);
        }

        footer {
            margin-top: 40px;
            font-size: 14px;
            color: #777;
        }

        footer a {
            text-decoration: none;
            color: #555;
            transition: color 0.3s ease;
        }

        footer a:hover {
            color: #e8a043;
        }

        /* Animation pour l'affichage des erreurs */
        .fade-in {
            opacity: 0;
            animation: fadeIn 2s forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

    </style>
</head>
<body>

    <div class="container fade-in">
        <h1>Erreur lors de l'inscription</h1>
        
        <!-- Affichage de l'erreur -->
        <div class="error-message">
            <?php if ($error_message): ?>
                <p><?= $error_message ?></p>
            <?php endif; ?>
        </div>

        <!-- Lien pour retourner à la page d'accueil -->
        <a href="/pfe/EasyLegal/index.php" class="button">Retour à la page d'accueil</a>

        <footer>
            <p>Easy Legal &copy; 2025 | Tous droits réservés</p>
            <p><a href="/pfe/EasyLegal/contact.php">Contactez-nous</a></p>
        </footer>
    </div>

</body>
</html>

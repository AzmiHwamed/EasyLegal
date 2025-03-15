<?php
session_start();
include('../dbconfig/index.php'); // Assurez-vous que la connexion est bien établie

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['nom']) && !empty($_POST['telephone']) && !empty($_POST['Email']) && !empty($_POST['motdepasse'])) {
        
        $nom = $_POST['nom'];
        $telephone = $_POST['telephone'];
        $Email = $_POST['Email'];
        $motdepasse = $_POST['motdepasse'];
        
        // Préparer et exécuter la requête sécurisée
        $stmt = $conn->prepare("UPDATE personne SET nom = ?, telephone = ?, Email = ?, motdepasse = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $nom, $telephone, $Email, $motdepasse, $id);
        
        if ($stmt->execute()) {
            echo "<script>alert('Mise à jour réussie !');</script>";
        } else {
            echo "<script>alert('Erreur lors de la mise à jour');</script>";
        }
        
        $stmt->close();
    } else {
        echo "<script>alert('Veuillez remplir tous les champs.');</script>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modification du Profil</title>
    <style>
        body {
            margin: 0;
            padding-top: 70px;
            font-family: Arial, sans-serif;
            background: #f8f4ef;
        }

        .form-container {
            width: 350px;
            background: transparent;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }

        input[type="text"], input[type="password"], input[type="email"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            display: block;
        }

        button {
            background: #e8a043;
            color: white;
            border: none;
            padding: 10px;
            width: 100%;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background: #d18f38;
        }

        .custom-alert {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: #e8a043;
            color: white;
            padding: 15px 20px;
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }

        .custom-alert.show {
            opacity: 1;
            visibility: visible;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Modifier vos informations</h2>
        <form id="updateForm" method="post" action="#">
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" required>
            
            <label for="Email">Email :</label>
            <input type="email" id="Email" name="Email" required>
            
            <label for="telephone">Téléphone :</label>
            <input type="text" id="telephone" name="telephone" required>
            
            <label for="motdepasse">Mot de passe :</label>
            <input type="password" id="motdepasse" name="motdepasse" required>
            
            <button type="submit">Mettre à jour</button>
        </form>
    </div>

    <div id="customAlert" class="custom-alert">
        ✅ Mise à jour réussie !
        <button onclick="closeCustomAlert()">✖</button>
    </div>

    <script>
        document.getElementById("updateForm").addEventListener("submit", function(event) {
            event.preventDefault();
            showCustomAlert();
            setTimeout(() => this.submit(), 2000);
        });

        function showCustomAlert() {
            const alertBox = document.getElementById("customAlert");
            alertBox.classList.add("show");
            setTimeout(closeCustomAlert, 2000);
        }

        function closeCustomAlert() {
            const alertBox = document.getElementById("customAlert");
            alertBox.classList.remove("show");
        }
    </script>
</body>
</html>
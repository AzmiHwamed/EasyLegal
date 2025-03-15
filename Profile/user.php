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
    <title>Profil Utilisateur</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8f4ef;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            display: flex;
            background: transparent;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            max-width: 800px;
            width: 100%;
        }
        .profile-card {
            width: 250px;
            text-align: center;
            padding: 20px;
            border-right: 1px solid #ddd;
        }
        .profile-card img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
        }
        .profile-card button {
            background: #e8a043;
            color: white;
            border: none;
            padding: 10px;
            width: 100%;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }
        .edit-profile {
            flex: 1;
            padding: 20px;
        }
        .edit-profile h2 {
            margin-bottom: 10px;
        }
        .form-group {
            margin-bottom: 10px;
        }
        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .update-btn {
            background: #e8a043;
            color: white;
            border: none;
            padding: 10px;
            width: 100%;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="profile-card">
            <img src="../assets/user.png" alt="Photo de Profil">
            <h3>Jamed Allan</h3>
            <p>@james</p>
            <button>Téléverser une nouvelle photo </button>
            <p>Member depuis: <strong>10 Mars 2025</strong></p>
        </div>
        <div class="edit-profile">
            <h2>Modifier le profil</h2>
            <div class="form-group">
                <label>Votre nom</label>
                <input type="text" value="James">
            </div>
            <div class="form-group">
                <label>Nom d'utilisateur</label>
                <input type="text" value="Allan">
            </div>
            <div class="form-group">
                <label>Mot de passe</label>
                <input type="password" value="**********">
            </div>
            <div class="form-group">
                <label>Confirmer le mot de passe </label>
                <input type="password" value="**********">
            </div>
            <div class="form-group">
                <label>Adresse e-mail </label>
                <input type="email" value="demomail@mail.com">
            </div>
            <div class="form-group">
                <label>Confirmer l'adresse e-mail </label>
                <input type="email" value="demomail@mail.com">
            </div>
            <button class="update-btn">Mettre à jour les informations</button>
        </div>
    </div>
</body>
</html>

<?php
session_start();
<<<<<<< HEAD
include('../dbconfig/index.php'); // Assurez-vous que la connexion est bien établie

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['nom']) && !empty($_POST['telephone']) && !empty($_POST['Email']) && !empty($_POST['motdepasse'])) {
        
        $nom = $_POST['nom'];
        $telephone = $_POST['telephone'];
        $Email = $_POST['Email'];
        $motdepasse = $_POST['motdepasse'];
=======
include('../dbconfig/index.php'); // Vérifiez que la connexion est bien établie

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['nom']) && !empty($_POST['telephone']) && !empty($_POST['Email']) && !empty($_POST['motdepasse']) && !empty($_POST['id'])) {
        
        $nom = htmlspecialchars($_POST['nom']);
        $telephone = htmlspecialchars($_POST['telephone']);
        $Email = htmlspecialchars($_POST['Email']);
        $motdepasse = password_hash($_POST['motdepasse'], PASSWORD_DEFAULT); // Hachage du mot de passe
        $id = intval($_POST['id']); // Sécurisation de l'ID
>>>>>>> d923630e7c98a7d2fe6a1634a3629bf8e752f336
        
        // Préparer et exécuter la requête sécurisée
        $stmt = $conn->prepare("UPDATE personne SET nom = ?, telephone = ?, Email = ?, motdepasse = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $nom, $telephone, $Email, $motdepasse, $id);
        
        if ($stmt->execute()) {
            echo "<script>alert('Mise à jour réussie !');</script>";
        } else {
<<<<<<< HEAD
            echo "<script>alert('Erreur lors de la mise à jour');</script>";
=======
            echo "<script>alert('Erreur lors de la mise à jour.');</script>";
>>>>>>> d923630e7c98a7d2fe6a1634a3629bf8e752f336
        }
        
        $stmt->close();
    } else {
        echo "<script>alert('Veuillez remplir tous les champs.');</script>";
    }
}

$conn->close();
?>

<<<<<<< HEAD







=======
>>>>>>> d923630e7c98a7d2fe6a1634a3629bf8e752f336
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<<<<<<< HEAD
    <title>Profil Utilisateur</title>
    <style>
        body {
=======
    <title>Modification du Profil</title>
    <style>
        body {
            margin: 0;
            padding-top: 70px;
>>>>>>> d923630e7c98a7d2fe6a1634a3629bf8e752f336
            font-family: Arial, sans-serif;
            background: #f8f4ef;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
<<<<<<< HEAD
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
=======
        }

        .container {
            display: flex;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            max-width: 800px;
            width: 100%;
        }

>>>>>>> d923630e7c98a7d2fe6a1634a3629bf8e752f336
        .profile-card {
            width: 250px;
            text-align: center;
            padding: 20px;
            border-right: 1px solid #ddd;
        }
<<<<<<< HEAD
=======

>>>>>>> d923630e7c98a7d2fe6a1634a3629bf8e752f336
        .profile-card img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
        }
<<<<<<< HEAD
=======

>>>>>>> d923630e7c98a7d2fe6a1634a3629bf8e752f336
        .profile-card button {
            background: #e8a043;
            color: white;
            border: none;
            padding: 10px;
            width: 100%;
            border-radius: 5px;
            cursor: pointer;
<<<<<<< HEAD
            margin-top: 10px;
        }
=======
            font-size: 16px;
            margin-top: 10px;
        }

        .profile-card button:hover {
            background: #d18f38;
        }

>>>>>>> d923630e7c98a7d2fe6a1634a3629bf8e752f336
        .edit-profile {
            flex: 1;
            padding: 20px;
        }
<<<<<<< HEAD
        .edit-profile h2 {
            margin-bottom: 10px;
        }
        .form-group {
            margin-bottom: 10px;
        }
=======

        .edit-profile h2 {
            margin-bottom: 10px;
        }

        .form-group {
            margin-bottom: 10px;
        }

>>>>>>> d923630e7c98a7d2fe6a1634a3629bf8e752f336
        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
<<<<<<< HEAD
=======

>>>>>>> d923630e7c98a7d2fe6a1634a3629bf8e752f336
        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
<<<<<<< HEAD
        .update-btn {
            background: #e8a043;
            color: white;
            border: none;
            padding: 10px;
            width: 100%;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
=======

        .update-btn {
            background: #e8a043;
            color: white;
            padding: 10px;
            width: 100%;
            border: none;
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            font-size: 16px;
            cursor: pointer;
        }

        .update-btn:hover {
            background: #d18f38;
        }

        .custom-alert {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: #4CAF50;
            color: white;
            padding: 10px;
            border-radius: 5px;
            display: none;
>>>>>>> d923630e7c98a7d2fe6a1634a3629bf8e752f336
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="profile-card">
            <img src="../assets/user.png" alt="Photo de Profil">
<<<<<<< HEAD
            <h3>Avocat James</h3>
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
=======
            <h3>James Allan</h3>
            <p>@james</p>
            <button>Téléverser une nouvelle photo</button>
            <p>Membre depuis : <strong>10 Mars 2025</strong></p>
        </div>
        <div class="edit-profile">
            <h2>Modifier le profil</h2>
            <form id="updateForm" method="post" action="">
                <input type="hidden" name="id" value="<?= $_SESSION['user_id'] ?? '' ?>"> <!-- Assurez-vous que la session contient l'ID -->

                <div class="form-group">
                    <label for="nom">Nom :</label>
                    <input type="text" id="nom" name="nom" required>
                </div>

                <div class="form-group">
                    <label for="Email">Email :</label>
                    <input type="email" id="Email" name="Email" required>
                </div>

                <div class="form-group">
                    <label for="telephone">Téléphone :</label>
                    <input type="text" id="telephone" name="telephone" required>
                </div>

                <div class="form-group">
                    <label for="motdepasse">Mot de passe :</label>
                    <input type="password" id="motdepasse" name="motdepasse" required>
                </div>

                <button type="submit" class="update-btn">Mettre à jour</button>
            </form>
        </div>
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
            alertBox.style.display = "block";
            setTimeout(closeCustomAlert, 2000);
        }

        function closeCustomAlert() {
            document.getElementById("customAlert").style.display = "none";
        }
    </script>
>>>>>>> d923630e7c98a7d2fe6a1634a3629bf8e752f336
</body>
</html>

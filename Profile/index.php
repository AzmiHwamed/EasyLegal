<?php
session_start();
include('../dbconfig/index.php'); // Connexion à la base de données


function mettreAJourProfil($id, $nom, $Email, $motdepasse, $telephone) {
    global $conn;

    if (!empty($motdepasse)) {
        
        $sql = "UPDATE personne SET nom=?, Email=?, motdepasse=?, telephone=? WHERE id=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssssi", $nom, $Email, $motdepasse, $telephone, $role, $id);
    } else {
        $sql = "UPDATE personne SET nom=?, Email=?, telephone=?  WHERE id=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssi", $nom, $Email, $telephone, $id);
    }

    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $result;
}




if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $id = $_SESSION['id'] ?? null;
    $nom = $_POST['nom'];
    $email = $_POST['Email'];
    $motdepasse = $_POST['motdepasse'];
    $telephone = $_POST['telephone'];

    if ($id) {
        if (mettreAJourProfil($id, $nom, $email, $motdepasse, $telephone)) {
            $_SESSION['success_message'] = "Profil mis à jour avec succès !";
        } else {
            $_SESSION['error_message'] = "Erreur lors de la mise à jour.";
        }
    }
    header("Location: ./index.php"); 
    exit();
}
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
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
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
            font-size: 16px;
            margin-top: 10px;
        }

        .profile-card button:hover {
            background: #d18f38;
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
        }
    </style>
</head>
<body>
    
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="custom-alert" style="display: block; background: #4CAF50;">
            ✅ <?= $_SESSION['success_message']; ?>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="custom-alert" style="display: block; background: #E74C3C;">
            ❌ <?= $_SESSION['error_message']; ?>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>
    <?php
                $id = $_SESSION['id'] ?? null;
                if ($id) {
                    $sql = "SELECT nom, Email, telephone FROM personne WHERE id=?";
                    $stmt = mysqli_prepare($conn, $sql);
                    mysqli_stmt_bind_param($stmt, "i", $id);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_bind_result($stmt, $nom, $Email, $telephone);
                    mysqli_stmt_fetch($stmt);
                    mysqli_stmt_close($stmt);
                }
                ?>
    <div class="container">
    
        <div class="profile-card">
            <img src="../assets/user.png" alt="Photo de Profil">
            <h3><?= htmlspecialchars($nom ?? '') ?></h3>
            <p><?= htmlspecialchars($Email ?? '') ?></p>
            <button>Téléverser une nouvelle photo</button>
        </div>
        <div class="edit-profile">
            <h2>Modifier le profil</h2>
            <form id="updateForm" method="post">
                <input type="hidden" name="id" value="<?= $_SESSION['user_id'] ?? '' ?>">

              

                <div class="form-group">
                    <label for="nom">Nom :</label>
                    <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($nom ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label for="Email">Email :</label>
                    <input type="email" id="Email" name="Email" value="<?= htmlspecialchars($Email ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label for="telephone">Téléphone :</label>
                    <input type="text" id="telephone" name="telephone" value="<?= htmlspecialchars($telephone ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label for="motdepasse">Mot de passe (laisser vide pour ne pas changer) :</label>
                    <input type="password" id="motdepasse" name="motdepasse">
                </div>

                <button type="submit" name="update" class="update-btn">Mettre à jour</button>
            </form>
        </div>
    </div>

</body>
</html>

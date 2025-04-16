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
    :root {
    --primary-color: #e8a043;
    --primary-hover: #d18f38;
    --bg-color: #f8f4ef;
    --white: #ffffff;
    --border-color: #ddd;
    --input-border: #ccc;
    --success-color: #4CAF50;
    --cancel-color: #e0e0e0;
    --cancel-hover: #cacaca;
    --font-family: 'Arial', sans-serif;
}

body {
    margin: 0;
    padding-top: 70px;
    font-family: var(--font-family);
    background: var(--bg-color);
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
}

.container {
    display: flex;
    flex-wrap: wrap;
    background: var(--white);
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
    max-width: 900px;
    width: 90%;
    transition: all 0.3s ease-in-out;
}

.profile-card {
    flex: 0 0 250px;
    text-align: center;
    padding: 20px;
    border-right: 1px solid var(--border-color);
}

.profile-card img {
    width: 110px;
    height: 110px;
    border-radius: 50%;
    object-fit: cover;
    margin-bottom: 20px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

.profile-card button {
    background: var(--primary-color);
    color: var(--white);
    border: none;
    padding: 12px;
    width: 100%;
    border-radius: 8px;
    font-size: 16px;
    cursor: pointer;
    transition: background 0.3s ease, transform 0.2s ease;
}

.profile-card button:hover {
    background: var(--primary-hover);
    transform: translateY(-2px);
}

.edit-profile {
    flex: 1;
    padding: 20px;
}

.edit-profile h2 {
    margin-bottom: 20px;
    font-size: 24px;
    color: #333;
}

.form-group {
    margin-bottom: 16px;
}

.form-group label {
    display: block;
    font-weight: 600;
    margin-bottom: 6px;
    color: #444;
}

.form-group input {
    width: 100%;
    padding: 10px;
    border: 1px solid var(--input-border);
    border-radius: 8px;
    font-size: 15px;
    transition: border-color 0.2s ease;
}

.form-group input:focus {
    border-color: var(--primary-color);
    outline: none;
}

.button-group {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    margin-top: 25px;
}

.update-btn,
.cancel-btn {
    padding: 12px 20px;
    font-size: 16px;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    transition: background 0.3s ease, transform 0.2s ease;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

/* Mettre à jour */
.update-btn {
    background: var(--primary-color);
    color: var(--white);
}

.update-btn:hover {
    background: var(--primary-hover);
    transform: scale(1.03);
}

/* Annuler */
.cancel-btn {
    background: var(--cancel-color);
    color: #333;
}

.cancel-btn:hover {
    background: var(--cancel-hover);
    transform: scale(1.03);
}

.custom-alert {
    position: fixed;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    background: var(--success-color);
    color: var(--white);
    padding: 12px 20px;
    border-radius: 8px;
    display: none;
    font-weight: bold;
    z-index: 999;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

@media (max-width: 768px) {
    .container {
        flex-direction: column;
        padding: 15px;
    }

    .profile-card {
        border-right: none;
        border-bottom: 1px solid var(--border-color);
        margin-bottom: 15px;
    }

    .button-group {
        flex-direction: column;
        align-items: stretch;
    }

    .update-btn,
    .cancel-btn {
        width: 100%;
    }
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
                <button type="button" class="cancel-btn" onclick="window.location.href='../index.php'">Annuler</button>

            </form>
        </div>
    </div>

</body>
</html>

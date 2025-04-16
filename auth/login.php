<?php
session_start();
include('../dbconfig/index.php');
include('../validateur.php');
$emailError = $passwordError = $loginError = "";

if (isset($_POST['Email']) && isset($_POST['motdepasse'])) {

    $stmt = $conn->prepare("SELECT * FROM personne WHERE Email = ?");
    $stmt->bind_param('s', $_POST['Email']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        $loginError = "Utilisateur non trouvé";
    } else {
        $user = $result->fetch_object();

        // Vérifier si l'utilisateur est suspendu
        if ($user->statut === 'suspendu') {
            $loginError = "Votre compte est suspendu. Veuillez contacter l'administration.";
        } elseif ($_POST['motdepasse'] == $user->motdepasse) {
            $_SESSION['id'] = $user->id;
            $_SESSION['role'] = $user->role;
            header('Location: ../' . $user->role . '/index.php');
            exit;
        } else {
            $loginError = "Mot de passe incorrect";
        }
    }
} else {
    $emailError = "Veuillez entrer un email valide.";
    $passwordError = "Le mot de passe doit contenir au moins 6 caractères.";
}
?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <style>
 

    body {
    font-family: Arial, sans-serif;
    background: #f8f4ef;
    text-align: center;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.login-container {
    width: 100%;
    max-width: 500px;
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    animation: fadeIn 0.5s ease-in-out;
}

h2 {
    color: #d38d2c;
    margin-bottom: 15px;
}

input[type="text"], input[type="password"] {
    width: 90%;
    text-align: center;
    padding: 12px;
    margin: 10px 0;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 16px;
    transition: border 0.3s ease-in-out;
}

input[type="text"]:focus, input[type="password"]:focus {
    border-color: #d38d2c;
    outline: none;
}

.error-message {
    color: red;
    font-size: 13px;
    margin-bottom: 10px;
}

button {
    background: #d38d2c;
    color: white;
    padding: 12px;
    width: 100%;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 16px;
    font-weight: bold;
    text-transform: uppercase;
    transition: all 0.3s ease-in-out;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

button:hover {
    background: #b37424;
    transform: translateY(-2px);
    box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
}

button:active {
    transform: translateY(1px);
    box-shadow: 0 3px 5px rgba(0, 0, 0, 0.2);
}

a {
    color: #d38d2c;
    text-decoration: none;
    font-weight: bold;
    display: inline-block;
    margin-top: 15px;
    transition: color 0.3s ease-in-out;
}

a:hover {
    text-decoration: underline;
    color: #b37424;
}

/* Animation d’apparition */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
</head>
<body>



    <div class="login-container">
        <h2>Connexion</h2>

        <?php if (!empty($loginError)): ?>
            <div class="error-message"><?php echo $loginError; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div>
                <label for="email">Email :</label>
                <input type="text" id="email" name="Email" value="<?php echo htmlspecialchars($email ?? ''); ?>">
                <?php if (!empty($emailError)): ?>
                    <div class="error-message"><?php echo $emailError; ?></div>
                <?php endif; ?>
            </div>

            <div>
                <label for="password">Mot de passe :</label>
                <input type="password" id="password" name="motdepasse">
                <?php if (!empty($passwordError)): ?>
                    <div class="error-message"><?php echo $passwordError; ?></div>
                <?php endif; ?>
            </div>

            <button type="submit">Se connecter</button>
        </form>
        <br>
        <a href="registration.php">Créer un compte</a>
    </div>
</body>
</html>

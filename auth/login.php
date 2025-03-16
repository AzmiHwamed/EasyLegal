<?php
session_start();
include('../dbconfig/index.php');

$emailError = $passwordError = $loginError = "";
    if (isset($_POST['Email']) && isset($_POST['motdepasse'])) {

        $stmt = $conn->prepare("SELECT * FROM personne WHERE Email = ?");
        $stmt->bind_param('s', $_POST['Email']);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            echo"3";
            $loginError = "Utilisateur non trouvé";
        } else {
            echo"4";
            $user = $result->fetch_object();
            if ($_POST['motdepasse'] == $user->motdepasse) {
                echo"5";
                $_SESSION['id'] = $user->id;
                $_SESSION['role'] = $user->role;
                header('Location: ../'.$user->role.'/index.php');
                exit;

            } else {
                echo"6";
                // Mot de passe incorrect
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
        }
        .login-container {
            width: 350px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .error-message {
            color: red;
            font-size: 12px;
        }
        button {
            background: #e8a043;
            color: white;
            padding: 10px;
            width: 100%;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background: #d18f38;
        }
        a {
            color: #e8a043;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
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

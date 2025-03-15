<?php
session_start();
include('../dbconfig/index.php');

// Initialisation des variables d'erreur
$emailError = $passwordError = $loginError = "";

// Traitement de la connexion
if (isset($_POST['Email']) && isset($_POST['motdepasse'])) {
    $email = $_POST['Email'];
    $password = $_POST['motdepasse'];

    // Validation côté serveur pour la connexion
    if (empty($email)) {
        $emailError = "Veuillez entrer un email valide.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailError = "L'email n'est pas valide.";
    }

    if (empty($password)) {
        $passwordError = "Veuillez entrer un mot de passe.";
    } elseif (strlen($password) < 6) {
        $passwordError = "Le mot de passe doit contenir au moins 6 caractères.";
    }

    if (empty($emailError) && empty($passwordError)) {
        // Préparer la requête pour vérifier l'existence de l'email
        $stmt = $conn->prepare("SELECT * FROM personne WHERE Email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        // Vérifier si l'email existe dans la base de données
        if ($result->num_rows == 0) {
            $loginError = "Utilisateur non trouvé"; // L'email n'existe pas dans la base de données
        } else {
            $user = $result->fetch_object();

            // Vérification du mot de passe avec password_verify()
            if (password_verify($password, $user->motdepasse)) {
                // Si le mot de passe est correct
                $_SESSION['id'] = $user->id;
                $_SESSION['role'] = $user->role;
                header('Location: ../'.$user->role.'/index.php');
                exit;
            } else {
                // Mot de passe incorrect
                $loginError = "Mot de passe incorrect";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <style>
        /* Styles pour la page de connexion */
        body {
            margin: 0;
            padding-top: 70px; /* Pour éviter que le contenu soit caché sous le nav */
            font-family: Arial, sans-serif;
            background: #f8f4ef;
        }
        .login-container {
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
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            display: block;
        }
        .input-group {
            position: relative;
            text-align: left;
        }
        .error-message {
            color: red;
            font-size: 12px;
            margin-top: -8px;
            margin-bottom: 8px;
            display: block;
        }
        .password-container {
            position: relative;
            width: 100%;
        }
        .toggle-password {
            position: absolute;
            right: 10px;
            top: 40%;
            transform: translateY(-50%);
            cursor: pointer;
        }
        button {
            background-color: #e8a043;
            color: white;
            border: none;
            padding: 10px;
            width: 100%;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #d18f38;
        }
        a {
            text-decoration: none;
            color: #e8a043;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <h2>Connexion</h2>
        
        <!-- Affichage des erreurs de connexion -->
        <?php if ($loginError): ?>
            <div class="error-message"><?php echo $loginError; ?></div>
        <?php endif; ?>
        
        <form id="loginForm" method="POST" autocomplete="off">
            <div class="input-group">
                <label for="email">Email:</label>
                <input type="text" id="email" name="Email" value="<?php echo isset($_POST['Email']) ? $_POST['Email'] : ''; ?>">
                <?php if ($emailError): ?>
                    <div class="error-message"><?php echo $emailError; ?></div>
                <?php endif; ?>
            </div>

            <div class="input-group password-container">
                <label for="password">Mot de passe:</label>
                <input type="password" id="password" name="motdepasse">
                <span class="toggle-password">👁️</span>
                <?php if ($passwordError): ?>
                    <div class="error-message"><?php echo $passwordError; ?></div>
                <?php endif; ?>
            </div>

            <button type="submit">Se connecter</button>
        </form>
        <br>
        <a href="registration.php">Créer un compte</a>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const passwordField = document.getElementById("password");
            const togglePassword = document.querySelector(".toggle-password");

            // Afficher/Masquer le mot de passe
            togglePassword.addEventListener("click", function () {
                if (passwordField.type === "password") {
                    passwordField.type = "text";
                    togglePassword.textContent = "👁️";
                } else {
                    passwordField.type = "password";
                    togglePassword.textContent = "👁️";
                }
            });
        });
    </script>
</body>
</html>

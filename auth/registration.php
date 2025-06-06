<?php
session_start();
include('../dbconfig/index.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['nom']) && !empty($_POST['telephone']) && !empty($_POST['Email']) && !empty($_POST['motdepasse']) && !empty($_POST['role'])) {
        $nom = $_POST['nom'];
        $telephone = $_POST['telephone'];
        $role = $_POST['role'];
        $Email = $_POST['Email'];
        $motdepasse = $_POST['motdepasse'];
        $found = true;

        if ($role === 'expert') {
            $stmt = $conn->prepare("SELECT * FROM refrenceexpert WHERE Nom = ? AND telephone = ?");
            $stmt->bind_param("ss", $_POST['nom'], $_POST['telephone']);
            $stmt->execute();
            $result = $stmt->get_result();
            $found = ($result->num_rows > 0);
            $stmt->close(); // Close statement here
            if (!$found) {
                echo "<script>alert('vos essayer de sinscrire en tant que expert , verifer vos donnée ou changer votre type d'utilisateur');</script>";
            }
        } else {
            echo "Failed to open the file.";
        }

        if ($found) {
            $s = $conn->prepare("SELECT * FROM personne WHERE Email = ?");
            $s->bind_param("s", $Email);
            $s->execute();
            $r = $s->get_result();
            if ($r->num_rows > 0) {
                echo "<script>alert('email deja utilisé');</script>";
            } else {
                $stmt = $conn->prepare("INSERT INTO personne (nom, telephone, role, Email, motdepasse) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssss", $nom, $telephone, $role, $Email, $motdepasse);
                if ($stmt->execute()) {
                    $stmt->close(); // Close after insert
                    $s->close(); // Close select statement
                    $conn->close();
                    echo "<script>alert('done')</script>";
                    header('Location: login.php');
                    exit();
                } else {
                    $stmt->close(); // Close even if insert fails
                    $s->close();
                    $conn->close();
                    header('Location: regerreur.php');
                    exit();
                }
            }
            $s->close(); // Close after checking email (if email exists case)
        }
    } else {
        // TODO: Handle empty fields
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <style>
        
        /* Ajustement du corps de la page */
        body {
            margin: 0;
            padding-top: 70px; /* Évite que le contenu soit caché sous le nav */
            font-family: Arial, sans-serif;
            background: #f8f4ef;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        h2 {
    color: #d38d2c;
    margin-bottom: 15px;
}

        /* Conteneur d'inscription */
        .registration-container {
            width: 100%;
            max-width: 400px;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
            text-align: center;
            animation: fadeIn 0.5s ease-in-out;
        }

        /* Formulaire et champs de saisie */
        input[type="text"], input[type="password"], input[type="tel"] ,select , input[type="email"]{
            width: 90%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            transition: border 0.3s ease-in-out;
            text-align: center;

            
        }

        input[type="text"]:focus, input[type="password"]:focus, input[type="number"]:focus {
            border-color: #d38d2c;
            outline: none;
            box-shadow: 0px 0px 6px rgba(211, 141, 44, 0.5);

        }

        .input-group {
            position: relative;
            text-align: center;
        }

        .error-message {
            color: red;
            font-size: 12px;
            margin-top: -8px;
            margin-bottom: 8px;
            display: none;
        }

        /* Contenu du mot de passe */
        .password-container {
            position: relative;
            width: 100%;
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 18px;
            color: #888;
            transition: color 0.3s ease-in-out;
        }

        .toggle-password:hover {
            color: #d38d2c;
        }

        #eyeOpen, #eyeClosed {
            width: 22px;
            height: 22px;
        }

        /* Bouton d'inscription */
        button {
            background: #d38d2c;
            color: white;
            border: none;
            padding: 12px;
            width: 100%;
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

        /* Liens */
        a {
            text-decoration: none;
            color: #d38d2c;
            font-weight: bold;
            transition: color 0.3s ease-in-out;
        }

        a:hover {
            text-decoration: underline;
            color: #b37424;
        }

        /* Styles pour l'alerte personnalisée */
        .custom-alert {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: #d38d2c;
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
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

        .custom-alert button {
            background: none;
            border: none;
            color: white;
            font-size: 18px;
            cursor: pointer;
            transition: color 0.3s ease-in-out;
        }

        .custom-alert button:hover {
            color: #f8f4ef;
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

        /* Responsive */
        @media screen and (max-width: 600px) {
            nav {
                padding: 0 10px;
            }

            .registration-container {
                max-width: 90%;
            }
        }
    </style>
</head>
<body>



    <div class="registration-container">
        <h2>Inscription</h2>
        <form id="registrationForm" autocomplete="off" method="post" action="#">

            <div class="input-group">
                <label for="name">Nom et Prenom:</label>
                <input type="text" id="nom" name="nom">
                <div class="error-message" id="nameError">Veuillez entrer votre nom.</div>
            </div>

            <div class="input-group">
              <label for="role">Rôle :</label>
                    <select id="role" name="role" required>
                    <option value="">-- Sélectionnez un rôle --</option>
                    <option value="user">User</option>
                    <option value="expert">Expert</option>
                     </select>
            <div class="error-message" id="roleError">Veuillez sélectionner votre rôle.</div>
            </div>

            <div class="input-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="Email">
                <div class="error-message" id="emailError">Veuillez entrer une adresse e-mail valide.</div>
            </div>

            <div class="input-group password-container">
    <label for="password">Mot de passe:</label>
    <input type="password" id="password" name="motdepasse" oninput="validatePassword()">
    <span class="toggle-password" id="togglePassword" onclick="togglePasswordVisibility()">
        <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-width="2" d="M1.5 12s3.5-7 10.5-7 10.5 7 10.5 7-3.5 7-10.5 7S1.5 12 1.5 12z"/>
            <circle cx="12" cy="12" r="3" stroke-width="2"/>
        </svg>
        <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
            <path stroke-width="2" d="M3 3l18 18M10.584 10.586A2 2 0 0112 12a2 2 0 002 2m3.3-1.3c.7-.8 1.3-1.7 1.7-2.7-1.4-3.3-4.6-6-8.9-6a9.77 9.77 0 00-4.9 1.4"/>
            <path stroke-width="2" d="M9.88 9.88a3 3 0 014.24 4.24M6.7 6.7C5.5 7.9 4.6 9.3 4 10.9c1.4 3.3 4.6 6 8.9 6 1.6 0 3.1-.4 4.4-1.1"/>
        </svg>
    </span>
    <small id="passwordError" style="color: red; display: none;">Le mot de passe doit contenir au moins 6 caractères.</small>
</div>

<script>
function togglePasswordVisibility() {
    const passwordInput = document.getElementById("password");
    const eyeOpen = document.getElementById("eyeOpen");
    const eyeClosed = document.getElementById("eyeClosed");

    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        eyeOpen.style.display = "none";
        eyeClosed.style.display = "inline";
    } else {
        passwordInput.type = "password";
        eyeOpen.style.display = "inline";
        eyeClosed.style.display = "none";
    }
}

function validatePassword() {
    const passwordInput = document.getElementById("password");
    const errorText = document.getElementById("passwordError");

    if (passwordInput.value.length < 6) {
        errorText.style.display = "block";
    } else {
        errorText.style.display = "none";
    }
}
</script>


            <div class="input-group">
                <label for="phone">Téléphone:</label>
                <input type="tel" id="phone" name="telephone"   maxlength="8" required>
                <div class="error-message" id="phoneError">Veuillez entrer un numéro de téléphone valide.</div>
            
</div>


            <button type="submit">S'inscrire</button>
        </form>

        <br>
        <a href="login.php">Déjà un compte ? Connectez-vous</a>
    </div>

    <!-- Alerte personnalisée -->
    <div id="customAlert" class="custom-alert">
        ✅ Inscription réussie !
        <button onclick="closeCustomAlert()">✖</button>
    </div>

    <!-- JS -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const passwordField = document.getElementById("password");
            const togglePassword = document.getElementById("togglePassword");
            const eyeOpen = document.getElementById("eyeOpen");
            const eyeClosed = document.getElementById("eyeClosed");

            togglePassword.addEventListener("click", function () {
                if (passwordField.type === "password") {
                    passwordField.type = "text";
                    eyeOpen.style.display = "none";
                    eyeClosed.style.display = "inline";
                } else {
                    passwordField.type = "password";
                    eyeOpen.style.display = "inline";
                    eyeClosed.style.display = "none";
                }
            });
        });


        // Afficher l'alerte personnalisée
        function showCustomAlert() {
            const alertBox = document.getElementById("customAlert");
            alertBox.classList.add("show");

            setTimeout(closeCustomAlert, 3000);
        }

        // Fermer l'alerte personnalisée
        function closeCustomAlert() {
            const alertBox = document.getElementById("customAlert");
            alertBox.classList.remove("show");
        }
    </script>

</body>
</html>
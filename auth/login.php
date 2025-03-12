<?php
session_start();
include ('../dbconfig/index.php');
if ( ! empty( $_POST ) ) {
    if ( isset( $_POST['username'] ) && isset( $_POST['password'] ) ) {
        $stmt = $conn->prepare("SELECT * FROM personne WHERE nom = ?");
    if ( isset( $_POST['email'] ) && isset( $_POST['password'] ) ) {
        echo "set";
        $stmt = $conn->prepare("SELECT * FROM personne WHERE Email = ?");
        $stmt->bind_param('s', $_POST['email']);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows == 0) {
            echo 'user not found';
        }
        else{
        $user = $result->fetch_object();
    	if ( $_POST['password'] == $user->motdepasse ) {
            echo 'success';
    		$_SESSION['user_id'] = $user->ID;
            $_SESSION['type'] = $user->role;     
            header('Location: ../'.$user->Role.'/index.php');    
    	}
        else{
            echo 'password wrong';
        }
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
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background: #fff;
            padding: 20px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            width: 320px;
            text-align: center;
        }
        input[type="text"], input[type="password"] {
            width: calc(100% - 24px);
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
            display: none;
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
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px;
            width: 100%;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
        a {
            text-decoration: none;
            color: #007bff;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <h2>Connexion</h2>
        <form id="loginForm" autocomplete="off">
            
            <div class="input-group">
                <label for="name">Nom:</label>
                <input type="text" id="name">
                <div class="error-message" id="nameError">Veuillez entrer votre nom.</div>
            </div>

            <div class="input-group">
                <label for="username">Nom d'utilisateur:</label>
                <input type="text" id="username">
                <div class="error-message" id="usernameError">Veuillez entrer un nom d'utilisateur.</div>
            </div>

            <div class="input-group">
                <label for="email">Email:</label>
                <input type="text" id="email">
                <div class="error-message" id="emailError">Veuillez entrer une adresse e-mail valide.</div>
            </div>

            <div class="input-group password-container">
                <label for="password">Mot de passe:</label>
                <input type="password" id="password">
                <span class="toggle-password">👁️</span>
                <div class="error-message" id="passwordError">Le mot de passe doit contenir au moins 6 caractères.</div>
            </div>

            <button type="submit">Se connecter</button>
        </form>
        <br>
        <a href="registration.php">Créer un compte</a>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const form = document.getElementById("loginForm");
            const passwordField = document.getElementById("password");
            const togglePassword = document.querySelector(".toggle-password");

            form.addEventListener("submit", function (event) {
                event.preventDefault();

                let name = document.getElementById("name").value.trim();
                let username = document.getElementById("username").value.trim();
                let email = document.getElementById("email").value.trim();
                let password = passwordField.value.trim();

                let isValid = true;

                // Réinitialiser les messages d'erreur
                document.querySelectorAll(".error-message").forEach(error => error.style.display = "none");

                if (name === "") {
                    document.getElementById("nameError").style.display = "block";
                    isValid = false;
                }

                if (username === "") {
                    document.getElementById("usernameError").style.display = "block";
                    isValid = false;
                }

                if (!validateEmail(email)) {
                    document.getElementById("emailError").style.display = "block";
                    isValid = false;
                }

                if (password.length < 6) {
                    document.getElementById("passwordError").style.display = "block";
                    isValid = false;
                }

                if (isValid) {
                    alert("Connexion réussie !");
                    form.submit();
                }
            });

            function validateEmail(email) {
                const re = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                return re.test(email);
            }

            // Afficher/Masquer le mot de passe
            togglePassword.addEventListener("click", function () {
                if (passwordField.type === "password") {
                    passwordField.type = "text";
                    togglePassword.textContent = "";
                } else {
                    passwordField.type = "password";
                    togglePassword.textContent = "";
                }
            });
        });
    </script>

</body>
</html>

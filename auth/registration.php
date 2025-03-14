<?php
session_start();
include('../dbconfig/index.php'); // Assurez-vous que la connexion est bien établie


if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    if (!empty($_POST['nom']) && !empty($_POST['telephone']) && !empty($_POST['Email']) && !empty($_POST['motdepasse'])) 
    {
        $nom = $_POST['nom'];
        $telephone = $_POST['telephone'];
        $role = 'user';
        $Email = $_POST['Email'];
        $motdepasse =$_POST['motdepasse']; 

        // Préparer et exécuter la requête sécurisée
        $stmt = $conn->prepare("INSERT INTO personne (nom, telephone, role, Email, motdepasse) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nom, $telephone, $role, $Email, $motdepasse);

        if ($stmt->execute()) {
            //TODO: navigation
        } else {
            //TODO : affichage de page de probleme saret    
        }

        $stmt->close();
    } else {
        //TODO : les champs
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
        /* Styles pour la barre de navigation */
nav {
    width: 100%;
    height: 60px;
    background-color: #F3EEE5;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 20px;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1000;
}

nav a {
    text-decoration: none;
    color: black;
    font-weight: bold;
    font-size: 16px;
}

nav img {
    height: 40px;
}

/* Ajustement du corps de la page */
body {
    margin: 0;
    padding-top: 70px; /* Pour éviter que le contenu ne soit caché sous le nav */
    font-family: Arial, sans-serif;
    background: #f8f4ef;
}

/* Conteneur d'inscription */
.registration-container {
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

/* Formulaire et champs de saisie */
input[type="text"], input[type="password"], input[type="number"] {
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
    display: none;
}

/* Contenu du mot de passe */
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

/* Bouton d'inscription */
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

/* Liens */
a {
    text-decoration: none;
    color: #e8a043;
}

a:hover {
    text-decoration: underline;
}

/* Styles pour l'alerte personnalisée */
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

.custom-alert button {
    background: none;
    border: none;
    color: white;
    font-size: 18px;
    cursor: pointer;
}





    </style>
</head>
<body>


<nav>
        <a href="#">
            <img src="../assets/logo.png" alt="Icône de la justice" class="hero-image">
        </a>
        <span>
            <a href="#">Rechercher</a>
            <a href="#">Forum</a>
            <a href="#">Discuter</a>
        </span>
        <a><img src="../assets/Male User.png" alt="Account" style="width: 3vw !important;"></a>
    </nav>
    

    <div class="registration-container">
        <h2>Inscription</h2>
        <form id="registrationForm" autocomplete="off" method="post" action="#">
            

            <div class="input-group">
                <label for="name">Nom:</label>
                <input type="text" id="nom" name="nom">
                <div class="error-message" id="nameError">Veuillez entrer votre nom.</div>
            </div>

            <div class="input-group">
                <label for="email">Email:</label>
                <input type="text" id="email" name="Email">
                <div class="error-message" id="emailError">Veuillez entrer une adresse e-mail valide.</div>
            </div>

            <div class="input-group password-container">
                <label for="password">Mot de passe:</label>
                <input type="password" id="password" name="motdepasse">
                <span class="toggle-password">👁️</span>
                <div class="error-message" id="passwordError">Le mot de passe doit contenir au moins 6 caractères.</div>
            </div>

            <div class="input-group">
                <label for="phone">Téléphone:</label>
                <input type="number" id="phone" name="telephone">
                <div class="error-message" id="phoneError">Veuillez entrer un numéro de téléphone valide.</div>
            </div>


            <button type="submit">S'inscrire</button>
        </form>
        <br>
        <a href="login.php">Déjà un compte ? Connectez-vous</a>
    </div>

    

    
    <!-- Alerte personnalisée -->
<div id="customAlert" class="custom-alert">
    ✅ Inscription réussie !
    <button onclick="closeCustomAlert()">✖</button>
</div>


<!-- JS -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const form = document.getElementById("loginForm");
        const passwordField = document.getElementById("password");
        const togglePassword = document.querySelector(".toggle-password");
        
        form.addEventListener("submit", function (event) {
            event.preventDefault();

            let email = document.getElementById("email").value.trim();
            let password = passwordField.value.trim();

            let isValid = true;

            // Réinitialiser les messages d'erreur
            document.querySelectorAll(".error-message").forEach(error => error.style.display = "none");

            // Validation de l'email
            if (!validateEmail(email)) {
                document.getElementById("emailError").style.display = "block";
                isValid = false;
            }

            // Validation du mot de passe
            if (password.length < 6) {
                document.getElementById("passwordError").style.display = "block";
                isValid = false;
            }

            if (isValid) {
                // Afficher l'alerte personnalisée en cas de réussite
                showCustomAlert();
                setTimeout(function() {
                    form.submit();
                }, 3000); // Délai pour masquer l'alerte avant l'envoi du formulaire
            }
        });

        // Fonction pour valider l'email
        function validateEmail(email) {
            const re = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            return re.test(email);
        }

        // Afficher/Masquer le mot de passe
        togglePassword.addEventListener("click", function () {
            if (passwordField.type === "password") {
                passwordField.type = "text";
                togglePassword.textContent = "🙈"; // Remplacer le texte par une icône
            } else {
                passwordField.type = "password";
                togglePassword.textContent = "👁️"; // Remplacer le texte par l'icône "œil"
            }
        });
    });

    // Afficher l'alerte personnalisée
    function showCustomAlert() {
        const alertBox = document.getElementById("customAlert");
        alertBox.classList.add("show");

        // Masquer automatiquement après 3 secondes
        setTimeout(closeCustomAlert, 3000);
    }

    // Fermer l'alerte personnalisée
    function closeCustomAlert() {
        const alertBox = document.getElementById("customAlert");
        alertBox.classList.remove("show");
    }
</script>

</body>
</html>


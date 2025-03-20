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
            echo "<script>alert('done')</script>";
            header('Location: login.php');
            exit();
        } else {
            header('Location: regerreur.php');
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
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1000;
    transition: background 0.3s ease-in-out;
}

nav a {
    text-decoration: none;
    color: black;
    font-weight: bold;
    font-size: 16px;
    transition: color 0.3s ease-in-out;
}

nav a:hover {
    color: #d38d2c;
}

nav img {
    height: 40px;
}

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
input[type="text"], input[type="password"], input[type="number"] {
    width: 100%;
    padding: 12px;
    margin: 10px 0;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 16px;
    transition: border 0.3s ease-in-out;
}

input[type="text"]:focus, input[type="password"]:focus, input[type="number"]:focus {
    border-color: #d38d2c;
    outline: none;
    box-shadow: 0px 0px 6px rgba(211, 141, 44, 0.5);
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


<nav>
        <a href="#">
            <img src="../assets/logo.png" alt="Icône de la justice" class="hero-image">
        </a>
        <span>
        <a href="../search/index.php">Rechercher</a> <!-- Lien vers le dossier 'search' à la racine -->
        <a href="../forum/index.php">Rechercher</a> <!-- Lien vers le dossier 'search' à la racine -->
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


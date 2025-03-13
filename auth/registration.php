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
        body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    text-align: center;
    background: #f8f4ef;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

h1 {
    margin-top: 20px;
    font-size: 24px;
}

.registration-container {
    background: #fff;
    padding: 20px;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    width: 320px;
    text-align: center;
}

input[type="text"], input[type="password"], input[type="number"] {
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

a {
    text-decoration: none;
    color: #007bff;
}

a:hover {
    text-decoration: underline;
}

.features {
    display: flex;
    justify-content: space-around;
    margin: 20px 0;
    font-weight: bold;
}

.info {
    padding: 20px;
}

.info-box {
    background: white;
    margin: 20px auto;
    padding: 20px;
    width: 80%;
    border-radius: 10px;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
}

.info-box img {
    width: 80px;
    display: block;
    margin: 10px auto;
}

.highlight {
    color: #e8a043;
}

.explore {
    margin: 20px 0;
}

.number {
    font-weight: bold;
    color: #e8a043;
}

.explore-btn {
    background: #e8a043;
    color: white;
    padding: 10px 20px;
    border: none;
    font-size: 16px;
    cursor: pointer;
    border-radius: 5px;
}

footer {
    background: #f0e6d6;
    padding: 10px;
    margin-top: 20px;
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

    <div class="registration-container">
        <h2>Inscription</h2>
        <form id="registrationForm" autocomplete="off" method="post" Action="#">
            

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























<!-- js-->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const form = document.getElementById("registrationForm");
            const passwordField = document.getElementById("password");
            const togglePassword = document.querySelector(".toggle-password");

            form.addEventListener("submit", function (event) {
                event.preventDefault();

                let name = document.getElementById("nom").value.trim();
                let email = document.getElementById("email").value.trim();
                let password = passwordField.value.trim();
                let phone = document.getElementById("phone").value.trim();

                let isValid = true;

                // Réinitialiser les messages d'erreur
                document.querySelectorAll(".error-message").forEach(error => error.style.display = "none");

                if (name === "") {
                    document.getElementById("nameError").style.display = "block";
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

                if (phone.length < 8 || phone.length > 15) {
                    document.getElementById("phoneError").style.display = "block";
                    isValid = false;
                }

                if (isValid) {
                    alert("Inscription réussie !");
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



// new
        function showCustomAlert() {
    const alertBox = document.getElementById("customAlert");
    alertBox.classList.add("show");

    // Masquer automatiquement après 3 secondes
    setTimeout(closeCustomAlert, 3000);
}

function closeCustomAlert() {
    const alertBox = document.getElementById("customAlert");
    alertBox.classList.remove("show");
}




    </script>

</body>
</html>


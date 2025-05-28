<?php
session_start();
include('../dbconfig/index.php'); // Connexion à la base de données


// index.php  (seulement la fonction corrigée)
function mettreAJourProfil($id, $nom, $email, $motdepasse, $telephone)
{
    global $conn;

    if (!empty($motdepasse)) {            // mot de passe modifié
        // 1) requête = 5 paramètres
        $sql  = "UPDATE personne SET nom = ?, Email = ?, motdepasse = ?, telephone = ? 
                 WHERE id = ?";

        $stmt = mysqli_prepare($conn, $sql);

        // 2) « ssssi » = 4 strings + 1 int  (change le dernier ‘i’ en ‘s’ si id est VARCHAR)
        mysqli_stmt_bind_param(
            $stmt,
            "ssssi",
            $nom,
            $email,
            $motdepasse,   // → idéalement : password_hash($motdepasse, PASSWORD_DEFAULT)
            $telephone,
            $id
        );
    } else {                               // mot de passe inchangé
        $sql  = "UPDATE personne SET nom = ?, Email = ?, telephone = ? 
                 WHERE id = ?";

        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param(
            $stmt,
            "sssi",
            $nom,
            $email,
            $telephone,
            $id
        );
    }

    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $ok;
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
/* debut de code chat en js */
.chat-header a {
  text-decoration: none;
  color: white;
}

.copyright {
  font-size: 12px;
  text-align: center;
  padding-bottom: 10px;
}

.copyright a {
  text-decoration: none;
  color: #343c41;
}

#chatbot-toggle-btn {
  position: fixed;
  bottom: 20px;
  right: 20px;

  border: none;
  background-color: transparent;
  cursor: pointer;
  transition: all 0.3s ease;
  z-index: 1001; /* Ensure the button is above the chatbot popup */
}

.chatbot-popup {
  display: none;
  position: fixed;
  bottom: 90px;
  right: 20px;
  background-color: #fff;
  border-radius: 15px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
  width: 350px;
  max-width: 90%;
  z-index: 1000;
}

.chat-header {
  background-color: #e8a043;
  color: #fff;
  padding: 15px 20px;
  border-top-left-radius: 15px;
  border-top-right-radius: 15px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

#close-btn {
  background-color: transparent;
  border: none;
  color: #fff;
  font-size: 20px;
  cursor: pointer;
}

.chat-box {
  max-height: 350px;
  overflow-y: auto;
  padding: 15px 20px;
}

.chat-input {
  display: flex;
  align-items: center;
  padding: 10px 20px;
  border-top: 1px solid #ddd;
}

#user-input {
  font-family: "Poppins";
  flex: 1;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 12px;
  outline: none;
}

#send-btn {
  font-family: "Poppins", sans-serif;
  padding: 10px 20px;
  border: none;
  background-color: #e8a043;
  color: #fff;
  border-radius: 12px;
  margin-left: 10px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

#send-btn:hover {
  background-color: #e8a043;
}

.user-message {
  background-color: #f3f3f3;
  color: #333;
  padding: 14px;
  border-radius: 15px;
  margin-bottom: 15px;
  margin-top: 15px;
  margin-left: 10px; /* Push user message to the left */
  position: relative;
  display: flex;
  align-items: center;
  flex-direction: row-reverse; /* Move user message to the right */
}

.user-message::before {
  content: "\1F468"; /* Man emoji */
  position: absolute;
  bottom: -17px;
  right: -20px;
  margin-bottom: 7px;
  font-size: 20px;
  background-color: #e8a043;
  color: #fff;
  border-radius: 50%;
  width: 30px;
  height: 30px;
  display: flex;
  justify-content: center;
  align-items: center;
  box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
}

.bot-message {
  background-color: #e8a043;
  color: #fff;
  padding: 14px;
  border-radius: 15px;
  margin-bottom: 10px;
  margin-top: 15px;
  align-self: flex-start; /* Move bot message to the left */
  margin-right: 10px; /* Push bot message to the right */
  position: relative;
  display: flex;
  align-items: center;
  flex-direction: column; /* Adjust for button placement */
}

.bot-message::before {
  content: "\1F916"; /* Robot emoji */
  position: absolute;
  bottom: -17px;
  left: -14px;
  margin-bottom: 4px;
  font-size: 20px;
  background-color: #e8a043;
  color: #fff;
  border-radius: 50%;
  width: 30px;
  height: 30px;
  display: flex;
  justify-content: center;
  align-items: center;
  box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
}

.button-container {
  display: flex;
  justify-content: space-around;
  margin-top: 10px;
}

.button-container button {
  padding: 10px 50px;
  border: none;
  background-color: #e8a043;
  color: #fff;
  border-radius: 10px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.button-container button:hover {
  background-color: #e8a043;
}

        
        .card {
            border-radius: 8px;
            background-color: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .like-btn {
            color: red;
            cursor: pointer;
        }
        .question {
            background-color: #fce6b6;
            padding: 15px;
            border-radius: 8px;
            cursor: pointer;
        
        }
        .expert-response {
            background-color: #d9c9a5;
            padding: 15px;
            border-radius: 8px;
            
        }
/* fin de code chat en js */

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
                <script>
    const emailInput = document.getElementById('Email');
    const emailError = document.getElementById('emailError');

    emailInput.addEventListener('input', function () {
        const emailValue = emailInput.value;
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (emailRegex.test(emailValue)) {
            emailError.style.display = 'none';
        } else {
            emailError.style.display = 'block';
        }
    });
</script>
<div class="form-group">
    <label for="telephone">Téléphone :</label>
    <input type="text" id="telephone" name="telephone" value="<?= htmlspecialchars($telephone ?? '') ?>" required maxlength="8">
    <div class="error-message" id="telError" style="display:none; color:red;">
        Veuillez entrer exactement 8 chiffres.
    </div>
</div>

<script>
    const telInput = document.getElementById('telephone');
    const telError = document.getElementById('telError');

    telInput.addEventListener('input', function () {
        // Supprime les lettres/symboles, garde que chiffres et limite à 8 chiffres
        telInput.value = telInput.value.replace(/\D/g, '').slice(0, 8);

        // Affiche erreur si pas exactement 8 chiffres
        if (telInput.value.length === 8) {
            telError.style.display = 'none';
        } else {
            telError.style.display = 'block';
        }
    });
</script>

                

               <div class="form-group">
    <label for="motdepasse">Mot de passe (laisser vide pour ne pas changer) :</label>
    <input type="password" id="motdepasse" name="motdepasse" minlength="6" placeholder="Au moins 6 caractères">
</div>

<script>
    const form = document.querySelector('form'); // suppose que c'est dans un <form>
    const passwordInput = document.getElementById('motdepasse');

    form.addEventListener('submit', function(event) {
        const password = passwordInput.value.trim();
        // On accepte mot de passe vide (pas de changement), sinon minimum 6 caractères
        if (password !== "" && password.length < 6) {
            event.preventDefault();
            alert("Le mot de passe doit contenir au moins 6 caractères.");
            passwordInput.focus();
        }
    });
</script>


                <button type="submit" name="update" class="update-btn">Mettre à jour</button>
                <button type="button" class="cancel-btn" onclick="window.location.href='../index.php'">Annuler</button>

            </form>
        </div>
    </div>
    </script>
<!-- debut de code chat html -->
<button id="chatbot-toggle-btn" onclick="toggleChatbot()"><img src="https://www2.stardust-testing.com/hs-fs/hubfs/banner-robot-service.png?width=1463&height=731&name=banner-robot-service.png"  style="border-radius:25px" width="50" height="50" alt="buttonpng" /></button>
<div class="chatbot-popup" id="chatbot-popup" style="display:none;">
  <div class="chat-header">
    <span>Chatbot | <a href="#" target="_blank"> EasyLegal</a></span>
    <button id="close-btn" onclick="toggleChatbot()">&times;</button>
  </div>
  <div class="chat-box" id="chat-box"></div>
  <div class="chat-input">
    <input type="text" id="user-input" placeholder="Type a message...">
    <button id="send-btn" onclick="sendMessage()">Envoyer</button>
  </div>
  <div class="copyright">
  </div>
</div>
<!-- fin de code chat html -->
 <script>
    // debut de code chat en js
function toggleChatbot() {
  const chatbotPopup = document.getElementById("chatbot-popup");
  chatbotPopup.style.display =
    chatbotPopup.style.display === "none" ? "block" : "none";
}




async function runGeminiQuery(m) {
      const apiKey = "AIzaSyCTmf2trLBuQqqLwMacvI3hJ0AHUj6zkdc";
      const url = `https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=${apiKey}`;

      const prompt = `Veuillez répondre à la question suivante en francais, à condition qu'elle relève du domaine des droits des femmes ou des lois relatives aux femmes en Tunisie uniquement. 
      vous pouvez aussi repondre au salutations et autre greetings. 
Si la question sort du cadre de ce sujet, veuillez répondre par la phrase : « Ceci sort du cadre de mon sujet. »

Question :  ${m}
      `;

      const requestBody = {
        contents: [
          {
            parts: [{ text: prompt }],
            role: "user"
          }
        ]
      };

      try {
        const res = await fetch(url, {
          method: "POST",
          headers: {
            "Content-Type": "application/json"
          },
          body: JSON.stringify(requestBody)
        });

        const data = await res.json();
        const output = data.candidates?.[0]?.content?.parts?.[0]?.text || "No response";
        appendMessage("bot",output);
    } catch (error) {
        console.error("Error:", error);
        appendMessage("bot",error);
          }
    }


    function sendMessage(){
     const m = document.getElementById('user-input').value;
     appendMessage("user",m);
     runGeminiQuery(m);
     document.getElementById('user-input').value = '';

    }

    function appendMessage(sender, message) {
  const chatBox = document.getElementById("chat-box");
  const messageElement = document.createElement("div");
  messageElement.classList.add(
    sender === "user" ? "user-message" : "bot-message"
  );
  messageElement.innerHTML = message;
  chatBox.appendChild(messageElement);
  chatBox.scrollTop = chatBox.scrollHeight;
}

// fin de code chat en js
</script>

</body>
</html>

<?php
include('../dbconfig/index.php'); // Assurez-vous que la connexion à la base de données est correcte.
include('../validateur.php');isAuthentiacted();

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION['id'])) {
    die('Vous devez être connecté pour ajouter un commentaire.');
}

// Récupérer l'ID du forum à partir de l'URL (GET)
if (isset($_GET['id_forum'])) {
    $id_forum = $_GET['id_forum'];
} else {
    die('ID du forum manquant.');
}

// Récupérer l'ID de l'utilisateur depuis la session
$id_personne = $_SESSION['id'];

// Ajout de commentaire
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['commentaire'])) {
    $commentaire = $_POST['commentaire'];

    // Vérification de l'existence du forum
    $stmt = $conn->prepare("SELECT id FROM forum WHERE id = ?");
    $stmt->bind_param('i', $id_forum);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $stmt = $conn->prepare("INSERT INTO commentaire (contenu, id_personne, id_forum) VALUES (?, ?, ?)");
        $stmt->bind_param('sii', $commentaire, $id_personne, $id_forum);
        
        if ($stmt->execute()) {
            header('Location: detail.php?id_forum=' . $id_forum);
            exit();
        } else {
            die('Erreur d\'exécution : ' . $stmt->error);
        }
    } else {
        die('L\'ID du forum est invalide.');
    }
}

// Récupérer les commentaires du forum
$stmt = $conn->prepare("SELECT commentaire.contenu, personne.nom, personne.role FROM commentaire 
                        JOIN personne ON commentaire.id_personne = personne.id 
                        WHERE commentaire.id_forum = ?");
$stmt->bind_param('i', $id_forum);
$stmt->execute();
$result = $stmt->get_result();
$comments = $result->fetch_all(MYSQLI_ASSOC);

// Récupérer les informations du forum et le nombre de likes
$stmt = $conn->prepare("SELECT forum.*, 
                               (SELECT COUNT(*) FROM aime WHERE aime.id_forum = forum.id) as likes 
                        FROM forum 
                        WHERE forum.id = ?");
$stmt->bind_param('i', $id_forum);
$stmt->execute();
$forum = $stmt->get_result()->fetch_assoc();




$stmt = $conn->prepare("SELECT * FROM personne WHERE id = ?");
$stmt->bind_param('s', $forum['id_personne']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_object();




?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forum de Discussion</title>
    <style>
        
        body {
    font-family: Arial, sans-serif;
    background-color: #F8F4ED;
    margin: 0;
    padding: 0;
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
    flex-wrap: wrap; /* Ajout de flex-wrap pour les petits écrans */
    margin: 20px;
}

.sidebar {
    width: 20%;
    background: #F3EEE5; /* Couleur de fond douce */
    padding: 20px;
    border-radius: 12px;
    margin-right: 20px;
    box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1); /* Ombre plus subtile pour un effet de profondeur */
    transition: box-shadow 0.3s ease, transform 0.3s ease; /* Transition douce pour les effets */
}

.sidebar:hover {
    transform: translateY(-5px); /* Légère élévation de la sidebar au survol */
    box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2); /* Ombre plus marquée au survol */
}

/* Titres des sections dans la sidebar */
.sidebar h3 {
    font-size: 1.3em;
    color: #333;
    margin-bottom: 15px;
    font-weight: bold;
    border-bottom: 2px solid #E7A63D;
    padding-bottom: 10px;
    letter-spacing: 0.5px; /* Ajout de l'espacement des lettres pour un effet plus aéré */
}

/* Listes dans la sidebar */
.sidebar ul {
    list-style-type: none;
    padding-left: 0;
    margin-top: 10px;
}

.sidebar ul li {
    padding: 12px 0;
    font-size: 1.1em;
    color: #555;
    border-bottom: 1px solid #ddd;
    transition: background-color 0.3s ease, padding-left 0.3s ease; /* Effet au survol */
}

.sidebar ul li:hover {
    background-color: #F8E2BE; /* Couleur de survol plus visible */
    padding-left: 15px; /* Décalage des éléments au survol */
    cursor: pointer;
}

/* Réactif pour les petits écrans */
@media (max-width: 768px) {
    .sidebar {
        width: 100%; /* Prend toute la largeur sur les petits écrans */
        margin-right: 0;
        margin-bottom: 20px;
    }

    .sidebar h3 {
        font-size: 1.1em; /* Réduction de la taille du texte sur petits écrans */
        
    }

    .sidebar ul li {
        font-size: 1em; /* Réduction de la taille du texte sur petits écrans */
        padding: 10px 0; /* Espacement réduit sur les petits écrans */
        
    }
}


.content {
    width: 75%;
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.question {
    background: #F8E2BE;
    padding: 15px;
    border-radius: 10px;
    margin-bottom: 15px;
    font-weight: bold;
    transform: translateY(-5px);
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
}

.comment-box {
    display: flex;
    align-items: center;
    background: #FFF;
    padding: 10px;
    border-radius: 10px;
    box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
    margin-top: 15px;
}

.comment-box input {
    flex: 1;
    border: none;
    padding: 10px;
    border-radius: 5px;
    font-size: 16px;
    margin-right: 10px;
    transition: box-shadow 0.3s;
}

.comment-box input:focus {
    outline: none;
    box-shadow: 0px 0px 8px rgba(231, 166, 61, 0.6);
}

.send-btn {
    background: #E7A63D;
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
    transition: background-color 0.3s;
    align:"right";
}

.send-btn:hover {
    background-color: #D28C2A; /* Changement de couleur au survol */
    
}

.btn-retour {
    color: #E7A63D;
    font-size: 25px;
    position: absolute;
    top: 20px;
    left: 20px;
    background-color: white;
    text-align: center;
    line-height: 40px;
    border-radius: 50%;
    text-decoration: none;
    transition: background-color 0.3s;
}

.btn-retour:hover {
    background-color: #F3EEE5;
}

.comment {
    margin-top: 10px;
    padding: 10px;
    background: #FFF;
    border-radius: 10px;
    box-shadow: 1px 1px 5px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s, background-color 0.3s;
}

.comment:hover {
    transform: translateY(-5px);
    background-color: #F8F4ED; /* Légère modification de couleur au survol */
}
.expert_comment{
    background: #EEE;

}

.comment strong {
    font-size: 1.1em;
    color: #E7A63D;
}

.comments-section p {
    font-style: italic;
    color: #888;
}

@media (max-width: 768px) {
    .sidebar {
        width: 100%;
        margin-right: 0;
    }

    .content {
        width: 100%;
    }
}

    
    </style>
</head>
<body>

<?php include('../nav/index.php'); ?>


<div class="container">
    <div class="sidebar">
        <h3>Utilisateur TOP 5:</h3>
        <ul>
            <li>1. Anonyme 1</li>
            <li>2. Anonyme 12</li>
            <li>3. Anonyme 5</li>
        </ul>

        <h3>Expert TOP 5:</h3>
        <ul>
            <li>1. Expert 10</li>
            <li>2. Expert 14</li>
        </ul>
    </div>

    <div class="content">
        <div class="question">
            <p>La Question de <?php echo htmlspecialchars($forum['anonyme'] ? 'Anonyme' : $user->nom); ?>:</p>
            <p><?php echo htmlspecialchars($forum['contenu']); ?></p>
        </div>

        <div class="comment-box">
            <form method="POST" action="">
                <input type="text" name="commentaire" placeholder="Écrivez votre commentaire..." required>
                <button class="send-btn" type="submit">Envoyer</button>
            </form>
        </div>

        <div class="comments-section">
    <?php if (isset($comments) && count($comments) > 0): ?>
        <?php foreach ($comments as $comment): ?>
            <div class="comment <?= ($comment['role'] == 'expert') ? 'expert_comment' : '' ?>">
                <strong><?php echo htmlspecialchars($comment['nom']); ?>:</strong>
                <p><?php echo htmlspecialchars($comment['contenu']); ?></p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Aucun commentaire pour ce forum.</p>
    <?php endif; ?>
</div>

    </div>
</div>

<script>
    // Script pour gérer les likes avec AJAX
    document.querySelectorAll('.like-btn').forEach(button => {
        button.addEventListener('click', function () {
            const forumId = this.dataset.id;
            const likeCountElement = this.querySelector('.like-count');

            fetch('like.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id_forum: forumId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    likeCountElement.textContent = data.likes;
                }
            });
        });
    });
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
</body>
</html>

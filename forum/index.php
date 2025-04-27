<?php
include('../dbconfig/index.php');
include('../validateur.php');
isAuthentiacted();

// Connexion à la base de données
$mysqli = new mysqli("localhost", "root", "", "easylegal");
if ($mysqli->connect_error) {
    die("Connexion échouée : " . $mysqli->connect_error);
}

// Récupérer les posts avec le nombre de likes
$sql = "
    SELECT forum.*, personne.nom, 
           (SELECT COUNT(*) FROM aime WHERE aime.id_forum = forum.id) AS likes
    FROM forum
    JOIN personne ON forum.id_personne = personne.id
    ORDER BY id DESC
";

$result = $mysqli->query($sql);
$forums = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $forums[] = $row;
    }
}

// Ajouter un post
if (isset($_POST['contenu'])) {
    $contenu = $mysqli->real_escape_string($_POST['contenu']);
    $anonyme = isset($_POST['anonyme']) ? 1 : 0;
    $id_personne = $_SESSION['id']; // Id de l'utilisateur connecté

    $stmt = $mysqli->prepare("INSERT INTO forum (contenu, anonyme, id_personne) VALUES (?, ?, ?)");
    $stmt->bind_param("sii", $contenu, $anonyme, $id_personne);
    $stmt->execute();
    $stmt->close();

    header("Location: index.php");
    exit;
}

// Gérer les likes (AJAX)
if (isset($_POST['id_forum']) && isset($_POST['like'])) {
  $id_forum = (int) $_POST['id_forum'];
  $id_personne = $_SESSION['id']; // Utilisateur connecté

  // Vérifier si l'utilisateur a déjà aimé ce post
  $check = $mysqli->prepare("SELECT id FROM aime WHERE id_forum = ? AND id_personne = ?");
  $check->bind_param("ii", $id_forum, $id_personne);
  $check->execute();
  $check_result = $check->get_result();

  if ($check_result->num_rows === 0) {
      // Ajouter un like
      $insert = $mysqli->prepare("INSERT INTO aime (id_forum, id_personne, liked_at) VALUES (?, ?, NOW())");
      $insert->bind_param("ii", $id_forum, $id_personne);
      $insert->execute();
      $insert->close();
  } else {
      // Retirer un like
      $delete = $mysqli->prepare("DELETE FROM aime WHERE id_forum = ? AND id_personne = ?");
      $delete->bind_param("ii", $id_forum, $id_personne);
      $delete->execute();
      $delete->close();
  }

  // Retourner le nouveau nombre de likes
  $likes = $mysqli->prepare("SELECT COUNT(*) FROM aime WHERE id_forum = ?");
  $likes->bind_param("i", $id_forum);
  $likes->execute();
  $likes_result = $likes->get_result();
  $likes_count = $likes_result->fetch_row()[0];

  echo json_encode(["success" => true, "likes" => $likes_count]);
  exit;
}


// Fermer la connexion à la base de données
$mysqli->close();
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #F8F4ED;
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
    </style>
</head>
<body>

<?php include('../nav/index.php'); ?>

<div class="container mt-4">
    <h1 class="mb-3">Forum </h1>
    <form method="POST" class="mb-3">
        <textarea name="contenu" class="form-control" placeholder="Écrivez votre message..." required></textarea>
        <div class="form-check mt-2">
            <input class="form-check-input" type="checkbox" name="anonyme" id="anonyme">
            <label class="form-check-label" for="anonyme">Poster en anonyme</label>
        </div>
        <button type="submit" class="btn btn-warning w-100 mt-2">Publier</button>
    </form>

    <?php foreach ($forums as $forum): ?>
        
        <div class="card mb-3 p-3">
            <div class="card-body">
                <h5 class="card-title"><?= $forum['anonyme'] ? 'Anonyme' : $forum['nom'] ?></h5>
                <a href=<?php echo"./detail.php?id_forum=".$forum['id'] ?>>

                <p class="card-text question"><?= htmlspecialchars($forum['contenu']) ?></p>    </a>

                <span class="like-btn" data-id="<?= $forum['id'] ?>">❤️ J'aime (<span class="like-count"><?= $forum['likes'] ?></span>)</span>
            </div>
        </div>
    <?php endforeach; ?>
    
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".like-btn").forEach(button => {
        button.addEventListener("click", function () {
            let id_forum = this.getAttribute("data-id");
            let likeCount = this.querySelector(".like-count");

            fetch("index.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: new URLSearchParams({ id_forum: id_forum, like: 1 })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    likeCount.textContent = data.likes;
                }
            })
            .catch(error => console.error("Erreur:", error));
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

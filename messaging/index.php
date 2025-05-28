<?php
session_start();
$conn = new mysqli("localhost", "root", "", "easylegal");

if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

if (!isset($_SESSION['id'])) {
    $_SESSION['id'] = 1; // For testing
}

if (!isset($_GET['id_messagerie'])) {
    $stmt = $conn->prepare("SELECT id FROM messagerie WHERE id_personne = ? OR participant_expert_id = ? LIMIT 1");
    $stmt->bind_param("ii", $_SESSION['id'], $_SESSION['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        header("Location: index.php?id_messagerie=" . $row['id']);
        exit;
    } else {
    }
}
$id_messagerie = (int)$_GET['id_messagerie'];
$user_id = $_SESSION['id']; // Important: was missing
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Simple Chat</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .msg{
            display:flex;
            flex-direction:column;
            padding: 10px;
            border:0.5px gray solid;
            border-radius: 5px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            max-width:100%;
            align-items:start;
        }
        .my{
            background-color:rgb(224, 204, 176);
            color: white;
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


<div class="content">
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Discussions</h2>

        <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search for names..">

        <ul id="myUL" style='dsiplay:none;'>
        <?php

$role = $_SESSION['role'];

if ($role == "expert") {
    $stmt = $conn->prepare("
        SELECT p.id, p.nom 
        FROM personne p 
        WHERE p.role = 'user' 
        AND p.id != ?
        AND p.id NOT IN (
            SELECT id_personne 
            FROM messagerie 
            WHERE participant_expert_id = ?
            UNION
            SELECT participant_expert_id 
            FROM messagerie 
            WHERE id_personne = ?
        )
    ");
    $stmt->bind_param("iii", $user_id, $user_id, $user_id);
} else {
    
    $stmt = $conn->prepare("
        SELECT p.id, p.nom 
        FROM personne p 
        WHERE p.role = 'expert' 
        AND p.id != ?
        AND p.id NOT IN (
            SELECT participant_expert_id 
            FROM messagerie 
            WHERE id_personne = ?
            UNION
            SELECT id_personne 
            FROM messagerie 
            WHERE participant_expert_id = ?
        )
    ");
    $stmt->bind_param("iii", $user_id, $user_id, $user_id);
}

$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $id_personne = ($role == "expert") ? $row['id'] : $user_id;
    $id_expert = ($role == "expert") ? $user_id : $row['id'];
    echo "<li><a href='create_messagerie.php?id_personne=$id_personne&id_expert=$id_expert'>{$row['nom']}</a></li>";
}
$stmt->close();
?>

        </ul>

        <div id="discussion-list">
            <?php
            if ($role == "expert") {
                $stmt = $conn->prepare("SELECT m.id, created_at, id_personne, p.id as pid, p.nom as nom 
                                        FROM messagerie m, personne p  
                                        WHERE participant_expert_id = ? AND p.id = id_personne");
            } else {
                $stmt = $conn->prepare("SELECT m.id, created_at, participant_expert_id, p.id as pid, p.nom as nom 
                                        FROM messagerie m, personne p  
                                        WHERE id_personne = ? AND p.id = participant_expert_id");
            }
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $activeClass = ($row['id'] == $id_messagerie) ? 'style="background-color: #d98e25;"' : '';
                echo "<div class='discussion' $activeClass onclick=\"window.location.href='index.php?id_messagerie={$row['id']}'\">{$row['nom']}</div>";
            }
            $stmt->close();
            ?>
        </div>
    </div>

    <script>
    function myFunction() {
        const input = document.getElementById("myInput");
        const ul = document.getElementById("myUL");

        input.addEventListener("keyup", () => {
            const filter = input.value.toUpperCase();
            const li = ul.getElementsByTagName("li");
            let hasVisibleItems = false;

            for (let i = 0; i < li.length; i++) {
            const a = li[i].getElementsByTagName("a")[0];
            if (a.innerHTML.toUpperCase().indexOf(filter) > -1) {
                li[i].style.display = "";
                hasVisibleItems = true;
            } else {
                li[i].style.display = "none";
            }
            }

            ul.style.display = filter && hasVisibleItems ? "block" : "none";
        });

        input.addEventListener("blur", () => {
            setTimeout(() => {
            ul.style.display = "none";
            }, 200);
        });
    }
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

    <!-- Chat Container -->
    <div class="chat-container">
        <div id="chat-box"></div>

        <div class="input-area">
            <input type="text" id="messageInput" placeholder="Type your message...">
            <button onclick="sendMessage()">Send Message</button>
        </div>

        <form class="upload-form">
            <input type="file" id="fileInput">
            <button type="button" onclick="sendFile()">Send File</button>
        </form>
    </div>
</div>

<script>
const idMessagerie = <?php echo $id_messagerie; ?>;
const idPersonne = <?php echo $_SESSION['id']; ?>;

function fetchMessages() {
    const xhr = new XMLHttpRequest();
    xhr.open('GET', `get_messages.php?id_messagerie=${idMessagerie}`, true);
    xhr.onload = function() {
        if (this.status === 200) {
            document.getElementById('chat-box').innerHTML = this.responseText;
        }
    };
    xhr.send();
}

setInterval(fetchMessages, 1000);

function sendMessage() {
    const message = document.getElementById('messageInput').value.trim();
    if (!message) return;

    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'send_message.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.send(`contenu=${encodeURIComponent(message)}&id_messagerie=${idMessagerie}&id_personne=${idPersonne}`);

    document.getElementById('messageInput').value = "";
}

function sendFile() {
    const fileInput = document.getElementById('fileInput');
    const file = fileInput.files[0];
    if (!file) {
        alert("Choose a file");
        return;
    }

    const reader = new FileReader();
    reader.onload = function() {
        const base64 = reader.result;
        console.log(base64); // For debugging

        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'send_file.php', true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.send(`contenu=${encodeURIComponent(base64)}&id_messagerie=${idMessagerie}&id_personne=${idPersonne}`);
    };
    reader.readAsDataURL(file);

    fileInput.value = "";
}
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
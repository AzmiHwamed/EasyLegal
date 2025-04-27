<?php
include('../validateur.php');
isAuthentiacted();


$conn = new mysqli("localhost", "root", "", "easylegal");

if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Vérifier l'authentification
if (!isset($_SESSION['id']) || !isset($_SESSION['role'])) {
    $role = $_GET['role'] ?? 'user';

    $stmt = $conn->prepare("SELECT id, role FROM personne WHERE role = ? LIMIT 1");
    $stmt->bind_param("s", $role);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        $nom = 'Utilisateur par défaut';
        $stmt = $conn->prepare("INSERT INTO personne (nom, role) VALUES (?, ?)");
        $stmt->bind_param("ss", $nom, $role);
        $stmt->execute();
        $row = ['id' => $conn->insert_id, 'role' => $role];
    }

    $_SESSION['id'] = $row['id'];
    $_SESSION['role'] = $row['role'];
    $_SESSION['id_personne'] = $row['id']; // Ajout important
    $stmt->close();
}

if (!isset($_GET['id_messagerie'])) {
    $stmt = $conn->prepare("SELECT id FROM messagerie WHERE id_personne = ? OR participant_expert_id = ? LIMIT 1");
    $stmt->bind_param("ii", $_SESSION['id_personne'], $_SESSION['id_personne']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        header("Location: index.php?id_messagerie=" . $row['id']);
        exit;
    } else {
        die("No messagerie found");
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Simple Chat</title>
    <link rel="stylesheet" href="style.css">
    <style>
    * {
    box-sizing: border-box;
}


body {
    margin: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f5f3ed;
    color: #333;
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
  border-raduis:25px;

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

.content {
    display: flex;
    height: 85vh;
    background-color: #f8f5eb;
    padding: 10px;
}

.sidebar {
    width: 300px;
    background-color: #ede0c4;
    padding: 20px;
    overflow-y: auto;
    border-right: 2px solid #ccc;
    border-radius: 12px;
    box-shadow: 2px 0 8px rgba(247, 220, 144, 0.74);
}

.sidebar h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #f4a836;
    font-size: 22px;
    font-weight: bold;
}

.discussion {
    padding: 12px;
    background-color: #dfd3b8;
    margin-bottom: 12px;
    cursor: pointer;
    transition: all 0.1s ease;
    border-radius: 10px;
}

.discussion:hover {
    background-color: #f4a836;
    color: white;
    transform: scale(1.02);
}

.discussion.active {
    background-color: #555;
    color: white;
}

.chat-container {
    flex: 1;
    display: flex;
    flex-direction: column;
    background-color: #fdfaf3;
    border-radius: 12px;
    overflow: hidden;
    margin-left: 15px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
}

#chat-box {
#chat-box1 {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    scroll-behavior: smooth;
}

.message {
    padding: 12px 15px;
    margin-bottom: 10px;
    border-radius: 12px;
    max-width: 65%;
    word-wrap: break-word;
    box-shadow: 2px 2px 6px rgba(0, 0, 0, 0.08);
    font-size: 14px;
}

.sent {
    align-self: flex-end;
    background-color: #d1f0d1;
}

.received {
    align-self: flex-start;
    background-color: #ffffff;
}

.input-area {
    display: flex;
    align-items: center;
    padding: 12px 15px;
    background-color: white;
    border-top: 2px solid #ccc;
}

.input-area input {
    flex: 1;
    padding: 10px 15px;
    border-radius: 25px;
    margin-right: 10px;
    border: 1px solid #ddd;
    font-size: 14px;
}

.input-area button {
    padding: 10px 20px;
    background-color: #f4a836;
    color: white;
    border: none;
    border-radius: 25px;
    cursor: pointer;
    font-weight: bold;
    transition: background-color 0.3s ease;
}

.input-area button:hover {
    background-color: #d98e25;
}

#myInput {
    width: 100%;
    font-size: 16px;
    padding: 12px 20px 12px 40px;
    border: 1px solid #ddd;
    margin-bottom: 12px;
    background-repeat: no-repeat;
    background-position: 10px 12px;
}

#myUL {
    list-style-type: none;
    padding: 0;
    display: none;
    margin: 0;
}

#myUL li a {
    border: 1px solid #ddd;
    margin-top: -1px;
    background-color: #f6f6f6;
    padding: 12px;
    text-decoration: none;
    font-size: 16px;
    color: #d98e25;
    display: block;
}

#myUL li a:hover:not(.header) {
    background-color: #eee;
}

form.upload-form {
    display: flex;
    flex-direction: column;
    gap: 12px;
    max-width: 300px;
    padding: 20px;
    background-color: #f9f9f9;
    border: 2px solid #ddd;
    border-radius: 12px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    margin: 20px 10px;
}

.upload-form input[type="file"] {
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 8px;
    background-color: #fff;
    font-size: 14px;
    cursor: pointer;
}


        </style>
</head>
<body>
<?php include('../nav/index.php'); ?>

<div class="content">
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Discussions</h2>

        <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search for names..">

        <ul id="myUL">
            <?php
            $role = $_SESSION['role'];
            if ($role == "expert") {
                $stmt = $conn->prepare("SELECT p.id, p.nom FROM personne p WHERE p.role = 'user' AND NOT EXISTS (
                    SELECT 1 FROM messagerie m WHERE m.id_personne = p.id AND m.participant_expert_id = ?
                )");
                $stmt->bind_param("i", $user_id);
            } else {
                $stmt = $conn->prepare("SELECT p.id, p.nom FROM personne p WHERE p.role = 'expert' AND NOT EXISTS (
                    SELECT 1 FROM messagerie m WHERE m.participant_expert_id = p.id AND m.id_personne = ?
                )");
                $stmt->bind_param("i", $user_id);
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

    <!-- Chat Container -->
    <div class="chat-container">
        <div id="chat-box"></div>

        <div class="input-area">
            <input type="text" id="messageInput" placeholder="Type your message...">
            <button onclick="sendMessage()">Send</button>
        </div>

        <form class="upload-form">
            <input type="file" id="fileInput">
            <button type="button" onclick="sendFile()">Send File</button>
        </form>
    </div>
</div>

<script>
const idMessagerie = <?php echo json_encode($id_messagerie); ?>;
const idPersonne = <?php echo json_encode($_SESSION['id']); ?>; // correction ici

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
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'send_file.php', true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.send(`contenu=${encodeURIComponent(base64)}&id_messagerie=${idMessagerie}&id_personne=${idPersonne}`);
    };
    reader.readAsDataURL(file);

    fileInput.value = "";
}
</script>


</body>
</html>

<?php
session_start();
$conn = new mysqli("localhost", "root", "", "easylegal");

if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

if (!isset($_SESSION['id_personne'])) {
    $_SESSION['id_personne'] = 1; // For testing
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
$id_messagerie = (int)$_GET['id_messagerie'];
$user_id = $_SESSION['id_personne']; // Important: was missing
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
            max-width:fit-content';
        }
        .my{
            background-color: #d98e25;
            color: white;
            align-items: flex-end;
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
const idMessagerie = <?php echo $id_messagerie; ?>;
const idPersonne = <?php echo $_SESSION['id_personne']; ?>;

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

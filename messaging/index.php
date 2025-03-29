<?php
include('../validateur.php');isAuthentiacted();

$conn = new mysqli("localhost", "root", "", "easylegal");

if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Vérification si l'utilisateur est authentifié (utilisation de $_SESSION)
if (!isset($_SESSION['id'])) {
    // On suppose que l'utilisateur existe déjà dans la table `personne`
    $result = $conn->query("SELECT id FROM personne LIMIT 1");

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['id'] = $row['id'];
    } else {
        $conn->query("INSERT INTO personne (nom, role) VALUES ('Utilisateur par défaut', 'user')");
        $_SESSION['id'] = $conn->insert_id;
    }
}

$user_id = $_SESSION['id'];

$result = $conn->query("SELECT * FROM personne WHERE id = $user_id LIMIT 1");
if ($result->num_rows == 0) {
    die("Utilisateur non trouvé, l'opération a échoué.");
}

// Création ou récupération de l'identifiant de messagerie
if (isset($_GET['id_messagerie'])) {
    $id_messagerie = (int)$_GET['id_messagerie'];
} else {
    // Utilisation d'une requête préparée pour l'insertion sécurisée
    $stmt = $conn->prepare("INSERT INTO messagerie (titre, id_personne , nom) VALUES (?, ? , ?)");
    $stmt->bind_param("sis", $titre, $id_personne,$titre);
    
    $titre = 'Nouvelle discussion';
    $id_personne = $user_id;
    
    if ($stmt->execute()) {
        $id_messagerie = $stmt->insert_id;
        header("Location: index.php?id_messagerie=$id_messagerie");
        exit();
    } else {
        die("Erreur d'insertion dans messagerie.");
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messagerie Dynamique</title>
    <style>
        body {
            display: flex;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .sidebar {
            width: 300px;
            background-color: #333;
            color: white;
            padding: 20px;
            overflow-y: auto;
        }
        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .discussion {
            padding: 10px;
            border-radius: 5px;
            background-color: #444;
            margin-bottom: 10px;
            cursor: pointer;
        }
        .discussion:hover {
            background-color: #555;
        }
        .chat-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            background-color: #f0f0f0;
        }
        #chat-box {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }
        .message {
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 10px;
            max-width: 60%;
            word-wrap: break-word;
        }
        .sent {
            align-self: flex-end;
            background-color: #d1f0d1;
        }
        .received {
            align-self: flex-start;
            background-color: #f0f0f0;
        }
        .input-area {
            display: flex;
            padding: 10px;
            background-color: white;
        }
        .input-area input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .input-area button {
            padding: 10px 20px;
            margin-left: 10px;
            border: none;
            background-color: #4CAF50;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }
        .input-area button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Discussions</h2>
        <button onclick="window.location.href='index.php'">+ Nouvelle Discussion</button>
        <div id="discussion-list">
            <?php
            $result = $conn->query("SELECT * FROM messagerie WHERE id_personne = $user_id");

            while ($row = $result->fetch_assoc()) {
                $activeClass = ($row['id'] == $id_messagerie) ? 'style="background-color: #555;"' : '';
                echo "<div class='discussion' $activeClass onclick=\"window.location.href='index.php?id_messagerie={$row['id']}'\">Discussion {$row['id']}</div>";
            }
            ?>
        </div>
    </div>

    <div class="chat-container">
        <div id="chat-box"></div>
        <div class="input-area">
            <input type="text" id="message" placeholder="Aa...">
            <button id="sendBtn">Envoyer</button>
        </div>
    </div>

    <script>
        const idMessagerie = <?php echo $id_messagerie; ?>;

        document.getElementById("sendBtn").addEventListener("click", sendMessage);

        function sendMessage() {
            const message = document.getElementById("message").value.trim();
            if (message === "") return alert("Le message ne peut pas être vide !");

            const xhr = new XMLHttpRequest();
            xhr.open("POST", "send_message.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.send(`message=${encodeURIComponent(message)}&id_messagerie=${idMessagerie}`);

            document.getElementById("message").value = "";
        }

        function fetchMessages() {
            const xhr = new XMLHttpRequest();
            xhr.open("GET", "get_messages.php?id_messagerie=" + idMessagerie, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    document.getElementById("chat-box").innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }

        setInterval(fetchMessages, 500);
    </script>
</body>
</html>

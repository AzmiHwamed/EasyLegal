<?php
include('../validateur.php');
isAuthentiacted();

$conn = new mysqli("localhost", "root", "", "easylegal");

if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Vérification si l'utilisateur est authentifié
if (!isset($_SESSION['id']) || !isset($_SESSION['role'])) {

    // Identification de l'utilisateur (User ou Expert)
    $role = isset($_GET['role']) ? $_GET['role'] : 'user';  // Par défaut, c'est un user

    $stmt = $conn->prepare("SELECT id, role FROM personne WHERE role = ? LIMIT 1");
    $stmt->bind_param("s", $role);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['id'] = $row['id'];
        $_SESSION['role'] = $row['role'];
    } else {
        $stmt = $conn->prepare("INSERT INTO personne (nom, role) VALUES (?, ?)");
        $nom = 'Utilisateur par défaut';
        $stmt->bind_param("ss", $nom, $role);
        $stmt->execute();
        $_SESSION['id'] = $conn->insert_id;
        $_SESSION['role'] = $role;
    }

    $stmt->close();
}

$user_id = $_SESSION['id'];
$role = $_SESSION['role'];

// Création ou récupération de l'identifiant de messagerie
if (isset($_GET['id_messagerie'])) {
    $id_messagerie = (int)$_GET['id_messagerie'];
} else {
    $stmt = $conn->prepare("INSERT INTO messagerie (id_personne, nom) VALUES (?, ?)");
    $titre = 'Discussion par défaut';
    $stmt->bind_param("is", $user_id, $titre);

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
        nav {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            height: 8vh;
            padding: 0 2%;
            background-color: #F3EEE5;
            box-shadow: 4px 10px 10px rgba(0, 0, 0, 0.2);
            position: fixed;
            top: 0;
            z-index: 10;
        }

        nav a img {
            width: 4vw;
            max-height: 100%;
            min-height: 100%;
        }

        nav span {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            width: 30%;
        }

        nav span a {
            text-decoration: none;
            color: #000;
            font-weight: bolder;
            transition: color 0.3s;
        }

        nav span a:hover {
            color: #f4a836;
        }

        body {
            display: flex;
            height: 90vh;
            background-color: #f8f5eb;
            margin: 0;
            font-family: Arial, sans-serif;
            padding-top: 8vh;
        }

        .sidebar {
            width: 300px;
            background-color: #ede0c4;
            padding: 20px;
            overflow-y: auto;
            border-right: 2px solid #ccc;
            border-radius: 10px;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #4a4a4a;
        }

        .discussion {
            padding: 10px;
            background-color: #dfd3b8;
            margin-bottom: 10px;
            cursor: pointer;
            transition: background-color 0.3s;
            border-radius: 10px;
        }

        .discussion:hover {
            background-color: #f4a836;
            color: white;
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
            border-radius: 10px;
            overflow: hidden;
            margin-left: 10px;
        }

        #chat-box {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .message {
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 10px;
            max-width: 60%;
            word-wrap: break-word;
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
        }

        .sent {
            align-self: flex-end;
            background-color: #d1f0d1;
        }

        .received {
            align-self: flex-start;
            background-color: white;
        }

        .input-area {
            display: flex;
            align-items: center;
            padding: 10px;
            background-color: white;
            border-top: 2px solid #ccc;
        }

        .input-area input {
            flex: 1;
            padding: 10px;
            border-radius: 20px;
            margin-right: 10px;
            border: 1px solid #ddd;
        }

        .input-area button {
            padding: 10px 20px;
            background-color: #f4a836;
            color: white;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .input-area button:hover {
            background-color: #d98e25;
        }

    </style>
</head>
<body>
<nav>
    <a href="#">
        <img src="./assets/logo.png" alt="Icône de la justice" class="hero-image">
    </a>
    <span>
        <a href="../search/index.php">Rechercher</a>
        <a href="../forum/index.php">Forum</a>
        <a href="../messaging/index.php">Discuter</a>
    </span>
    <a><img src="./assets/Male User.png" alt="Account" style="width: 3vw !important;"></a>
</nav>

<div class="sidebar">
    <h2>Discussions</h2>
    <button onclick="window.location.href='index.php'">+ Nouvelle Discussion</button>
    <div id="discussion-list">
        <?php
        $stmt = $conn->prepare("SELECT * FROM messagerie WHERE id_personne = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $activeClass = ($row['id'] == $id_messagerie) ? 'style="background-color: #555;"' : '';
            echo "<div class='discussion' $activeClass onclick=\"window.location.href='index.php?id_messagerie={$row['id']}'\">Discussion {$row['id']}</div>";
        }

        $stmt->close();
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

    setInterval(fetchMessages, 1000);
</script>
</body>
</html>

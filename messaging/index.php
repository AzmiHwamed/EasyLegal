<?php
include('../validateur.php');
isAuthentiacted();

$conn = new mysqli("localhost", "root", "", "easylegal");

if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Vérification si l'utilisateur est authentifié
if (!isset($_SESSION['id']) || !isset($_SESSION['role'])) {
    $role = isset($_GET['role']) ? $_GET['role'] : 'user';  
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


if (isset($_GET['id_messagerie'])) {
    $id_messagerie = (int)$_GET['id_messagerie'];
} else {
    if($role=="expert"){
        $stmt = $conn->prepare("SELECT id FROM messagerie WHERE participant_expert_id = ? LIMIT 1");}
    else{
        $stmt = $conn->prepare("SELECT id FROM messagerie WHERE id_personne = ? LIMIT 1");}
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $id_messagerie = $row['id'];
            header("Location: index.php?id_messagerie=$id_messagerie");
    }
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
            width: 97%;
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
 
        .nav-center a,
  .nav-right a {
    text-decoration: none;
    color: #000;
    font-weight: bold;
    margin-left: 1vw;
  }


        .content {
            display: flex;
            height: 85vh;
            background-color: #f8f5eb;
            margin: 0;
            font-family: Arial, sans-serif;
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
        #myInput {
  background-image: url('/css/searchicon.png'); /* Add a search icon to input */
  background-position: 10px 12px; /* Position the search icon */
  background-repeat: no-repeat; /* Do not repeat the icon image */
  width: 100%; /* Full-width */
  font-size: 16px; /* Increase font-size */
  padding: 12px 20px 12px 40px; /* Add some padding */
  border: 1px solid #ddd; /* Add a grey border */
  margin-bottom: 12px; /* Add some space below the input */
}

#myUL {
  /* Remove default list styling */
  list-style-type: none;
  padding: 0;
  display:none;
  margin: 0;
}

#myUL li a {
  border: 1px solid #ddd; /* Add a border to all links */
  margin-top: -1px; /* Prevent double borders */
  background-color: #f6f6f6; /* Grey background color */
  padding: 12px; /* Add some padding */
  text-decoration: none; /* Remove default text underline */
  font-size: 18px; /* Increase the font-size */
  color: black; /* Add a black text color */
  display: block; /* Make it into a block element to fill the whole list */
}

#myUL li a:hover:not(.header) {
  background-color: #eee; /* Add a hover effect to all links, except for headers */
}

    </style>
</head>
<body>
<?php include('../nav/index.php'); ?>
<div class="content">

<!-- <nav>
<div class="nav-left">
    <a href="#">
      <img src="../assets/logo.png" alt="Logo">
    </a>
  </div>

  <div class="nav-center">
    <a href="../search/index.php">Rechercher</a>
    <a href="../forum/index.php">Forum</a>
    <a href="../messaging/index.php">Discuter</a>
  </div>

  <div class="nav-right">
    <a href="../Profile/index.php">
      <img src="../assets/Male User.png" alt="Profil">
    </a>
    <?php if (isset($_SESSION['id'])): ?>
      <a href="../auth/Logout.php">Déconnexion</a>
    <?php else: ?>
      <a href="../auth/login.php">Connexion</a>
    <?php endif; ?>
  </div>
</nav> -->

<div class="sidebar">
    <h2>Discussions</h2>
    <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search for names..">

    <ul id="myUL">
        <?php
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
        if($role=="expert"){
            $stmt = $conn->prepare("SELECT m.id,created_at,id_personne,p.id as pid, p.nom as nom FROM messagerie m , personne p  WHERE participant_expert_id = ? AND p.id = id_personne");
        }
        else{
            $stmt = $conn->prepare("SELECT  m.id,created_at,participant_expert_id,p.id as pid, p.nom as nom FROM messagerie m , personne p  WHERE id_personne = ? AND p.id = participant_expert_id");
        }
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();


            while ($row = $result->fetch_assoc()) {

            $activeClass = ($row['id'] == $id_messagerie) ? 'style="background-color: #555;"' : '';
            echo "<div class='discussion' $activeClass onclick=\"window.location.href='index.php?id_messagerie={$row['id']}'\">{$row['nom']}</div>";
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
<script>
function myFunction() {
  // Declare variables
  var input, filter, ul, li, a, i, txtValue;
  input = document.getElementById('myInput');
  filter = input.value.toUpperCase();
  ul = document.getElementById("myUL");
  li = ul.getElementsByTagName('li');
  
  for (i = 0; i < li.length; i++) {
    a = li[i].getElementsByTagName("a")[0];
    txtValue = a.textContent || a.innerText;
    if (txtValue.toUpperCase().indexOf(filter) > -1) {
      li[i].style.display = "";
    } else {
      li[i].style.display = "none";
    }
  }
  
}



var myInput = document.getElementById("myInput");
myInput.addEventListener("focus", function() {
    document.getElementById("myUL").style.display = "block";
});

myInput.addEventListener("blur", function() {
    setTimeout(function() {
        document.getElementById("myUL").style.display = "none";
    }, 200); // Delay to allow click events on list items
});

</script>
</body>
</html>

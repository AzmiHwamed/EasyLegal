<?php session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f4ef;
            text-align: center;
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

    

        header {
    background-color: #f4dfb6;
    padding: 30px 20px;
    text-align: center;
    border-bottom: 3px solid #d38d2c;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    border-radius: 0 0 15px 15px;
}

h1 {
    color: #d38d2c;
    font-size: 2em;
    font-weight: bold;
    margin: 0;
    text-transform: uppercase;
    letter-spacing: 2px;
}


        p {
            font-size: 16px;
            color: #444;
        }

        form {
            margin: 20px;
        }

        input, select {
            padding: 10px;
            width: 300px;
            border: 1px solid #ccc;
            border-radius: 5px;
            display: block;
            margin: 10px auto;
        }

        .filter-container {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 10px;
        }

        .filter-container select {
            width: 150px;
        }

        button {
    padding: 12px 20px;
    background-color: #d38d2c;
    color: white;
    border: none;
    cursor: pointer;
    border-radius: 8px;
    margin-top: 10px;
    font-size: 16px;
    font-weight: bold;
    text-transform: uppercase;
    transition: all 0.3s ease-in-out;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
}

/* Effet au survol */
button:hover {
    background-color: #b37424;
    transform: translateY(-2px);
    box-shadow: 0px 6px 10px rgba(0, 0, 0, 0.15);
}

/* Effet au clic */
button:active {
    transform: translateY(1px);
    box-shadow: 0px 3px 5px rgba(0, 0, 0, 0.2);
}

        .header-box {
    background-color: #f8f4ef; 
    border: 2px solid #d38d2c;
    border-radius: 5px; 
    padding: 15px 20px; 
    margin: 10px auto; 
    width: 80%; 
    text-align: left; 
}

h1 {
    font-size: 1.5em; 
    font-weight: bold; 
    color: #d38d2c; 
    margin: 0; 
    padding-bottom: 5px; 
    border-bottom: 2px solid #d38d2c; 
    display: inline-block; 
}

h2 {
    font-size: 1em; 
    color: #333; 
    margin-top: 10px; 
    font-weight: normal; 
}

#search {
    width: 60%; 
    height: 30px; 
    font-size: 1.2em;
    padding: 10px;
    border-radius: 10px;
    border: 1px solid #ccc;
}

form {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-top: 20px;
}
h3{
    color:#d38d2c;
}
.result {
    width: 80%;
    margin: 20px auto;
    padding: 15px;
    background-color: #f8f4ef;
    border-radius: 8px;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
}

.result h2 {
    color: #d38d2c;
    text-align: center;
}

.result div {
    margin-bottom: 10px;
}

.collapsible {
    background-color: #d38d2c;
    color: white;
    cursor: pointer;
    padding: 10px;
    width: 100%;
    border: none;
    text-align: left;
    font-size: 16px;
    font-weight: bold;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}

.collapsible:hover {
    background-color: #b37424;
}

.content {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease-out;
    background-color: #fff;
    padding: 0 15px;
    border-left: 3px solid #d38d2c;
    border-radius: 5px;
    margin-top: 5px;
}

.content p {
    padding: 10px 0;
    color: #444;
    font-size: 14px;
    line-height: 1.5;
}

    </style>
</head>
<body>
<?php include('../nav/index.php'); ?>

    <div class="header-box">
        <h1>Bienvenue dans EasyLegal</h1>
        <h2>Cette bibliothèque numérique a pour objectif d'augmenter la visibilité et l'accessibilité au texte juridique.</h2>
    </div>

    <main>
        <form action="" method="GET">
            <h3>Rechercher :</h3>
            <input type="text" name="search" id="search" placeholder="Rechercher un texte juridique...">
            
            <h3>Trier par :</h3>
            <div class="filter-container">
                <select name="type">
                    <option value="" disabled selected>Type</option>
                    <option value="">Tous</option>
                    <option value="avis">Avis</option>
                    <option value="lois">Lois</option>
                    <option value="arretes">Arrêtés</option>
                    <option value="decret-loi">Decret-Loi</option>

                </select>
                
                <input type="date" name="date">

                <select name="annee">
                    <option value="">Année</option>
                    <?php
                        for ($i = 2024; $i >= 1956; $i--) {
                            echo "<option value=\"$i\">$i</option>";
                        }
                    ?>
                </select>
            </div>

            <button type="submit">🔍 Rechercher</button>
        </form>

        <?php
        include '../dbconfig/index.php';

        if ($conn) {
            $search = isset($_GET['search']) ? trim($_GET['search']) : '';
            $type = isset($_GET['type']) ? trim($_GET['type']) : '';
            $date = isset($_GET['date']) ? trim($_GET['date']) : '';
            $annee = isset($_GET['annee']) ? trim($_GET['annee']) : '';
            $numero = isset($_GET['numero']) ? trim($_GET['numero']) : '';
            
            // Vérifier si une recherche est effectuée
            if (!empty($search) || !empty($type) || !empty($date) || !empty($annee) || !empty($numero)) {
                $query = "SELECT id, Date, Titre, Type, Theme, Contenu FROM textjuridique WHERE 1=1";

                if (!empty($search)) {
                    $query .= " AND (Contenu LIKE ? OR Titre LIKE ?)";
                    $search = "%$search%";
                }
                if (!empty($type)) {
                    $query .= " AND Type = ?";
                }
                if (!empty($date)) {
                    $query .= " AND Date = ?";
                }
                if (!empty($annee)) {
                    $query .= " AND YEAR(Date) = ?";
                }


                $stmt = $conn->prepare($query);

                // Associer les paramètres dynamiquement
                $params = [];
                $types = '';
                if (!empty($search)) {
                    $types .= 'ss';
                    $params[] = $search;
                    $params[] = $search;
                }
                if (!empty($type)) {
                    $types .= 's';
                    $params[] = $type;
                }
                if (!empty($date)) {
                    $types .= 's';
                    $params[] = $date;
                }
                if (!empty($annee)) {
                    $types .= 's';
                    $params[] = $annee;
                }


                if (!empty($params)) {
                    $stmt->bind_param($types, ...$params);
                }

                $stmt->execute();
                $result = $stmt->get_result();

                echo "<h2>Résultats de recherche :</h2>";
                echo "<div class='result'>";
                while ($row = $result->fetch_assoc()) {
                    echo "<div>
                            <button class='collapsible'>
                                <span>" . htmlspecialchars($row['Titre']) . "</span>
                            </button>
                            <div class='content'>
                                <p>" . htmlspecialchars($row['Contenu']) . "</p>
                            </div>
                          </div>";
                }
                echo "</div>";
            }
        }
        ?>

    </main>

    <script>
        document.querySelectorAll(".collapsible").forEach(button => {
            button.addEventListener("click", function () {
                this.classList.toggle("active");
                var content = this.nextElementSibling;
                content.style.maxHeight = content.style.maxHeight ? null : content.scrollHeight + "px";
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
<!-- debut de code chat html -->
<button id="chatbot-toggle-btn" onclick="toggleChatbot()"><img src="https://www2.stardust-testing.com/hs-fs/hubfs/banner-robot-service.png?width=1463&height=731&name=banner-robot-service.png"  style="border-radius:25px" width="50" height="50" alt="buttonpng" /></button>
<div class="chatbot-popup" id="chatbot-popup" style="display:none;">
  <div class="chat-header">
    <span>Chatbot  <a href="#" target="_blank"> EasyLegal</a></span>
    <button id="close-btn" onclick="toggleChatbot()">&times;</button>
  </div>
  <div class="chat-box" id="chat-box"></div>
  <div class="chat-input">
    <input type="text" id="user-input" placeholder="Aaa...">
    <button id="send-btn" onclick="sendMessage()">Envoyer</button>
  </div>
  <div class="copyright">
  </div>
</div>
<!-- fin de code chat html -->
 <script>
</body>
</html>

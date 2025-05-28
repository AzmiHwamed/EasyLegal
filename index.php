<?php
session_start();
?>



<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Easy Legal</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            text-align: center;
            background: #f8f4ef;
        }


        h1 {
            margin-top: 20px;
            font-size: 24px;
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
  background-color:#e8a043;
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








        

    .features {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 20px;
    margin: 40px 0;
}

.features a {
    text-decoration: none;
    color: #333;
    background-color: #f0f0f0;
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    transition: background-color 0.3s, transform 0.2s;
}

.features a:hover {
    background-color: #dcdcdc;
    transform: translateY(-2px);
    color: #000;
}


        .info {
            padding: 20px;
        }

        
        

        .info-box img {
            width: 80px;
            display: block;
            margin: 10px auto;
        }

        .highlight {
            color: #e8a043;
            text-align :center ;
        }

        .explore {
            margin: 20px 0;
        }

        .number {
            font-weight: bold;
            color: #e8a043;
        }

        .explore-btn {
    display: inline-block;
    padding: 10px 20px;
    background-color: #e8a043;
    color: white;
    text-decoration: none;
    border-radius: 5px;
}

.explore-btn:hover {
    background-color:rgb(239, 197, 105);/
}


        footer {
            background: #f0e6d6;
            padding: 10px;
            margin-top: 20px;
        }
        p{
            text-align:center ;
            size: 20px ;
        }
    </style>
</head>

<body>
    <!-- nav bar -->
     <?php include('./nav/index.php') ?>
    <!-- end of nav bar -->

    <!-- hero section -->
    <section class="Hero">
    <h1>Votre compagnon légal</h1>
    <img src="./assets/logo.png" alt="Icône de la justice" class="hero-image">
    <div class="features">
        <span>
        <div class="features">
    <a href="./search/index.php">Rechercher</a>
    <a href="./forum/index.php">Forum</a>
    <a href="./messaging/index.php">Discuter</a>
    </div>
     </span>
     </div>
    </section>

    <!-- end of hero section  -->
    <!-- information boxes  -->
    <section class="info">
        <!-- info box  -->
        <div >
            <h2 class="highlight">Chercher les lois qui vous concernent</h2>
            <p>Cherchez parmi des centaines des lois par catégorie, mots clés, et découvrez celles qui correspondent à
                vos besoins.</p>
            <img src="./assets/balance.png" alt="Balance de justice">
        </div>
        <!-- end of info box  -->


        <!-- info box  -->
        <div >
            <h2 class="highlight">Discuter avec des experts</h2>
            <p>Trouvez le conseil juridique parfait fourni par un ensemble d’experts.</p>
            <img src="./assets/avocat.png" alt="Avocat donnant des conseils">
        </div>
        <!-- end of info box  -->
    </section>
    <!-- end of information boxes -->

    <section class="explore">
    <h2>Plus que <span class="number">100</span> lois à explorer</h2>
    <a href="./search/index.php" class="explore-btn">Explorer maintenant</a>
</section>


    <footer>
        <p>Easy legal &copy; 2025 | All rights reserved</p>
    </footer>
    
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
    <input type="text" id="user-input" placeholder="Aaa...">
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
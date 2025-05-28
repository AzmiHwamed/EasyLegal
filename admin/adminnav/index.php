<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "easylegal";

// Connexion
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

$nom_utilisateur = isset($_SESSION['nom']) ? $_SESSION['nom'] : "Admin";

// Nombre d'utilisateurs
$sql_user = "SELECT COUNT(*) AS total FROM personne WHERE role = 'user'";
$nb_users = ($result_user = $conn->query($sql_user)) && ($row_user = $result_user->fetch_assoc()) ? $row_user['total'] : 0;

// Nombre d'experts
$sql_expert = "SELECT COUNT(*) AS total FROM refrenceexpert";
$nb_experts = ($result_expert = $conn->query($sql_expert)) && ($row_expert = $result_expert->fetch_assoc()) ? $row_expert['total'] : 0;

// Initialisation tableau des mois
$evolutionData = [];
for ($m = 1; $m <= 12; $m++) {
    $monthName = DateTime::createFromFormat('!m', $m)->format('F');
    $evolutionData[$monthName] = ['user' => 0, 'expert' => 0];
}

// Données mensuelles - utilisateurs
$sql_users_monthly = "SELECT MONTH(date_inscription) AS month, COUNT(*) AS user
                      FROM personne
                      WHERE role = 'user' AND YEAR(date_inscription) = YEAR(CURRENT_DATE)
                      GROUP BY MONTH(date_inscription)";
if ($result_users_monthly = $conn->query($sql_users_monthly)) {
    while ($row = $result_users_monthly->fetch_assoc()) {
        $monthName = DateTime::createFromFormat('!m', $row['month'])->format('F');
        $evolutionData[$monthName]['user'] = (int)$row['user'];
    }
}

// Données mensuelles - experts
$sql_experts_monthly = "SELECT MONTH(date_inscription) AS month, COUNT(*) AS expert
                        FROM refrenceexpert
                        WHERE YEAR(date_inscription) = YEAR(CURRENT_DATE)
                        GROUP BY MONTH(date_inscription)";
if ($result_experts_monthly = $conn->query($sql_experts_monthly)) {
    while ($row = $result_experts_monthly->fetch_assoc()) {
        $monthName = DateTime::createFromFormat('!m', $row['month'])->format('F');
        $evolutionData[$monthName]['expert'] = (int)$row['expert'];
    }
}

// Données graphique évolution
$labels = json_encode(array_keys($evolutionData));
$userData = json_encode(array_column($evolutionData, 'user'));
$expertData = json_encode(array_column($evolutionData, 'expert'));

// Répartition des textes juridiques
$typeLabels = [];
$typeData = [];

$sql_types = "SELECT type, COUNT(*) AS total FROM textjuridique GROUP BY type";
if ($result_types = $conn->query($sql_types)) {
    while ($row = $result_types->fetch_assoc()) {
        $typeLabels[] = $row['type'];
        $typeData[] = (int)$row['total'];
    }
}

$typeLabelsJson = json_encode($typeLabels);
$typeDataJson = json_encode($typeData);

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord Admin</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            display: flex;
            min-height: 100vh;
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f6f9;
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

        .sidebar {
            width: 260px;
            background: linear-gradient(135deg, #e89d3f, #e8a043);
            color: white;
            padding: 40px 25px;
            height: 100vh;
            position: fixed;
            overflow-y: auto;
            transition: all 0.3s ease;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar h2 {
            margin-bottom: 35px;
            font-size: 24px;
            text-align: center;
            font-weight: 600;
        }

        .sidebar nav ul {
            list-style: none;
        }

        .sidebar nav ul li {
            margin: 15px 0;
        }

        .sidebar nav ul li a {
            color: white;
            text-decoration: none;
            padding: 12px 18px;
            display: block;
            font-size: 16px;
            border-radius: 10px;
            transition: background 0.3s ease, transform 0.2s ease;
        }

        .sidebar nav ul li a:hover {
            background-color: rgba(255, 255, 255, 0.2);
            transform: translateX(5px);
        }

        .main-content {
            margin-left: 260px;
            padding: 40px;
            width: calc(100% - 260px);
            transition: all 0.3s ease;
        }

        .main-content h1 {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 12px;
        }

        .main-content p {
            font-size: 16px;
            color: #666;
            margin-bottom: 25px;
        }

        .stats-box {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            margin-top: 30px;
        }

        .stat-card {
    background: #ffffff;
    padding: 25px;
    border-radius: 16px;
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08);
    width: 220px;
    min-height: 130px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}


        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .stat-card h3 {
            font-size: 18px;
            color: #e89d3f;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .stat-card p {
            font-size: 28px;
            font-weight: bold;
            color: #333;
        }

        .chart-container {
            margin-top: 50px;
            background: #fff;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.07);
        }

        .chart-container h2 {
            font-size: 20px;
            margin-bottom: 20px;
            color: #222;
        }

        @media screen and (max-width: 768px) {
            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
                padding: 20px;
            }

            .main-content {
                margin-left: 0;
                width: 100%;
                padding: 20px;
            }

            .stats-box {
                justify-content: center;
            }
        }
        @media screen and (max-width: 768px) {
    .stats-box {
        justify-content: center;
        flex-direction: column;
        align-items: center;
    }

    .stat-card {
        width: 80%;
        max-width: 300px;
    }
}

    </style>
</head>
<body>

<aside class="sidebar">
    <h2>Bienvenue, <?= htmlspecialchars($nom_utilisateur); ?> 👋</h2>
    <nav>
        <ul>
            <li><a href="../user/index.php">Gérer les utilisateurs</a></li>
            <li><a href="../forum/index.php">Gérer le forum</a></li>
            <li><a href="../text/index.php">Textes juridiques</a></li>
            <li><a href="../expert/index.php">Gérer les experts</a></li>
            <li><a href="../../index.php">Page d'accueil</a></li>
        </ul>
    </nav>
</aside>

<main class="main-content">
    <h1>Tableau de bord</h1>
    <p>Bienvenue dans votre espace administrateur.</p>
    <div class="stats-box">
    <div class="stat-card">
        <h3>Utilisateurs</h3>
        <p><?= $nb_users ?></p>
    </div>
    <div class="stat-card">
        <h3>Experts</h3>
        <p><?= $nb_experts ?></p>
    </div>
</div>
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



    <!-- Courbe -->
    <div class="chart-container">
        <h2>Évolution des inscriptions</h2>
        <canvas id="evolutionChart"></canvas>
    </div>
    <div class="chart-container" style="margin-top: 40px;">
    <h2>Répartition des textes juridiques</h2>
    <canvas id="typeChart"></canvas>
</div>

</main>

<script>
    // Évolution des inscriptions
    const ctx = document.getElementById('evolutionChart').getContext('2d');
    const evolutionChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= $labels ?>,
            datasets: [
                {
                    label: 'Utilisateurs',
                    data: <?= $userData ?>,
                    backgroundColor: '#4e73df'
                },
                {
                    label: 'Experts',
                    data: <?= $expertData ?>,
                    backgroundColor: '#e89d3f'
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });

    // Répartition des textes juridiques
    const ctx2 = document.getElementById('typeChart').getContext('2d');
    const typeChart = new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: <?= $typeLabelsJson ?>,
            datasets: [{
                data: <?= $typeDataJson ?>,
                backgroundColor: [
                    '#4e73df', // lois
                    '#1cc88a', // décrets
                    '#f6c23e', // décret-lois
                    '#e74a3b'  // avis
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `${context.label}: ${context.parsed} textes`;
                        }
                    }
                }
            }
        }
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


</body>
</html>

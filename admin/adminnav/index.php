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
</script>


</body>
</html>

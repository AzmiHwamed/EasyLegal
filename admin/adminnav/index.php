<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "easylegal";

// Connexion
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$nom_utilisateur = isset($_SESSION['nom']) ? $_SESSION['nom'] : "Admin";

// Statistiques globales
$sql = "SELECT role, COUNT(*) AS total FROM personne GROUP BY role";
$result = $conn->query($sql);

$stats = ['user' => 0, 'expert' => 0];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        if ($row['role'] === 'user') {
            $stats['user'] = $row['total'];
        } elseif ($row['role'] === 'expert') {
            $stats['expert'] = $row['total'];
        }
    }
}

// Requête pour récupérer les données mensuelles
$sql_evolution = "SELECT MONTH(date_inscription) AS month, 
                          COUNT(CASE WHEN role = 'user' THEN 1 END) AS user,
                          COUNT(CASE WHEN role = 'expert' THEN 1 END) AS expert
                  FROM personne
                  WHERE YEAR(date_inscription) = YEAR(CURRENT_DATE)
                  GROUP BY MONTH(date_inscription)
                  ORDER BY month ASC";
$result_evolution = $conn->query($sql_evolution);

$evolutionData = [];
while ($row = $result_evolution->fetch_assoc()) {
    $monthName = DateTime::createFromFormat('!m', $row['month'])->format('F');
    $evolutionData[$monthName] = [
        'user' => (int)$row['user'],
        'expert' => (int)$row['expert']
    ];
}

$labels = json_encode(array_keys($evolutionData));
$userData = json_encode(array_column($evolutionData, 'user'));
$expertData = json_encode(array_column($evolutionData, 'expert'));
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

    <!-- Cartes statistiques -->
    <div class="stats-box">
        <div class="stat-card">
            <h3>Utilisateurs</h3>
            <p><?= $stats['user'] ?></p>
        </div>
        <div class="stat-card">
            <h3>Experts</h3>
            <p><?= $stats['expert'] ?></p>
        </div>
    </div>

    <!-- Courbe -->
    <div class="chart-container">
        <h2>Évolution des inscriptions</h2>
        <canvas id="evolutionChart"></canvas>
    </div>
</main>

<script>
    const ctx = document.getElementById('evolutionChart').getContext('2d');
    const evolutionChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= $labels ?>,
            datasets: [
                {
                    label: 'Utilisateurs',
                    data: <?= $userData ?>,
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, 0.1)',
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Experts',
                    data: <?= $expertData ?>,
                    borderColor: '#e89d3f',
                    backgroundColor: 'rgba(232, 157, 63, 0.1)',
                    fill: true,
                    tension: 0.4
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
</script>

</body>
</html>

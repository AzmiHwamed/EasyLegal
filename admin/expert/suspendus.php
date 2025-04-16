<?php
session_start();
session_regenerate_id(true); // Sécurisation de la session

// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "easylegal";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Fonction pour récupérer les experts
function getExperts($limit = 50, $offset = 0) {
    global $conn;
    $sql = "SELECT * FROM personne WHERE role = 'expert' LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Pagination
$limit = 50;
$offset = isset($_GET['page']) ? ($_GET['page'] - 1) * $limit : 0;
$experts = getExperts($limit, $offset);

// Fonction pour suspendre un expert
function suspendreExpert($id) {
    global $conn;
    $sql = "UPDATE personne SET statut = 'suspendu' WHERE id = ? AND role = 'expert'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

// Fonction pour annuler la suspension
function annulerSuspension($id) {
    global $conn;
    $sql = "UPDATE personne SET statut = 'actif' WHERE id = ? AND role = 'expert'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

// Traitement AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax'])) {
    $id = intval($_POST['id']);
    $action = $_POST['action'];

    if ($action === 'suspendre') {
        $success = suspendreExpert($id);
        echo json_encode([
            'success' => $success,
            'newStatut' => $success ? 'suspendu' : null,
            'message' => $success ? "Expert suspendu." : "Erreur lors de la suspension."
        ]);
    } elseif ($action === 'annuler') {
        $success = annulerSuspension($id);
        echo json_encode([
            'success' => $success,
            'newStatut' => $success ? 'actif' : null,
            'message' => $success ? "Suspension annulée." : "Erreur lors de l'annulation."
        ]);
    }
    exit;
}

// Nom utilisateur affiché dans la sidebar
$nom_utilisateur = isset($_SESSION['nom']) ? $_SESSION['nom'] : "Admin";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Suspendre des Experts</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f4f6f9;
            display: flex;
            min-height: 100vh;
            color: #333;
            font-family: 'Segoe UI', sans-serif;
        }
        .sidebar {
            width: 260px;
            background: linear-gradient(135deg, #e89d3f, #e8a043);
            color: white;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            padding: 40px 25px;
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.1);
        }
        .sidebar h2 {
            margin-bottom: 35px;
            font-size: 22px;
            font-weight: 600;
            text-align: center;
        }
        .sidebar nav ul {
            list-style: none;
            padding: 0;
        }
        .sidebar nav ul li {
            margin: 20px 0;
        }
        .sidebar nav ul li a {
            color: #fff;
            text-decoration: none;
            padding: 12px 18px;
            display: block;
            font-size: 16px;
            border-radius: 8px;
            transition: background 0.3s, padding-left 0.3s;
        }
        .sidebar nav ul li a:hover {
            background-color: rgba(255, 255, 255, 0.15);
            padding-left: 28px;
        }
        .main-content {
            margin-left: 260px;
            padding: 40px;
            width: calc(100% - 260px);
            background-color: #fff;
            min-height: 100vh;

        }
        .main-content h1 {
            font-size: 34px;
            margin-bottom: 20px;
            font-weight: 600;
            color: #222;
            text-align: center;

        }
        .btn {
            background-color: #ff9800;
            color: white;
            padding: 10px 22px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: background-color 0.3s ease, transform 0.2s;
            border: none;
        }
        .btn:hover {
            background-color: #e68900;
            transform: scale(1.05);
            color: white;
        }
        .alert {
            margin-top: 20px;
            padding: 10px;
        }
        .message.success {
            background-color: #dff0d8;
            color: #3c763d;
        }
        .message.error {
            background-color: #f2dede;
            color: #a94442;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Bienvenue, <?= htmlspecialchars($nom_utilisateur); ?> 👋</h2>
    <nav>
        <ul>
            <li><a href="../user/index.php">Gérer les utilisateurs</a></li>
            <li><a href="../forum/index.php">Gérer le forum</a></li>
            <li><a href="../text/index.php">Gérer les textes juridiques</a></li>
            <li><a href="../expert/index.php">Gérer les experts</a></li>
            <li><a href="../adminnav/index.php">Accueil</a></li>
        </ul>
    </nav>
</div>

<div class="main-content">
    <h1>Suspendre des Experts</h1>

    <div id="alert-container"></div>

    <div class="card mt-3">
        <div class="card-header">Liste des Experts</div>
        <div class="card-body">
            <table class="table table-bordered" id="expertsTable">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($experts as $expert): ?>
                    <tr id="expert-row-<?= $expert['id'] ?>">
                        <td><?= htmlspecialchars($expert['id']) ?></td>
                        <td><?= htmlspecialchars($expert['nom']) ?></td>
                        <td><?= htmlspecialchars($expert['Email']) ?></td>
                        <td><?= htmlspecialchars($expert['telephone']) ?></td>
                        <td>
                            <button class="btn btn-sm" 
                                onclick="changerStatut(<?= $expert['id'] ?>, '<?= $expert['statut'] === 'actif' ? 'suspendre' : 'annuler' ?>')">
                                <?= $expert['statut'] === 'actif' ? 'Confirmer' : 'Annuler Suspension' ?>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function changerStatut(id, action) {
    if (!confirm("Êtes-vous sûr de vouloir " + (action === 'suspendre' ? 'suspendre' : 'annuler la suspension') + " ?")) {
        return;
    }

    fetch("", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams({
            ajax: true,
            id: id,
            action: action
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const btn = document.querySelector(`#expert-row-${id} button`);
            btn.innerText = data.newStatut === 'actif' ? "Confirmer" : "Annuler Suspension";
            btn.setAttribute("onclick", `changerStatut(${id}, '${data.newStatut === 'actif' ? 'suspendre' : 'annuler'}')`);
            showAlert(data.message, 'success');
        } else {
            showAlert(data.message, 'danger');
        }
    })
    .catch(() => showAlert("Une erreur est survenue.", 'danger'));
}

function showAlert(message, type) {
    const alertContainer = document.getElementById("alert-container");
    alertContainer.innerHTML = `<div class="alert alert-${type}">${message}</div>`;
    setTimeout(() => alertContainer.innerHTML = '', 3000);
}
</script>
</body>
</html>

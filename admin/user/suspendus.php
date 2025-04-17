<?php
// session_start();

// $servername = "localhost";
// $username = "root";
// $password = "";
// $dbname = "easylegal";
// $conn = new mysqli($servername, $username, $password, $dbname);
// if ($conn->connect_error) {
//     die("Échec de la connexion : " . $conn->connect_error);
// }

// function getUsers($limit = 50, $offset = 0) {
//     global $conn;
//     $sql = "SELECT * FROM personne WHERE role = 'user' LIMIT ? OFFSET ?";
//     $stmt = $conn->prepare($sql);
//     $stmt->bind_param("ii", $limit, $offset);
//     $stmt->execute();
//     $result = $stmt->get_result();
//     return $result->fetch_all(MYSQLI_ASSOC);
// }

// function suspendreUsers($id) {
//     global $conn;
//     $sql = "UPDATE personne SET statut = 'suspendu' WHERE id = ? AND role = 'user'";
//     $stmt = $conn->prepare($sql);
//     $stmt->bind_param("i", $id);
//     return $stmt->execute();
// }

// function annulerSuspension($id) {
//     global $conn;
//     $sql = "UPDATE personne SET statut = 'actif' WHERE id = ? AND role = 'user'";
//     $stmt = $conn->prepare($sql);
//     $stmt->bind_param("i", $id);
//     return $stmt->execute();
// }

// // Traitement AJAX
// if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax'])) {
//     $id = intval($_POST['id']);
//     $action = $_POST['action'];

//     if ($action === 'suspendre') {
//         $success = suspendreUsers($id);
//         echo json_encode([
//             'success' => $success,
//             'newStatut' => $success ? 'suspendu' : null,
//             'message' => $success ? "Utilisateur suspendu." : "Erreur lors de la suspension."
//         ]);
//     } elseif ($action === 'annuler') {
//         $success = annulerSuspension($id);
//         echo json_encode([
//             'success' => $success,
//             'newStatut' => $success ? 'actif' : null,
//             'message' => $success ? "Suspension annulée." : "Erreur lors de l'annulation."
//         ]);
//     }
//     exit;
// }

// // Données initiales
// $limit = 50;
// $offset = isset($_GET['page']) && is_numeric($_GET['page']) ? ($_GET['page'] - 1) * $limit : 0;
// $users = getUsers($limit, $offset);
$nom_utilisateur = isset($_SESSION['nom']) ? $_SESSION['nom'] : "Admin";
//
 ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Suspension Utilisateurs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Bienvenue, <?= htmlspecialchars($nom_utilisateur); ?> 👋</h2>
        <nav>
            <ul>
                <li><a href="../user/index.php">Gérer les Utilisateurs</a></li>
                <li><a href="../forum/index.php">Gérer le forum</a></li>
                <li><a href="../text/index.php">Gérer les textes juridiques</a></li>
                <li><a href="../expert/index.php">Gérer les experts</a></li>
                <li><a href="../adminnav/index.php">Accueil</a></li>
            </ul>
        </nav>
    </div>

    <div class="main-content">
    <?php
$pdo = new PDO('mysql:host=localhost;dbname=easylegal', 'root', '');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm']) && isset($_POST['id'])) {
    $id = $_POST['id'];

    // Get current statut
    $stmt = $pdo->prepare("SELECT statut FROM personne WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch();

    if ($user) {
        $newstatut = $user['statut'] === 'actif' ? 'suspendu' : 'actif';

        // Update statut
        $update = $pdo->prepare("UPDATE personne SET statut = ? WHERE id = ?");
        $update->execute([$newstatut, $id]);

        header("Location: index.php");
        exit;
    }
} elseif (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $pdo->prepare("SELECT statut FROM personne WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch();

    if ($user) {
        $action = $user['statut'] === 'actif' ? 'suspendre' : 'activer';
        ?>
        <div class="container mt-5">
            <h4>Êtes-vous sûr de vouloir <?= htmlspecialchars($action) ?> cet utilisateur ?</h4>
            <form method="POST" action="suspendus.php">
                <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
                <input type="hidden" name="confirm" value="1">
                <button class="btn btn-primary" type="submit">Oui</button>
                <a href="index.php" class="btn btn-secondary">Non</a>
            </form>
        </div>
        <?php
    } else {
        echo "Utilisateur introuvable.";
    }
}
?>


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
            const btn = document.querySelector(`#user-row-${id} button`);
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

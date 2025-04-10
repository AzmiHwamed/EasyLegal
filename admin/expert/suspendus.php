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

// Fonction pour récupérer les experts avec une pagination dynamique
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
    $sql = "UPDATE personne SET statut = 'suspendu' WHERE id = ? AND role = 'expert'";  // Vérifier que l'utilisateur est un expert
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

// Fonction pour annuler la suspension d'un expert
function annulerSuspension($id) {
    global $conn;
    $sql = "UPDATE personne SET statut = 'actif' WHERE id = ? AND role = 'expert'";  // Remettre le statut à 'actif'
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

// Gestion de la suspension ou annulation de l'expert
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['suspendre_utilisateur']) && isset($_POST['id'])) {
        $id = intval($_POST['id']); // S'assurer que l'ID est un entier
        if (suspendreExpert($id)) {
            $message = "Expert suspendu avec succès.";
        } else {
            $message = "Erreur lors de la suspension de l'expert.";
        }
    }

    if (isset($_POST['annuler_suspension']) && isset($_POST['id'])) {
        $id = intval($_POST['id']);
        if (annulerSuspension($id)) {
            $message = "Suspension annulée avec succès.";
        } else {
            $message = "Erreur lors de l'annulation de la suspension.";
        }
    }
}

// Vérification de la connexion de l'utilisateur
$nom_utilisateur = isset($_SESSION['nom']) ? $_SESSION['nom'] : "Admin";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suspendre des experts</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    
    <!-- CSS personnalisé -->
    <style>
       /* Réinitialisation + police moderne */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', 'Arial', sans-serif;
}

body {
    background-color: #f4f6f9;
    display: flex;
    min-height: 100vh;
    overflow-x: hidden;
    color: #333;
}

/* Barre latérale */
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

/* Contenu principal */
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
}

/* Boutons */
.btn {
    background-color: #e89d3f;
    color: white;
    padding: 12px 25px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s ease, transform 0.2s;
    border: none;
}

.btn:hover {
    background-color: #e89d3f;
    transform: scale(1.05);
}

/* Messages */
.message {
    background-color: #f2f2f2;
    color: #333;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 16px;
    text-align: center;
}

.message.success {
    background-color: #dff0d8;
    color: #3c763d;
}

.message.error {
    background-color: #f2dede;
    color: #a94442;
}

/* Lien Retour */
.back {
    display: block;
    margin-top: 20px;
    color: #e89d3f;
    text-decoration: none;
    font-size: 16px;
}

.back:hover {
    text-decoration: underline;
}

/* Responsive design */
@media screen and (max-width: 768px) {
    .sidebar {
        width: 100%;
        height: auto;
        position: relative;
        box-shadow: none;
        padding: 20px;
    }

    .main-content {
        margin-left: 0;
        width: 100%;
        padding: 20px;
    }
}

    </style>

    <!-- JavaScript personnalisé -->
    <script>
        // Fonction de confirmation avant suspension
        function confirmSuspend() {
            return confirm("Êtes-vous sûr de vouloir suspendre cet expert ?");
        }

        // Fonction de confirmation avant annulation de suspension
        function confirmAnnuler() {
            return confirm("Êtes-vous sûr de vouloir annuler la suspension de cet expert ?");
        }
    </script>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Bienvenue, <?php echo htmlspecialchars($nom_utilisateur); ?> 👋</h2>
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

    <!-- Main Content -->
    <div class="main-content">
        <h1>Suspendre des Experts</h1>

        <!-- Message d'alerte -->
        <?php if (isset($message)): ?>
        <div class="message <?php echo strpos($message, 'succès') !== false ? 'success' : 'error'; ?>">
            <?php echo $message; ?>
        </div>
        <?php endif; ?>

        <!-- Liste des experts -->
        <div class="card mt-3">
            <div class="card-header">Liste des Experts</div>
            <div class="card-body">
                <table class="table table-bordered">
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
                        <?php foreach ($experts as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['id']) ?></td>
                            <td><?= htmlspecialchars($user['nom']) ?></td>
                            <td><?= htmlspecialchars($user['Email']) ?></td>
                            <td><?= htmlspecialchars($user['telephone']) ?></td>
                            <td>
                                <?php if ($user['statut'] === 'actif'): ?>
                                    <form method="POST" style="display:inline;" onsubmit="return confirmSuspend();">
                                        <input type="hidden" name="id" value="<?= htmlspecialchars($user['id']) ?>">
                                        <button type="submit" name="suspendre_utilisateur" class="btn btn-warning btn-sm">Confirmer</button>
                                    </form>
                                <?php elseif ($user['statut'] === 'suspendu'): ?>
                                    <form method="POST" style="display:inline;" onsubmit="return confirmAnnuler();">
                                        <input type="hidden" name="id" value="<?= htmlspecialchars($user['id']) ?>">
                                        <button type="submit" name="annuler_suspension" class="btn btn-success btn-sm">Annuler Suspension</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>


    </div>

</body>
</html>

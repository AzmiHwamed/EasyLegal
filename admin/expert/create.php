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

// Ajouter un expert
if (isset($_POST['ajouter_utilisateur'])) {
    $nom = trim($_POST['nom']);
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL); // Validation de l'email
    $role = 'expert'; // Définir le rôle sur "expert"
    $telephone = trim($_POST['telephone']);
    $motdepasse = password_hash($_POST['motdepasse'], PASSWORD_BCRYPT);

    // Vérifier si l'email est valide avant d'insérer
    if ($email && $nom && $role && $telephone && $motdepasse) {
        $stmt = $conn->prepare("INSERT INTO personne (nom, Email, role, motdepasse, telephone) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nom, $email, $role, $motdepasse, $telephone);
        $stmt->execute();
        $stmt->close();
    } else {
        $_SESSION['error_message'] = "L'email n'est pas valide ou les champs sont manquants.";
    }
}

// Récupérer les experts
$sql = "SELECT * FROM personne WHERE role = 'expert' LIMIT 50";
$result_users = $conn->query($sql);
$experts = $result_users->fetch_all(MYSQLI_ASSOC);

// Vérification de connexion de l'utilisateur
$nom_utilisateur = isset($_SESSION['nom']) ? $_SESSION['nom'] : "Admin";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Experts</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    
    <!-- CSS personnalisé -->
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f6f9;
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background-color: #34495e;
            color: white;
            height: 100%;
            position: fixed;
            top: 0;
            left: 0;
            padding: 40px 30px;
            box-shadow: 2px 0 20px rgba(0, 0, 0, 0.7);
        }

        .sidebar h2 {
            margin-bottom: 40px;
            font-size: 24px;
            font-weight: 700;
            text-align: center;
            letter-spacing: 1px;
            color: #ecf0f1;
        }

        .sidebar nav ul {
            padding-left: 0;
            list-style: none;
        }

        .sidebar nav ul li {
            margin: 25px 0;
        }

        .sidebar nav ul li a {
            color: #ecf0f1;
            text-decoration: none;
            font-size: 18px;
            padding: 12px 20px;
            display: block;
            border-radius: 30px;
            transition: all 0.3s ease;
        }

        .sidebar nav ul li a:hover {
            background-color: #1abc9c;
            color: white;
            padding-left: 25px;
            transform: translateX(10px);
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }

        .main-content {
            margin-left: 280px;
            padding: 40px;
            width: calc(100% - 280px);
            background-color: #fff;
            min-height: 100vh;
        }

        h1 {
            color: #2c3e50;
            font-size: 28px;
            margin-bottom: 20px;
        }

        @media screen and (max-width: 768px) {
            .sidebar {
                width: 100%;
                position: relative;
                padding: 15px;
            }

            .main-content {
                margin-left: 0;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Bienvenue, <?php echo htmlspecialchars($nom_utilisateur); ?> 👋</h2>
        <nav role="navigation">
            <ul>
                <li><a href="../user/index.php" onclick="return confirmNavigation(this.href);" aria-label="Gérer les utilisateurs">Gérer les utilisateurs</a></li>
                <li><a href="../forum/index.php" onclick="return confirmNavigation(this.href);" aria-label="Gérer le forum">Gérer le forum</a></li>
                <li><a href="../text/index.php" onclick="return confirmNavigation(this.href);" aria-label="Gérer les textes juridiques">Gérer les textes juridiques</a></li>
                <li><a href="../expert/index.php" onclick="return confirmNavigation(this.href);" aria-label="Gérer les experts">Gérer les experts</a></li>
            </ul>
        </nav>
    </div>

    <div class="main-content">
        <h1>Gestion des Experts</h1>

        <!-- Affichage du message d'erreur ou de succès -->
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['error_message'] ?>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <!-- Formulaire d'ajout d'expert -->
        <div class="card mt-3">
            <div class="card-header">Ajouter un expert</div>
            <div class="card-body">
                <form method="POST" name="addUserForm" onsubmit="return validateForm()">
                    <div class="mb-2">
                        <label>Nom :</label>
                        <input type="text" name="nom" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label>Email :</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label>Mot de passe :</label>
                        <input type="password" name="motdepasse" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label>Téléphone :</label>
                        <input type="text" name="telephone" class="form-control" required>
                    </div>
                    <button type="submit" name="ajouter_utilisateur" class="btn btn-primary">Ajouter</button>
                </form>
            </div>
        </div>

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
                                <!-- Boutons d'action -->
                                <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete(<?= htmlspecialchars($user['id']) ?>)">Supprimer</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script>
        // Fonction de validation du formulaire d'ajout d'expert
        function validateForm() {
            var nom = document.forms["addUserForm"]["nom"].value;
            var email = document.forms["addUserForm"]["email"].value;
            var telephone = document.forms["addUserForm"]["telephone"].value;
            var motdepasse = document.forms["addUserForm"]["motdepasse"].value;

            if (nom == "" || email == "" || telephone == "" || motdepasse == "") {
                alert("Tous les champs doivent être remplis !");
                return false;
            }

            var emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
            if (!email.match(emailRegex)) {
                alert("Veuillez entrer un email valide.");
                return false;
            }
        }

        // Fonction de confirmation avant suppression
        function confirmDelete(userId) {
            if (confirm("Êtes-vous sûr de vouloir supprimer cet expert ?")) {
                window.location.href = "delete.php?id=" + userId;
            }
        }

        function confirmNavigation(url) {
            if (confirm("Voulez-vous vraiment accéder à cette page ?")) {
                window.location.href = url;
            }
            return false;
        }
    </script>
</body>
</html>

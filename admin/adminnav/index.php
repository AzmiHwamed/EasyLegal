<?php
session_start();

// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "easylegal";

// Création de la connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Définitions des constantes pour les noms de tables et de colonnes
define('TABLE_PERSONNE', 'personne');
define('TABLE_FORUM', 'forum');
define('TABLE_TEXTES_JURIDIQUES', 'textjuridique');

// Fonction pour ajouter un utilisateur
function ajouterUtilisateur($nom, $email, $role, $motdepasse, $telephone) {
    global $conn;
    $hash = password_hash($motdepasse, PASSWORD_BCRYPT);
    $sql = "INSERT INTO " . TABLE_PERSONNE . " (nom, Email, role, motdepasse, telephone) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $nom, $email, $role, $hash, $telephone);
    return $stmt->execute();
}

// Fonction pour supprimer un utilisateur
function supprimerUtilisateur($id) {
    global $conn;
    $sql = "DELETE FROM " . TABLE_PERSONNE . " WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

// Fonction pour afficher les utilisateurs
function afficherUtilisateurs() {
    global $conn;
    $sql = "SELECT * FROM " . TABLE_PERSONNE;
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Fonction générique pour ajouter un contenu (post ou texte juridique)
function ajouterContenu($table, $contenu) {
    global $conn;
    $sql = "INSERT INTO $table (Contenu) VALUES (?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $contenu);
    return $stmt->execute();
}

// Fonction générique pour afficher un contenu
function afficherContenus($table) {
    global $conn;
    $sql = "SELECT * FROM $table";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Gestion des formulaires
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['ajouter_utilisateur'])) {
        ajouterUtilisateur($_POST['nom'], $_POST['email'], $_POST['role'], $_POST['motdepasse'], $_POST['telephone']);
    }
    if (isset($_POST['supprimer_utilisateur'])) {
        supprimerUtilisateur($_POST['id']);
    }
    if (isset($_POST['ajouter_post_forum'])) {
        ajouterContenu(TABLE_FORUM, $_POST['contenu']);
    }
    if (isset($_POST['ajouter_texte_juridique'])) {
        ajouterContenu(TABLE_TEXTES_JURIDIQUES, $_POST['contenu']);
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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

        /* Style général de la sidebar */
.sidebar {
    width: 280px;
    background-color: #34495e;
    color: white;
    height: 100%;
    position: fixed;
    top: 0;
    left: 0;
    padding: 40px 30px;
    box-shadow: 2px 0 20px rgba(0, 0, 0, 0.2);
    transition: width 0.3s ease-in-out, padding 0.3s ease-in-out;
    font-family: 'Roboto', sans-serif;
}

.sidebar h2 {
    margin-bottom: 40px;
    font-size: 28px;
    font-weight: 700;
    text-align: center;
    letter-spacing: 1px;
    color: #ecf0f1;
}

.sidebar ul {
    padding-left: 0;
    list-style: none;
}

.sidebar ul li {
    margin: 25px 0;
    display: flex;
    align-items: center;
}

.sidebar ul li a {
    color: #ecf0f1;
    text-decoration: none;
    font-size: 18px;
    padding: 12px 20px;
    display: block;
    border-radius: 30px;
    transition: all 0.3s ease;
    letter-spacing: 0.5px;
}

.sidebar ul li a:hover {
    background-color: #1abc9c;
    color: white;
    padding-left: 25px;
    transform: translateX(10px);
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
}

.sidebar ul li a.active {
    background-color: #16a085;
    color: white;
    font-weight: 600;
}

.sidebar ul li a i {
    margin-right: 15px;
    font-size: 22px;
}

/* Icônes élégantes */
.sidebar ul li a:hover i {
    transform: rotate(15deg);
    transition: transform 0.3s ease;
}

/* Effet de rétrécissement de la sidebar au survol */
.sidebar:hover {
    width: 300px;
    padding: 40px 35px;
}

/* Responsivité - plus chic et minimaliste pour les petites écrans */
@media screen and (max-width: 768px) {
    .sidebar {
        width: 230px;
        padding: 30px 20px;
    }

    .sidebar h2 {
        font-size: 24px;
    }

    .sidebar ul li {
        margin: 20px 0;
    }

    .sidebar ul li a {
        font-size: 16px;
        padding: 10px 15px;
    }

    .sidebar:hover {
        width: 230px;
    }
}


        .main-content {
            margin-left: 270px;
            padding: 40px;
            width: 100%;
            background-color: #fff;
            min-height: 100vh;
        }

        h1 {
            color: #2c3e50;
            font-size: 32px;
            margin-bottom: 20px;
        }

        h2 {
            color: #2c3e50;
            margin-top: 40px;
            font-size: 28px;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            border-radius: 8px;
            overflow: hidden;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
            font-size: 16px;
        }

        th {
            background-color: #f0f3f5;
            color: #34495e;
        }

        tr:nth-child(even) {
            background-color: #f9fafc;
        }

        form {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        input[type="text"], input[type="email"], input[type="password"], textarea {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9fafc;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        button {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color:rgb(117, 156, 168);
        }

        button:disabled {
            background-color: #95a5a6;
            cursor: not-allowed;
        }

        ul {
            list-style: none;
            margin-top: 20px;
        }

        ul li {
            margin-bottom: 10px;
            padding: 10px;
            background-color: #f9fafc;
            border-radius: 8px;
            font-size: 16px;
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

            table {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Admin</h2>
        <ul>
            <li><a href="#users">Gérer les utilisateurs</a></li>
            <li><a href="#forum">Gérer le forum</a></li>
            <li><a href="#legal">Gérer les textes juridiques</a></li>
        </ul>
    </div>

    <div class="main-content">
        <h1>Tableau de Bord</h1>

        <!-- Gestion des utilisateurs -->
        <h2 id="users">Utilisateurs</h2>
        <form method="post">
            <input type="text" name="nom" placeholder="Nom" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="role" placeholder="Rôle" required>
            <input type="password" name="motdepasse" placeholder="Mot de passe" required>
            <input type="text" name="telephone" placeholder="Téléphone" required>
            <button type="submit" name="ajouter_utilisateur">Ajouter</button>
        </form>
        <table>
            <thead>
                <tr><th>ID</th><th>Nom</th><th>Email</th><th>Rôle</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach (afficherUtilisateurs() as $user): ?>
                    <tr>
                        <td><?= $user['id'] ?></td>
                        <td><?= $user['nom'] ?></td>
                        <td><?= $user['Email'] ?></td>
                        <td><?= $user['role'] ?></td>
                        <td>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                <button type="submit" name="supprimer_utilisateur">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Gestion du forum -->
        <h2 id="forum">Forum</h2>
        <form method="post">
            <textarea name="contenu" placeholder="Nouveau post" required></textarea>
            <button type="submit" name="ajouter_post_forum">Ajouter</button>
        </form>
        <ul>
            <?php foreach (afficherContenus(TABLE_FORUM) as $post): ?>
                <li><?= $post['contenu'] ?></li>
            <?php endforeach; ?>
        </ul>

        <!-- Gestion des textes juridiques -->
        <h2 id="legal">Textes Juridiques</h2>
        <form method="post">
            <textarea name="contenu" placeholder="Ajouter un texte juridique" required></textarea>
            <button type="submit" name="ajouter_texte_juridique">Ajouter</button>
        </form>
        <ul>
            <?php foreach (afficherContenus(TABLE_TEXTES_JURIDIQUES) as $texte): ?>
                <li><?= $texte['Contenu'] ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>

<?php
$host = "localhost";
$user = "root"; // Ton utilisateur MySQL
$pass = ""; // Ton mot de passe MySQL
$dbname = "easylegal"; // Nom de ta base de données

// Connexion à la base de données
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Requête pour récupérer les messages
$query = "SELECT libelle FROM message ORDER BY id DESC";
$result = $conn->query($query);

// Vérifier si la requête a réussi
if (!$result) {
    die("Erreur dans la requête : " . $conn->error);
}

// Envoyer un message (si formulaire soumis)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $message = $_POST['message'];

    $stmt = $conn->prepare("INSERT INTO message (libelle) VALUES (?)");
    $stmt->bind_param("s", $message);
    $stmt->execute();
    $stmt->close();

    // Rediriger après l'insertion pour éviter la soumission multiple
    header("Location: forum.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forum - EasyLegal</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
        }
        header {
            background: #b89149;
            color: white;
            padding: 20px 0;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        header h1 {
            font-size: 2.2em;
            margin: 0;
        }
        nav ul {
            list-style: none;
            padding: 0;
            margin: 10px 0 0;
            display: flex;
            justify-content: center;
        }
        nav ul li {
            margin: 0 15px;
        }
        nav ul li a {
            color: white;
            font-size: 1em;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 5px;
            transition: background 0.3s;
        }
        nav ul li a:hover {
            background-color: #d8aa5b;
        }
        main {
            display: flex;
            justify-content: center;
            padding: 30px;
        }
        .forum {
            background: white;
            width: 60%;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .forum h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
            font-size: 1.8em;
        }
        .message {
            background: #fafafa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .message p {
            font-size: 1.1em;
            color: #333;
        }
        .post-message {
            display: flex;
            justify-content: center;
            margin-top: 30px;
        }
        .post-message form {
            background: white;
            width: 60%;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        textarea {
            width: 100%;
            padding: 10px;
            font-size: 1em;
            border-radius: 5px;
            border: 1px solid #ddd;
            margin-bottom: 15px;
            resize: vertical;
        }
        button {
            width: 100%;
            padding: 12px;
            font-size: 1.1em;
            background-color: #d8aa5b;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover {
            background-color: #b89149;
        }
    </style>
</head>
<body>
    <header>
        <h1>EasyLegal Forum</h1>
        <nav>
            <ul>
                <li><a href="#">Accueil</a></li>
                <li><a href="#">Forum</a></li>
                <li><a href="#">Aide</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <section class="forum">
            <h2>Discussion</h2>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="message">
                    <p><?= nl2br(htmlspecialchars($row['libelle'])) ?></p>
                </div>
            <?php endwhile; ?>
        </section>
    </main>
    <section class="post-message">
        <form action="forum.php" method="POST">
            <textarea name="message" placeholder="Écrivez votre message..." required></textarea>
            <button type="submit">Envoyer</button>
        </form>
    </section>
</body>
</html>

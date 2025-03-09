<!DOCTYPE html> 
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyLegal - Recherche</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f8f8;
            text-align: center;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #f4dfb6;
            padding: 20px;
        }

        h1 {
            color: #d38d2c;
        }

        p {
            font-size: 16px;
            color: #444;
        }

        form {
            margin: 20px;
        }

        input, select {
            padding: 10px;
            width: 300px;
            border: 1px solid #ccc;
            border-radius: 5px;
            display: block;
            margin: 10px auto;
        }

        button {
            padding: 10px;
            background-color: #d38d2c;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background: white;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #d38d2c;
            color: white;
        }

        .success {
            color: green;
        }

        .error {
            color: red;
        }
    </style>
</head>
<body>

    <header>
        <h1>Bienvenue dans EasyLegal</h1>
        <p>Cette bibliothèque numérique a pour objectif d'augmenter la visibilité et l'accessibilité au texte juridique.</p>
    </header>

    <?php
    include '../dbconfig/index.php';

    // Connexion sécurisée et gestion des erreurs
    if (!$conn) {
        die("<p class='error'>Erreur de connexion à la base de données.</p>");
    }

    // Récupérer et sécuriser les valeurs GET
    $search = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';
    $allowed_sort_columns = ['id', 'Date', 'Titre', 'Type', 'Theme'];
    $sort = isset($_GET['sort']) && in_array($_GET['sort'], $allowed_sort_columns) ? $_GET['sort'] : 'id';

    // Afficher un message personnalisé si les paramètres sont fournis
    $subject = isset($_GET['subject']) ? htmlspecialchars($_GET['subject']) : '';
    $web = isset($_GET['web']) ? htmlspecialchars($_GET['web']) : '';
    if (!empty($subject) && !empty($web)) {
        echo "<p>Study $subject at $web</p>";
    }

    // Requête SQL sécurisée
    $stmt = $conn->prepare("SELECT id, Date, Titre, Type, Theme FROM textjuridique WHERE Titre LIKE ? ORDER BY $sort ASC");
    $search_param = "%$search%";
    $stmt->bind_param("s", $search_param);
    $stmt->execute();
    $result = $stmt->get_result();
    ?>

    <main>
        <form action="" method="GET">
            <label for="search">Recherche :</label>
            <input type="text" name="search" id="search" placeholder="Rechercher un texte juridique..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit">🔍 Rechercher</button>
        </form>

        <h2>Résultats de recherche :</h2>
        <table>
            <tr>
                <th><a href="?search=<?= htmlspecialchars($search) ?>&sort=id">ID</a></th>
                <th><a href="?search=<?= htmlspecialchars($search) ?>&sort=Date">Date</a></th>
                <th><a href="?search=<?= htmlspecialchars($search) ?>&sort=Titre">Titre</a></th>
                <th><a href="?search=<?= htmlspecialchars($search) ?>&sort=Type">Type</a></th>
                <th><a href="?search=<?= htmlspecialchars($search) ?>&sort=Theme">Thème</a></th>
            </tr>

            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['Date']) ?></td>
                    <td><?= htmlspecialchars($row['Titre']) ?></td>
                    <td><?= htmlspecialchars($row['Type']) ?></td>
                    <td><?= htmlspecialchars($row['Theme']) ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </main>

</body>
</html>

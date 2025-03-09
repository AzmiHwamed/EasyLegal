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

    // Récupérer les valeurs GET de manière sécurisée
    $search = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';
    $sort = isset($_GET['sort']) ? htmlspecialchars($_GET['sort']) : 'id';

    // Message personnalisé avec $_GET['subject'] et $_GET['web']
    $subject = isset($_GET['subject']) ? htmlspecialchars($_GET['subject']) : '';
    $web = isset($_GET['web']) ? htmlspecialchars($_GET['web']) : '';
    if (!empty($subject) && !empty($web)) {
        echo "<p>Study $subject at $web</p>";
    }

    // Préparation de la requête SQL avec LIKE pour la recherche
    $stmt = $pdo->prepare("SELECT * FROM documents WHERE Titre LIKE ? ORDER BY $sort ASC");
    $stmt->execute(["%$search%"]);
    $results = $stmt->fetchAll();
    ?>

    <main>
        <form action="" method="GET">
            <label for="search">Recherche :</label>
            <input type="text" name="search" id="search" placeholder="Rechercher un texte juridique..." value="<?= $search ?>">
            <button type="submit">🔍</button>
        </form>

        <h2>Résultats de recherche :</h2>
        <table>
            <tr>
                <th><a href="?search=<?= $search ?>&sort=id">ID</a></th>
                <th><a href="?search=<?= $search ?>&sort=Date">Date</a></th>
                <th><a href="?search=<?= $search ?>&sort=Titre">Titre</a></th>
                <th><a href="?search=<?= $search ?>&sort=Type">Type</a></th>
                <th><a href="?search=<?= $search ?>&sort=Theme">Thème</a></th>
            </tr>

            <?php foreach ($results as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['id']) ?></td>
                <td><?= htmlspecialchars($row['Date']) ?></td>
                <td><?= htmlspecialchars($row['Titre']) ?></td>
                <td><?= htmlspecialchars($row['Type']) ?></td>
                <td><?= htmlspecialchars($row['Theme']) ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </main>

</body>
</html>

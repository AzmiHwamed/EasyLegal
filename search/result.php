@ -1,213 +1,213 @@
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
        .collapsible {
            display:flex;
            justify-content:space-between;
            background-color: white;
            border:1px black solid !important;
            border-radius:15px;
            color: black;
            cursor: pointer;
            padding: 18px;
            width: 100%;
            border: none;
            text-align: left;
            outline: none;
            font-size: 15px;
}

.active, .collapsible:hover {
  background-color: #EEE;
}

.content {
  padding: 0 18px;
  max-height: 0;
  overflow: hidden;
  transition: max-height 0.2s ease-out;
  background-color: #f1f1f1;
}
.result {
    display:flex;
    flex-direction:column;
}
.result div{
    margin:10px;
    margin-top:20px;

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

    // Récupérer et sécuriser les valeurs GET    // Database connection (make sure $conn is properly initialized)
    $search = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';
    $type = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : 'avis,lois,arretes'; // Default values
    if ($type === 'tous') {
        $type = 'avis,lois,arretes';
    }
    
    // Convert type string to an array
    $typeArray = explode(',', $type);
    
    // Ensure sorting is safe
    $allowed_sort_columns = ['id', 'Date', 'Titre', 'Type', 'Theme'];
    $sort = isset($_GET['sort']) && in_array($_GET['sort'], $allowed_sort_columns) ? $_GET['sort'] : 'id';
    
    // Optional message display
    $subject = isset($_GET['subject']) ? htmlspecialchars($_GET['subject']) : '';
    $web = isset($_GET['web']) ? htmlspecialchars($_GET['web']) : '';
    if (!empty($subject) && !empty($web)) {
        echo "<p>Study $subject at $web</p>";
    }
    
    // Dynamically create placeholders (?,?,?)
    $placeholders = implode(',', array_fill(0, count($typeArray), '?'));
    
    $query = "SELECT id, Date, Titre, Type, Theme  , Contenu
              FROM textjuridique 
              WHERE Contenu LIKE ? 
              AND Type IN ($placeholders) 
              ORDER BY $sort ASC";
    
    $stmt = $conn->prepare($query);
    
    // Prepare parameters for binding
    $search_param = "%$search%";
    $params = array_merge([$search_param], $typeArray);
    
    // Prepare bind types (all strings)
    $bind_types = str_repeat('s', count($params));
    
    // Bind parameters
    $stmt->bind_param($bind_types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    ?>

    <main>
        <form action="" method="GET">
            <label for="search">Recherche :</label>
            <input type="text" name="search" id="search" placeholder="Rechercher un texte juridique..." value="<?= htmlspecialchars($search) ?>">
            <select name="type">
            <option value="tous">tous</option>
            <option value="avis">avis</option>
            <option value="lois">lois</option>
            <option value="arretes">arretes</option>
            </select>


            <button type="submit">🔍 Rechercher</button>
        </form>

        <h2>Résultats de recherche :</h2>
        <?php if(isset($_GET['search']) ||isset($_GET['type'])) echo "<h2>Résultats de recherche :</h2>" ?>
      
            <div class="result">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div>
                    <button class="collapsible">
                        <span><?= htmlspecialchars($row['Titre']) ?></span>
                        <span><?= htmlspecialchars($row['Titre']) ?></span>
                    </button>
                    <div class="content">
                        <p><?= htmlspecialchars($row['Contenu']) ?></p>
                    </div>
                </div>
            <?php endwhile; ?>
            </div>
    </main>
<script>
    var coll = document.getElementsByClassName("collapsible");
var i;

for (i = 0; i < coll.length; i++) {
  coll[i].addEventListener("click", function() {
    this.classList.toggle("active");
    var content = this.nextElementSibling;
    if (content.style.maxHeight){
      content.style.maxHeight = null;
    } else {
      content.style.maxHeight = content.scrollHeight + "px";
    } 
  });
}
</script>
</body>
</html>

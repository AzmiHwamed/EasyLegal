<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyLegal - Recherche</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f4ef;
            text-align: center;
            margin: 0;
            padding: 0;
        }

        nav {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            max-width: 100%;
            height: 5vh;
            padding: 1%;
            background-color: #F3EEE5;
            box-shadow: 5px 12px 10px rgba(0, 0, 0, 0.2);
            position: sticky;
            top: 0;
        }

        nav a img {
            width: 4vw !important;
            max-height: 100%;
            min-height: 100%;
        }

        nav span {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            width: 20%;
        }

        nav span a {
            text-decoration: none;
            color: #000;
            font-weight: bolder;
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

        .filter-container {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 10px;
        }

        .filter-container select {
            width: 150px;
        }

        button {
            padding: 10px;
            background-color: #d38d2c;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            margin-top: 10px;
        }
        .header-box {
    background-color: #f8f4ef; 
    border: 2px solid #d38d2c;
    border-radius: 5px; 
    padding: 15px 20px; 
    margin: 10px auto; 
    width: 80%; 
    text-align: left; 
}

h1 {
    font-size: 1.5em; 
    font-weight: bold; 
    color: #d38d2c; 
    margin: 0; 
    padding-bottom: 5px; 
    border-bottom: 2px solid #d38d2c; 
    display: inline-block; 
}

h2 {
    font-size: 1em; 
    color: #333; 
    margin-top: 10px; 
    font-weight: normal; 
}

#search {
    width: 60%; 
    height: 30px; 
    font-size: 1.2em;
    padding: 10px;
    border-radius: 10px;
    border: 1px solid #ccc;
}

form {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-top: 20px;
}
h3{
    color:#d38d2c;
}
    </style>
</head>
<body>
    <nav>
        <a href="#">
            <img src="../assets/logo.png" alt="Icône de la justice" class="hero-image">
        </a>
        <span>
            <a href="#">Rechercher</a>
            <a href="#">Forum</a>
            <a href="#">Disscuter</a>
        </span>
        <a><img src="../assets/Male User.png" alt="Account" style="width: 3vw !important;"></a>
    </nav>
    <div class="header-box">
        <h1>Bienvenue dans EasyLegal</h1>
        <h2>Cette bibliothèque numérique a pour objectif d'augmenter la visibilité et l'accessibilité au texte juridique.</h2>
    </div>

    <main>
        <form action="" method="GET">
            <h3>Rechercher :</h3>
            <input type="text" name="search" id="search" placeholder="Rechercher un texte juridique...">
            
            <h3>Trier par :</h3>
            <div class="filter-container">
                <select name="type">
                    <option value="">Type</option>
                    <option value="avis">Avis</option>
                    <option value="lois">Lois</option>
                    <option value="arretes">Arrêtés</option>
                </select>
                
                <input type="date" name="date">

                <select name="annee">
                    <option value="">Année</option>
                    <?php
                        for ($i = 2024; $i >= 1956; $i--) {
                            echo "<option value=\"$i\">$i</option>";
                        }
                    ?>
                </select>

                <select name="numero">
                    <option value="">Numéro</option>
                    <?php
                        for ($i = 1; $i <= 1000; $i++) {
                            echo "<option value=\"$i\">$i</option>";
                        }
                    ?>
                </select>
            </div>

            <button type="submit">🔍 Rechercher</button>
        </form>

        <?php
        include '../dbconfig/index.php';

        if ($conn) {
            $search = isset($_GET['search']) ? trim($_GET['search']) : '';
            $type = isset($_GET['type']) ? trim($_GET['type']) : '';
            $date = isset($_GET['date']) ? trim($_GET['date']) : '';
            $annee = isset($_GET['annee']) ? trim($_GET['annee']) : '';
            $numero = isset($_GET['numero']) ? trim($_GET['numero']) : '';

            // Vérifier si une recherche est effectuée
            if (!empty($search) || !empty($type) || !empty($date) || !empty($annee) || !empty($numero)) {
                $query = "SELECT id, Date, Titre, Type, Theme, Contenu FROM textjuridique WHERE 1=1";

                if (!empty($search)) {
                    $query .= " AND (Contenu LIKE ? OR Titre LIKE ?)";
                    $search = "%$search%";
                }
                if (!empty($type)) {
                    $query .= " AND Type = ?";
                }
                if (!empty($date)) {
                    $query .= " AND Date = ?";
                }
                if (!empty($annee)) {
                    $query .= " AND YEAR(Date) = ?";
                }
                if (!empty($numero)) {
                    $query .= " AND id = ?";
                }

                $stmt = $conn->prepare($query);

                // Associer les paramètres dynamiquement
                $params = [];
                $types = '';
                if (!empty($search)) {
                    $types .= 'ss';
                    $params[] = $search;
                    $params[] = $search;
                }
                if (!empty($type)) {
                    $types .= 's';
                    $params[] = $type;
                }
                if (!empty($date)) {
                    $types .= 's';
                    $params[] = $date;
                }
                if (!empty($annee)) {
                    $types .= 's';
                    $params[] = $annee;
                }
                if (!empty($numero)) {
                    $types .= 'i';
                    $params[] = $numero;
                }

                if (!empty($params)) {
                    $stmt->bind_param($types, ...$params);
                }

                $stmt->execute();
                $result = $stmt->get_result();

                echo "<h2>Résultats de recherche :</h2>";
                echo "<div class='result'>";
                while ($row = $result->fetch_assoc()) {
                    echo "<div>
                            <button class='collapsible'>
                                <span>" . htmlspecialchars($row['Titre']) . "</span>
                            </button>
                            <div class='content'>
                                <p>" . htmlspecialchars($row['Contenu']) . "</p>
                            </div>
                          </div>";
                }
                echo "</div>";
            }
        }
        ?>

    </main>

    <script>
        document.querySelectorAll(".collapsible").forEach(button => {
            button.addEventListener("click", function () {
                this.classList.toggle("active");
                var content = this.nextElementSibling;
                content.style.maxHeight = content.style.maxHeight ? null : content.scrollHeight + "px";
            });
        });
    </script>
</body>
</html>

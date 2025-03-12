<?php
session_start();

if (!empty($_POST)) {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $conn = new mysqli($servername, $username, $password, $dbname);        
        if ($con->connect_error) {
            die("Connection failed: " . $con->connect_error);
        }

        // Vérifier si l'utilisateur existe déjà
        $stmt = $con->prepare("SELECT * FROM personne WHERE username = ?");
        $stmt->bind_param('s', $_POST['username']);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "Nom d'utilisateur déjà pris.";
        } else {
            // Hasher le mot de passe
            $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            
            // Insérer l'utilisateur dans la base de données
            $stmt = $con->prepare("INSERT INTO personne (username, password) VALUES (?, ?)");
            $stmt->bind_param('ss', $_POST['username'], $hashed_password);
            
            if ($stmt->execute()) {
                $_SESSION['user_id'] = $stmt->insert_id;
                echo "Inscription réussie.";
            } else {
                echo "Erreur lors de l'inscription.";
            }
        }
        
        $stmt->close();
        $con->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .registration-container {
            background: #fff;
            padding: 20px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            width: 300px;
            text-align: center;
        }
        input[type="text"], input[type="password"], input[type="number"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px;
            width: 100%;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        a {
            text-decoration: none;
            color: #007bff;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="registration-container">
        <h2>Registration</h2>
        <form action="" method="post" autocomplete="off">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required><br>
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required><br>
            <label for="phone">Téléphone:</label>
            <input type="number" name="uphone" id="phone" required><br>
            <button type="submit">Register</button>
        </form>
        <br>
        <a href="login.php">Login</a>
    </div>
</body>
</html>

<?php
session_start();
include ('../dbconfig/index.php');
if (!empty($_POST)) {
    if (isset($_POST['name']) && isset($_POST['password'])) {


        // Vérifier si l'utilisateur existe déjà
        $stmt = $conn->prepare("SELECT * FROM personne WHERE Email = ?");
        $stmt->bind_param('s', $_POST['email']);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "Nom d'utilisateur déjà pris.";
        } else {
            $stmt = $conn->prepare("INSERT INTO personne (nom, email,motdepasse,telephone) VALUES (?, ? , ? , ?)");
            $stmt->bind_param('ssss', $_POST['name'],$_POST['email'],$_POST['password'],$_POST['uphone']);

            if ($stmt->execute()) {
                $_SESSION['user_id'] = $stmt->insert_id;
                $_SESSION['type'] = 'user';
                echo "Inscription réussie.";
            } else {
                echo "Erreur lors de l'inscription.";
            }
        }
        
        $stmt->close();
        $conn->close();
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
        <label for="username">Nom:</label>
        <input type="text" name="name" id="username" required><br>
        <label for="username">Email:</label>
            <input type="text" name="email" id="username" required><br>
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

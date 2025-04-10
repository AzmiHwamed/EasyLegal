<?php
session_start();

// On peut pré-remplir ici une valeur d'exemple pour id_messagerie si besoin
// Exemple pour test : $id_messagerie = 1;
$id_messagerie = isset($_GET['id_messagerie']) ? $_GET['id_messagerie'] : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $targetDir = "uploads/";
    $fileName = basename($_FILES["fileToUpload"]["name"]);
    $targetFile = $targetDir . $fileName;
    $uploadOk = 1;
    $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    if ($_FILES["fileToUpload"]["error"] !== UPLOAD_ERR_OK) {
        echo "Erreur lors du téléchargement du fichier. Code erreur : " . $_FILES["fileToUpload"]["error"];
        $uploadOk = 0;
    }

    if (isset($_POST["submit"])) {
        $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
        if ($check !== false) {
            echo "Fichier image détecté : " . $check["mime"] . ".";
        } else {
            echo "Le fichier n'est pas une image.";
            $uploadOk = 0;
        }
    }

    if ($_FILES["fileToUpload"]["size"] > 5000000) {
        echo "Fichier trop volumineux (max 5 Mo).";
        $uploadOk = 0;
    }

    if (!in_array($fileType, ['jpg', 'jpeg', 'png', 'gif', 'pdf'])) {
        echo "Type de fichier non autorisé.";
        $uploadOk = 0;
    }

    if ($uploadOk == 1) {
        $newFileName = uniqid('file_', true) . '.' . $fileType;
        $newTargetFile = $targetDir . $newFileName;

        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $newTargetFile)) {
            echo "Fichier " . htmlspecialchars($fileName) . " uploadé avec succès.<br>";

            $id_messagerie = $_POST['id_messagerie'];

            // Connexion à la base de données "64"
            $conn = new mysqli("localhost", "root", "", "64");

            if ($conn->connect_error) {
                die("Échec connexion : " . $conn->connect_error);
            }

            $stmt = $conn->prepare("INSERT INTO uploads (file_name, file_path, file_size, file_type, id_messagerie)
                                    VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssisi", $newFileName, $newTargetFile, $_FILES["fileToUpload"]["size"], $fileType, $id_messagerie);

            if ($stmt->execute()) {
                echo "Infos du fichier enregistrées dans la base.";
            } else {
                echo "Erreur d'enregistrement : " . $stmt->error;
            }

            $stmt->close();
            $conn->close();
        } else {
            echo "Erreur de déplacement du fichier.";
        }
    } else {
        echo "Fichier non téléchargé.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Upload de fichier</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
        }
        .upload-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f8f5eb;
        }
        .upload-form {
            display: flex;
            flex-direction: column;
            gap: 12px;
            max-width: 300px;
            padding: 20px;
            background-color: #fff;
            border: 2px solid #ddd;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .upload-form input[type="file"],
        .upload-form button {
            padding: 10px;
            border-radius: 8px;
        }
        .upload-form button {
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
        }
        .upload-form button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="upload-container">
    <form action="uploads.php" method="post" enctype="multipart/form-data" class="upload-form">
        <label for="fileToUpload">Choisir un fichier :</label>
        <input type="file" name="fileToUpload" id="fileToUpload" required>
        <input type="hidden" name="id_messagerie" value="<?php echo htmlspecialchars($id_messagerie); ?>">
        <button type="submit" name="submit">Envoyer un fichier</button>
    </form>
</div>

</body>
</html>

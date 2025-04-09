<?php
// Démarre la session pour utiliser la variable session si nécessaire
session_start();

// Vérifie si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Paramètres de destination du fichier uploadé
    $targetDir = "uploads/"; // Dossier où les fichiers seront sauvegardés
    $fileName = basename($_FILES["fileToUpload"]["name"]);
    $targetFile = $targetDir . $fileName;
    $uploadOk = 1;
    $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    
    // Vérifie si le fichier n'a pas d'erreur de téléchargement
    if ($_FILES["fileToUpload"]["error"] !== UPLOAD_ERR_OK) {
        echo "Désolé, une erreur est survenue lors du téléchargement du fichier. Code d'erreur : " . $_FILES["fileToUpload"]["error"];
        $uploadOk = 0;
    }

    // Vérifie si le fichier est une image ou un autre type de fichier valide
    if (isset($_POST["submit"])) {
        $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
        if ($check !== false) {
            echo "Le fichier est une image - " . $check["mime"] . ".";
            $uploadOk = 1;
        } else {
            echo "Le fichier n'est pas une image.";
            $uploadOk = 0;
        }
    }

    // Vérifie la taille du fichier (limite de 5MB ici)
    if ($_FILES["fileToUpload"]["size"] > 5000000) {
        echo "Désolé, le fichier est trop volumineux.";
        $uploadOk = 0;
    }

    // Limiter les types de fichiers autorisés
    if ($fileType != "jpg" && $fileType != "png" && $fileType != "jpeg" && $fileType != "gif" && $fileType != "pdf") {
        echo "Désolé, seuls les fichiers JPG, JPEG, PNG, GIF et PDF sont autorisés.";
        $uploadOk = 0;
    }

    // Vérifie si $uploadOk est défini sur 0 en cas d'erreur
    if ($uploadOk == 0) {
        echo "Désolé, votre fichier n'a pas pu être téléchargé.";
    } else {
        // Renommage du fichier pour éviter des conflits
        $newFileName = uniqid('file_', true) . '.' . $fileType;
        $newTargetFile = $targetDir . $newFileName;

        // Déplace le fichier vers le dossier de destination
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $newTargetFile)) {
            echo "Le fichier " . htmlspecialchars($fileName) . " a été téléchargé avec succès.";

            // Si nécessaire, enregistrer les informations du fichier dans la base de données
            $id_messagerie = $_POST['id_messagerie']; // Le ID de la messagerie envoyée via le formulaire

            // Connexion à la base de données
            $conn = new mysqli("localhost", "username", "password", "dbname");

            if ($conn->connect_error) {
                die("Échec de la connexion : " . $conn->connect_error);
            }

            // Requête d'insertion dans la table uploads (assurez-vous que cette table existe)
            $sql = "INSERT INTO uploads (file_name, file_path, file_size, file_type, id_messagerie)
                    VALUES ('$newFileName', '$newTargetFile', '" . $_FILES["fileToUpload"]["size"] . "', '$fileType', '$id_messagerie')";

            if ($conn->query($sql) === TRUE) {
                echo "Les informations du fichier ont été enregistrées dans la base de données.";
            } else {
                echo "Erreur : " . $sql . "<br>" . $conn->error;
            }

            // Fermeture de la connexion
            $conn->close();
        } else {
            echo "Désolé, une erreur est survenue lors du téléchargement de votre fichier.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload de fichier</title>
    <style>
        /* Intégration du CSS pour le formulaire */
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        
        .upload-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f8f5eb;
        }

        form.upload-form {
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

        form.upload-form input[type="file"] {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: #fff;
            font-size: 14px;
            cursor: pointer;
        }

        form.upload-form button {
            padding: 10px 15px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 15px;
            transition: background-color 0.3s ease;
        }

        form.upload-form button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="upload-container">
    <form action="uploads.php" method="post" enctype="multipart/form-data" class="upload-form">
        <label for="fileToUpload">Choisir un fichier :</label>
        <input type="file" name="fileToUpload" id="fileToUpload" required>
        <input type="hidden" name="id_messagerie" value="<?php echo isset($id_messagerie) ? $id_messagerie : ''; ?>">
        <button type="submit" name="submit">Envoyer un fichier</button>
    </form>
</div>

</body>
</html>

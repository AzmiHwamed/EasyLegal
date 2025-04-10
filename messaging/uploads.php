<?php
// Démarre la session pour utiliser la variable session si nécessaire
session_start();

// Vérifie si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST'&& isset($_FILES['fileToUpload'])) {
    $fileTmpPath = $_FILES['fileToUpload']['tmp_name'];

    if (file_exists($fileTmpPath)) {
        // Read the file content
        $fileContent = file_get_contents($fileTmpPath);

        // Encode to base64
        $base64 = base64_encode($fileContent);

        // Optional: get the file type to construct a data URI
        $fileType = mime_content_type($fileTmpPath);
        $base64WithMime = 'data:' . $fileType . ';base64,' . $base64;

        echo "<p>Base64 Encoded:</p>";
        echo "<textarea rows='10' cols='80'>{$base64WithMime}</textarea>";
        echo "<img src='$base64WithMime' alt='Decoded Image' style='max-width:300px;'>";

    } else {
        echo "File upload failed.";
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

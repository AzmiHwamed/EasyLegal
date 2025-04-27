<?php
$conn = new mysqli("localhost", "root", "", "easylegal");

if ($conn->connect_error) {
    file_put_contents('error.txt', "Erreur de connexion : " . $conn->connect_error . PHP_EOL, FILE_APPEND);
    die("Erreur de connexion : " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contenu = $_POST['contenu'];
    $id_messagerie = (int)$_POST['id_messagerie'];
    $id_personne = (int)$_POST['id_personne'];

    $stmt = $conn->prepare("INSERT INTO message (contenu, created_at, id_messagerie, id_personne, isFile) VALUES (?, NOW(), ?, ?, 0)");
    if (!$stmt) {
        file_put_contents('error.txt', "Erreur de préparation : " . $conn->error . PHP_EOL, FILE_APPEND);
        die("Erreur de préparation : " . $conn->error);
    }

    $stmt->bind_param("sii", $contenu, $id_messagerie, $id_personne);
    if (!$stmt->execute()) {
        file_put_contents('error.txt', "Erreur d'exécution : " . $stmt->error . PHP_EOL, FILE_APPEND);
        die("Erreur d'exécution : " . $stmt->error);
    }
}
?>

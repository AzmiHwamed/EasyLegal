<?php
$conn = new mysqli("localhost", "root", "", "easylegal");

if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contenu = $_POST['contenu']; 
    $id_messagerie = (int)$_POST['id_messagerie'];
    $id_personne = (int)$_POST['id_personne'];

    $stmt = $conn->prepare("INSERT INTO message (contenu, created_at, id_messagerie, id_personne, isFile) VALUES (?, NOW(), ?, ?, 1)");
    $stmt->bind_param("sii", $contenu, $id_messagerie, $id_personne);
    $stmt->execute();
}
?>

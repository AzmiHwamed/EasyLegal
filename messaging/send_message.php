<?php
header('Content-Type: application/json'); // Important for CURL to understand JSON

$conn = new mysqli("localhost", "root", "", "easylegal");

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => "Erreur de connexion : " . $conn->connect_error]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['contenu'], $_POST['id_messagerie'], $_POST['id_personne'])) {
        http_response_code(400); // Bad Request
        echo json_encode(["success" => false, "error" => "Missing parameters"]);
        exit;
    }

    $contenu = $_POST['contenu'];
    $id_messagerie = (int)$_POST['id_messagerie'];
    $id_personne = (int)$_POST['id_personne'];

    $stmt = $conn->prepare("INSERT INTO message (contenu, created_at, id_messagerie, id_personne, isFile) VALUES (?, NOW(), ?, ?, 0)");

    if (!$stmt) {
        http_response_code(500);
        echo json_encode(["success" => false, "error" => "Erreur de préparation : " . $conn->error]);
        exit;
    }

    $stmt->bind_param("sii", $contenu, $id_messagerie, $id_personne);

    if ($stmt->execute()) {
        http_response_code(300); // Success
        echo json_encode(["success" => true, "message" => "Message envoyé avec succès."]);
    } else {
        http_response_code(500); // Server error
        echo json_encode(["success" => false, "error" => "Erreur lors de l'envoi du message."]);
    }
} else {
    http_response_code(405); // Method not allowed
    echo json_encode(["success" => false, "error" => "Méthode non autorisée"]);
}
?>

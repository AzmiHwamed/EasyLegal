<?php
session_start();
$conn = new mysqli("localhost", "root", "", "easylegal");

if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Vérification de l'utilisateur connecté
if (!isset($_SESSION['id']) || !isset($_SESSION['role'])) {
    die("Utilisateur non authentifié.");
}


if (isset($_GET['id_personne']) && isset($_GET['id_expert'])) {
    $id_personne = intval($_GET['id_personne']);
    $id_expert = intval($_GET['id_expert']);
    

    $stmt = $conn->prepare("SELECT id FROM messagerie WHERE id_personne = ? AND participant_expert_id = ?");
    $stmt->bind_param("ii", $id_personne, $id_expert);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $stmt = $conn->prepare("INSERT INTO messagerie (id_personne, participant_expert_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $id_personne, $id_expert);
        $stmt->execute();
    }

    

    // Récupérer l'ID de la messagerie nouvellement créée ou existante
    $stmt = $conn->prepare("SELECT id FROM messagerie WHERE id_personne = ? AND participant_expert_id = ?");
    $stmt->bind_param("ii", $id_personne, $id_expert);
    $stmt->execute();
    $result = $stmt->get_result();
    $messagerie = $result->fetch_assoc();

    // Rediriger vers la messagerie nouvellement créée ou existante
    header("Location: index.php?id=".$messagerie['id']);
    $stmt->close();
    $conn->close();
    exit();
} else {
    die("Paramètres manquants.");
}
?>
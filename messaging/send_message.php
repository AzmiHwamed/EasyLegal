<?php
session_start();
$conn = new mysqli("localhost", "root", "", "easylegal");

if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Vérification de la session de l'utilisateur
if (!isset($_SESSION['id'])) {
    die("Utilisateur non authentifié");
}

$user_id = $_SESSION['id'];
$messagerie_id = isset($_POST['id_messagerie']) ? (int)$_POST['id_messagerie'] : 1;
$message = isset($_POST['message']) ? trim($_POST['message']) : "";

// Si le message est vide, on arrête l'exécution
if (empty($message)) {
    die("Le message est vide");
}

// Insertion du message dans la base de données
$sql = "INSERT INTO message (contenu, created_at, id_messagerie, id_personne) 
        VALUES (?, NOW(), ?, ?)"; 

$stmt = $conn->prepare($sql);
$stmt->bind_param("sii", $message, $messagerie_id, $user_id);

if ($stmt->execute()) {
    echo "Message envoyé avec succès!";
} else {
    echo "Erreur lors de l'envoi du message: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>

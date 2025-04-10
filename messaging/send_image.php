<?php
session_start();
$conn = new mysqli("localhost", "root", "", "easylegal");

if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Vérification de la session de l'utilisateur
if (!isset($_SESSION['id']) || !isset($_SESSION['role'])) {
    die("Utilisateur non authentifié");
}

$user_id = $_SESSION['id'];
$role = $_SESSION['role'];
$messagerie_id = isset($_POST['id_messagerie']) ? (int)$_POST['id_messagerie'] : 0;
$message = isset($_POST['message']) ? $_POST['message'] : "";

if (empty($message)) {
    die("Le message est vide");
}
$stmt = $conn->prepare("SELECT id_personne, participant_expert_id FROM messagerie WHERE id = ?");
$stmt->bind_param("i", $messagerie_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Discussion non trouvée.");
}

$row = $result->fetch_assoc();
$id_personne = $row['id_personne'];
$participant_expert_id = $row['participant_expert_id'];

// Vérification de l'accès selon le rôle
if ($role === 'user') {
    if ($id_personne != $user_id) {
        die("Vous n'avez pas accès à cette discussion.");
    }
} elseif ($role === 'expert') {
    if ($participant_expert_id != $user_id) {
        die("Vous n'avez pas accès à cette discussion.");
    }
} else {
    die("Rôle utilisateur non reconnu.");
}

// Insertion du message dans la base de données
$sql = "INSERT INTO message (contenu, created_at, id_messagerie, id_personne , isImage) 
        VALUES (?, NOW(), ?, ? , true)"; 

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

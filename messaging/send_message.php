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
$role = $_SESSION['role']; // Récupération du rôle de l'utilisateur (user ou expert)
$messagerie_id = isset($_POST['id_messagerie']) ? (int)$_POST['id_messagerie'] : 1;
$message = isset($_POST['message']) ? trim($_POST['message']) : "";

// Si le message est vide, on arrête l'exécution
if (empty($message)) {
    die("Le message est vide");
}

// Vérification du rôle de l'utilisateur pour contrôler l'envoi de message
if ($role === 'user') {
    // Logic spécifique pour les utilisateurs (par exemple, empêcher l'envoi dans certaines messageries)
    // Exemple : limiter l'envoi de messages dans certaines discussions aux experts
    $stmt = $conn->prepare("SELECT id_personne FROM messagerie WHERE id = ?");
    $stmt->bind_param("i", $messagerie_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // Vérifier si la messagerie est accessible par l'utilisateur
    if ($row['id_personne'] != $user_id) {
        die("Vous n'avez pas accès à cette discussion.");
    }
} elseif ($role === 'expert') {
    // Logic spécifique pour les experts
    // Exemple : Les experts peuvent envoyer des messages dans toutes les discussions
    // Ou peut-être donner des privilèges spéciaux pour certaines messageries
} else {
    die("Rôle utilisateur non reconnu.");
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

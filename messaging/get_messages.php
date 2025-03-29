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

$user_id = $_SESSION['id'];
$role = $_SESSION['role']; // 'user' ou 'expert'

// Récupération de l'ID de la messagerie
$messagerie_id = isset($_GET['id_messagerie']) ? (int)$_GET['id_messagerie'] : 5;

// Construire la requête SQL en fonction du rôle
if ($role === 'expert') {
    // Si c'est un expert, il voit tous les messages de la discussion
    $sql = "SELECT m.contenu, m.created_at, p.nom 
            FROM message m 
            JOIN personne p ON m.id_personne = p.id
            WHERE m.id_messagerie = ? 
            ORDER BY m.created_at ASC";
} else {
    // Si c'est un utilisateur, il voit ses messages et ceux des experts uniquement
    $sql = "SELECT m.contenu, m.created_at, p.nom 
            FROM message m 
            JOIN personne p ON m.id_personne = p.id
            WHERE m.id_messagerie = ? AND (m.id_personne = ? OR p.role = 'expert')
            ORDER BY m.created_at ASC";
}

$stmt = $conn->prepare($sql);

if ($role === 'expert') {
    $stmt->bind_param("i", $messagerie_id);
} else {
    $stmt->bind_param("ii", $messagerie_id, $user_id);
}

$stmt->execute();
$result = $stmt->get_result();

// Affichage des messages
while ($row = $result->fetch_assoc()) {
    echo "<div class='message'>";
    echo "<strong>" . htmlspecialchars($row['nom']) . " :</strong> ";
    echo htmlspecialchars($row['contenu']) . "<br>";
    echo "<em>Envoyé le " . $row['created_at'] . "</em>";
    echo "</div>";
}

$stmt->close();
$conn->close();
?>

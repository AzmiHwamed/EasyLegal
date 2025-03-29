<?php
session_start();
$conn = new mysqli("localhost", "root", "", "easylegal");

if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Récupération de l'ID de la messagerie
$messagerie_id = isset($_GET['messagerie_id']) ? $_GET['messagerie_id'] : 5;

// Requête pour récupérer les messages
$sql = "SELECT m.contenu, m.createdAt, p.nom 
        FROM message m 
        JOIN personne p ON m.id_personne = p.id
        WHERE m.id_messagerie = ? 
        ORDER BY m.createdAt ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $messagerie_id); 
$stmt->execute();
$result = $stmt->get_result();

// Affichage des messages
while ($row = $result->fetch_assoc()) {
    echo "<div class='message'>";
    echo "<strong>" . htmlspecialchars($row['nom']) . " :</strong> ";
    echo htmlspecialchars($row['contenu']) . "<br>";
    echo "<em>Envoyé le " . $row['createdAt'] . "</em>";
    echo "</div>";
}

$stmt->close();
$conn->close();
?>

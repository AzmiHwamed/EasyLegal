<?php
session_start();
$conn = new mysqli("localhost", "root", "", "easylegal");

if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

if (!isset($_GET['id_messagerie'])) {
    die("Missing id_messagerie");
}

$id_messagerie = (int)$_GET['id_messagerie'];

$stmt = $conn->prepare("SELECT message.*, personne.nom FROM message LEFT JOIN personne ON message.id_personne = personne.id WHERE id_messagerie = ? ORDER BY message.created_at ASC");
$stmt->bind_param("i", $id_messagerie);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $formattedDate = date("Y/n/j H:i:s", strtotime($row['created_at']));

    if ($row['isFile']) {
        echo "<div class='msg " . ($row['id_personne'] === $_SESSION['id'] ? 'my' : '') . "'><span class='name'>".$row['nom']."</span><a href='" . $row['contenu'] . "' download='file'>Download File</a> <small>" . $formattedDate . "</small></div>";
    } else {
       
        echo "<div class='msg " . ($row['id_personne'] === $_SESSION['id'] ? 'my' : '') . "'><span class='name'>".$row['nom']."</span>" . htmlspecialchars($row['contenu']) . " <small>" . $formattedDate. "</small></div>";
    }
}
?>

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

$stmt = $conn->prepare("SELECT * FROM message WHERE id_messagerie = ? ORDER BY created_at ASC");
$stmt->bind_param("i", $id_messagerie);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    if ($row['isFile']) {
        // it's a file → create a download link
        echo "<div class='msg " . ($row['id_personne'] === $_SESSION['id'] ? 'my' : '') . "'><a href='" . $row['contenu'] . "' download='file'>Download File</a> <small>(" . $row['created_at'] . ")</small></div>";
    } else {
       
        echo "<div class='msg " . ($row['id_personne'] === $_SESSION['id'] ? 'my' : '') . "'>" . htmlspecialchars($row['contenu']) . " <small>(" . $row['created_at'] . ")</small></div>";
    }
}
?>

<?php
$conn = new mysqli("localhost", "root", "", "easylegal");

$query = "SELECT m.message, u.nom, m.created_at FROM messages m
          JOIN personne u ON m.user_id = u.id ORDER BY m.created_at ASC";

$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {
    echo "
    <div class='message'><strong>{$row['nom']}:</strong> {$row['message']} <span style='float:right;color:gray;font-size:0.8em;'>{$row['created_at']}</span></div>";
}

$conn->close();
?>

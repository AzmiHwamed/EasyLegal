<?php
session_start();
$conn = new mysqli("localhost", "root", "", "easylegal");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["message"])) {
    // if (!isset($_SESSION["id"])) {
    //     echo "User not authenticated";
    //     exit;
    // }

    $message = trim($_POST["message"]);
    if (empty($message)) {
        echo "Message is empty";
        exit;
    }

    $user_id = 1;
    //$_SESSION["id"];
    $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

    $stmt = $conn->prepare("INSERT INTO messages (user_id, message) VALUES (?, ?)");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("is", $user_id, $message);
    
    if ($stmt->execute()) {
        echo "Message sent successfully";
    } else {
        echo "Error sending message: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

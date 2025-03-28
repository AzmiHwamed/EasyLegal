<?php
session_start();
$conn = new mysqli("localhost", "root", "", "easylegal"); 

if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = rand(1000, 9999);
}

$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Group Chat</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; }
        #chat-box { width: 80%; height: 300px; overflow-y: scroll; border: 1px solid #ccc; padding: 10px; margin: 20px auto; }
        #message { width: 70%; padding: 8px; }
        #sendBtn { padding: 8px; cursor: pointer; }
        .message { padding: 5px; margin: 5px; border-radius: 5px; background: #f1f1f1; text-align: left; }
    </style>
</head>
<body>

    <h2>Simple Group Chat</h2>
    
    <div id="chat-box"></div>

    <input type="text" id="message" placeholder="Type a message...">
    <button id="sendBtn">Send</button>

    <script>
        document.getElementById("sendBtn").addEventListener("click", sendMessage);

        function sendMessage() {
            let message = document.getElementById("message").value.trim();

            if (message === "") {
                alert("Message cannot be empty!");
                return;
            }

            let xhr = new XMLHttpRequest();
            //TODO: specifiée le iditifiant de la disscussion
            xhr.open("POST", "send_message.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.send(`message=${encodeURIComponent(message)}`);

            document.getElementById("message").value = "";
        }

        function fetchMessages() {
            let xhr = new XMLHttpRequest();
            //TODO: specifiée le iditifiant de la disscussion
            xhr.open("GET", "get_messages.php", true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    document.getElementById("chat-box").innerHTML = xhr.responseText;
                }
            };
            xhr.send();

        }

        setInterval(fetchMessages, 200);
    </script>

</body>
</html>

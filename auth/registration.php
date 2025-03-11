<?php
// Always start this first
session_start();

if ( ! empty( $_POST ) ) {
    if ( isset( $_POST['username'] ) && isset( $_POST['password'] ) ) {
        // Getting submitted user data from database
        $con = new mysqli($db_host, $db_user, $db_pass, $db_name);
        $stmt = $con->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param('s', $_POST['username']);
        $stmt->execute();
        $result = $stmt->get_result();
    	$user = $result->fetch_object();
    		
    	// Verify user password and set $_SESSION
    	if ( password_verify( $_POST['password'], $user->password ) ) {
    		$_SESSION['user_id'] = $user->ID;
    	}
    }
}
?> 
































<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
</head>
<body>
    <h2>Registration</h2>
    <form class="" action="" method="post" authcomplete="off">
        <label for="name">Name:</label>
        <input type="text" name="name" id="name" required value=""><br>
        <label for="username">username:</label>
        <input type="text" name="username" id="username" required value=""><br>
        <label for="password">password:</label>
        <input type="text" name="password" id="password" required value=""><br>

        <label for="phone">Téléphone:</label>
        <input type="number" name="uphone" id="phone" required value=""><br>

        <button type="submit">Register</button>

        
</form>
<br>
<a href="login.php">Login</a>
</body>
</html>
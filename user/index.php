Les users
<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['user_id'] == null){
    header('Location: ../auth/login.php');
    exit();
}
if($_SESSION['type'] != 'user'){
    header('Location: ../'.$_SESSION['type'].'/index.php');
}

?>
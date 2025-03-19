Les experts 
<?php
session_start();
if(!isset($_SESSION['id']) || $_SESSION['id'] == null){
    header('Location: ../auth/login.php');
    exit();
}
if($_SESSION['type'] != 'expert'){
    header('Location: ../'.$_SESSION['type'].'/index.php');
}

?>
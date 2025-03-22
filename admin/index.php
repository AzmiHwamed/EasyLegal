<?php
session_start();
include('../validateur.php');isAdmin();

if(!isset($_SESSION['user_id']) || $_SESSION['user_id'] == null){
    header('Location: ../auth/login.php');
    exit();
}
if($_SESSION['type'] != 'admin'){
    header('Location: ../'.$_SESSION['type'].'/index.php');
}
?>


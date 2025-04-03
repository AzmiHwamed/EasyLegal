<?php
session_start();
include('../validateur.php');isAdmin();
header('Location: ./AdminNav/index.php');
?>


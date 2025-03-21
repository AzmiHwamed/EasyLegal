<?php
function isAuthentiacted(){
    
    session_start(); // Démarrer la session si elle n'est pas encore démarrée
    
    if (!isset($_SESSION['id'])) {
    header("Location: ../auth/login.php");//
    exit();
    }
    
    
}

function isAdmin(){
    
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        header("Location: ../auth/login.php");//
        exit();
    }     
    
}

function isExpert(){
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'expert') {
        header("Location: ../auth/login.php");//
        exit();
    } 
}


function isUser(){
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
        header("Location: ../auth/login.php");//
        exit();
    }
}
?>

<?php
function isAuthentiacted(){
    session_start(); 
    echo $_SESSION['id'];
    if (!isset($_SESSION['id'])) {
    header("Location: ../auth/login.php");//
    exit();
    }
    echo $_SESSION['id'];

    
    
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

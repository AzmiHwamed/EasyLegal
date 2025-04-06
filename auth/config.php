<?php
session_start();

// Configuration de la base de données
$db_host = 'localhost:4306';
$db_user = 'root';
$db_pass = ''; 
$db_name = 'easylegal'; 

// Connexion à la base de données
$conn = new mysqli($servername, $username, $password, $dbname);



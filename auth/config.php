<?php
session_start();

// Configuration de la base de données
$db_host = 'localhost';
$db_user = 'root'; // Remplacez par votre utilisateur
$db_pass = ''; // Remplacez par votre mot de passe
$db_name = 'easylegal'; // Remplacez par le nom de votre base de données

// Connexion à la base de données
$conn = new mysqli($servername, $username, $password, $dbname);



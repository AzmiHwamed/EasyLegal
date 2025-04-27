<?php
session_start();
include('../dbconfig/index.php');

// Connexion à la base de données
$mysqli = new mysqli("localhost", "root", "", "easylegal");
if ($mysqli->connect_error) {
    die("Connexion échouée : " . $mysqli->connect_error);
}
if (isset($_POST['id_forum']) && isset($_POST['like'])) {
    $id_forum = (int) $_POST['id_forum'];
    $id_personne = $_SESSION['id'];

    // Vérifier si l'utilisateur a déjà liké
    $check = $mysqli->prepare("SELECT id FROM aime WHERE id_forum = ? AND id_personne = ?");
    $check->bind_param("ii", $id_forum, $id_personne);
    $check->execute();
    $check_result = $check->get_result();

    if ($check_result->num_rows === 0) {
        // Ajouter un like
        $insert = $mysqli->prepare("INSERT INTO aime (id_forum, id_personne) VALUES (?, ?)");
        $insert->bind_param("ii", $id_forum, $id_personne);
        $insert->execute();
        $insert->close();
    } else {
        // Retirer un like
        $delete = $mysqli->prepare("DELETE FROM aime WHERE id_forum = ? AND id_personne = ?");
        $delete->bind_param("ii", $id_forum, $id_personne);
        $delete->execute();
        $delete->close();
    }

    // Retourner le nouveau nombre de likes
    $likes = $mysqli->prepare("SELECT COUNT(*) FROM aime WHERE id_forum = ?");
    $likes->bind_param("i", $id_forum);
    $likes->execute();
    $likes_result = $likes->get_result();
    $likes_count = $likes_result->fetch_row()[0];

    echo json_encode(["success" => true, "likes" => $likes_count]);
    exit;
}
?>
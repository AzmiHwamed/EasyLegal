<?php
session_start();
$conn = new mysqli("localhost", "root", "", "easylegal");

if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Vérification de l'utilisateur connecté
if (!isset($_SESSION['id']) || !isset($_SESSION['role'])) {
    die("Utilisateur non authentifié.");
}

$user_id = $_SESSION['id'];
$role = $_SESSION['role']; // 'user' ou 'expert'

// Récupération de l'ID de la messagerie
$messagerie_id = isset($_GET['id_messagerie']) ? (int)$_GET['id_messagerie'] : 0;

// Vérification de l'existence de la discussion
$stmt = $conn->prepare("SELECT id_personne, participant_expert_id FROM messagerie WHERE id = ?");
$stmt->bind_param("i", $messagerie_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Discussion introuvable.");
}

$row = $result->fetch_assoc();
$id_personne = $row['id_personne'];
$participant_expert_id = $row['participant_expert_id'];

// Vérification des autorisations d'accès
if (($role === 'user' && $id_personne != $user_id) || 
    ($role === 'expert' && $participant_expert_id != $user_id)) {
    die("Vous n'avez pas accès à cette discussion.");
}

// Construction de la requête SQL pour récupérer les messages
$sql = "SELECT m.contenu, m.created_at, p.nom, p.role  , m.isImage
        FROM message m 
        JOIN personne p ON m.id_personne = p.id
        WHERE m.id_messagerie = ?
        ORDER BY m.created_at ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $messagerie_id);
$stmt->execute();
$result = $stmt->get_result();

// Affichage des messages
while ($row = $result->fetch_assoc()) {
    if($row['isImage']){
        $nom = htmlspecialchars($row['nom']);
        $contenu = htmlspecialchars($row['contenu']);
        $created_at = $row['created_at'];
        $roleMessage = $row['role'] === 'expert' ? '(Expert)' : '(Utilisateur)';
        echo "<div class='message'>";
    echo "<strong>$nom $roleMessage :</strong><br>";
    echo "<script> alert('".$contenu."')</script>";
    echo "<img src='".$contenu . "' alt='Image' style='max-width:300px;'><br>";
    echo "</div>";
        
    $date = DateTime::createFromFormat('Y-m-d H:i:s.u', $created_at);

    if ($date) {
        echo $date->format('Y/n/j G:i:s'); 
    } else {
        echo "Invalid date format.";
    }    
    }else{
        $nom = htmlspecialchars($row['nom']);
    $contenu = htmlspecialchars($row['contenu']);
    $created_at = $row['created_at'];
    $roleMessage = $row['role'] === 'expert' ? '(Expert)' : '(Utilisateur)';
    
    echo "<div class='message'>";
    echo "<strong>$nom $roleMessage :</strong> $contenu<br>";
    $date = DateTime::createFromFormat('Y-m-d H:i:s.u', $created_at);

    if ($date) {
        echo $date->format('Y/n/j G:i:s'); 
    } else {
        echo "Invalid date format.";
    }    
     

    echo "</div>";
    }
}

$stmt->close();
$conn->close();
?>

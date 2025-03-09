<?php  
include('../dbconfig/index.php');
$sql = "SELECT * FROM textjuridique ORDER BY id DESC LIMIT 3";
$result = $conn->query($sql);
if($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
        $id = $row['id'];
        $date = $row['Date'];
        $contenu = $row['Contenu'];
        echo "date : $date \n contenu : $contenu \n ";
    }
} 
?>


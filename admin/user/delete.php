<?php
ob_start();
include '../Base/db.php';
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM audioguides WHERE id='{$id}'";
    if ($conn->query($sql) === TRUE) {
        header("Location: ../Audioguides");
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}
?>

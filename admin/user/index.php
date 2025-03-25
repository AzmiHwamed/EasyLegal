<?php 
ob_start();
include '../../dbconfig/index.php'; 
?>
<!DOCTYPE html>
<html>
<head>
    <title>User management</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
    <?php include "../Nav/index.php" ?>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <a href="create.php" class="btn btn-primary mb-3">Add New Audioguide</a>
                <a href="./Audio" class="btn btn-primary mb-3">Add Audios</a>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Telephone</th>
                            <th>Role</th>
                            <th>Email</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM personne";
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                echo "<tr>
                                    
                                    <td>{$row['nom']}</td>
                                    <td>{$row['telephone']}</td>
                                    <td>{$row['role']}</td>
                                    <td>{$row['Email']}</td>
                                    <td>
                                        <a href='update.php?id={$row['id']}' class='btn btn-warning'>Edit</a>
                                        <a href='delete.php?id={$row['id']}' class='btn btn-danger'>Delete</a>
                                    </td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7' class='text-center'>No Records Found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
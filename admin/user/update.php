<?php 
ob_start();
include '../Base/db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Update Audioguide</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
<?php include "../Nav/index.php" ?>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <h2>Update Audioguide</h2>
                <?php
                if (isset($_GET['id'])) {
                    $id = $_GET['id'];
                    $sql = "SELECT * FROM audioguides WHERE id='{$id}'";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        ?>
                        <form action="" method="POST">
                            <div class="form-group">
                                <label>ID</label>
                                <input type="text" name="id" class="form-control" value="<?php echo $row['id']; ?>" readonly>
                            </div>
                            <div class="form-group">
                                <label>Title (Arabic)</label>
                                <input type="text" name="TitleAr" class="form-control" value="<?php echo $row['TitleAr']; ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Title (English)</label>
                                <input type="text" name="TitleEn" class="form-control" value="<?php echo $row['TitleEn']; ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Description (Arabic)</label>
                                <textarea name="DescAr" class="form-control" required><?php echo $row['DescAr']; ?></textarea>
                            </div>
                            <div class="form-group">
                                <label>Description (English)</label>
                                <textarea name="DescEn" class="form-control" required><?php echo $row['DescEn']; ?></textarea>
                            </div>
                            <div class="form-group">
                                <label>Image URL</label>
                                <input type="text" name="imageUrl" class="form-control" value="<?php echo $row['imageUrl']; ?>" required>
                            </div>
                            <button type="submit" name="submit" class="btn btn-primary">Update</button>
                        </form>
                        <?php
                        if (isset($_POST['submit'])) {
                            $id = $_POST['id'];
                            $TitleAr = $_POST['TitleAr'];
                            $TitleEn = $_POST['TitleEn'];
                            $DescAr = $_POST['DescAr'];
                            $DescEn = $_POST['DescEn'];
                            $imageUrl = $_POST['imageUrl'];
                            $sql = "UPDATE audioguides SET TitleAr='$TitleAr', TitleEn='$TitleEn', DescAr='$DescAr', DescEn='$DescEn', imageUrl='$imageUrl' WHERE id='$id'";
                            if ($conn->query($sql) === TRUE) {
                                echo "<p class='alert alert-success mt-3'>Record updated successfully</p>";
                                header("Location: ../Audioguides");
                            } else {
                                echo "<p class='alert alert-danger mt-3'>Error updating record: " . $conn->error . "</p>";
                            }
                        }
                    } else {
                        echo "<p class='alert alert-danger'>Record not found</p>";
                    }
                }
                ?>
            </div>
        </div>
    </div>
</body>
</html>

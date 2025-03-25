<?php
ob_start();
include '../Base/db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Audioguide</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
<?php include "../Nav/index.php" ?>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <h2>Add New Audioguide</h2>
                <form action="" method="POST">
                    <div class="form-group">
                        <label>ID</label>
                        <input type="text" name="id" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Title (Arabic)</label>
                        <input type="text" name="TitleAr" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Title (English)</label>
                        <input type="text" name="TitleEn" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Description (Arabic)</label>
                        <textarea name="DescAr" class="form-control" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Description (English)</label>
                        <textarea name="DescEn" class="form-control" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Image URL</label>
                        <input type="text" name="imageUrl" class="form-control" required>
                    </div>
                    <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                </form>
                <?php
                if (isset($_POST['submit'])) {
                    $id = $_POST['id'];
                    $TitleAr = $_POST['TitleAr'];
                    $TitleEn = $_POST['TitleEn'];
                    $DescAr = $_POST['DescAr'];
                    $DescEn = $_POST['DescEn'];
                    $imageUrl = $_POST['imageUrl'];
                    $sql = "INSERT INTO audioguides (id, TitleAr, TitleEn, DescAr, DescEn, imageUrl) VALUES ('$id', '$TitleAr', '$TitleEn', '$DescAr', '$DescEn', '$imageUrl')";
                    if ($conn->query($sql) === TRUE) {
                        echo "<p class='alert alert-success mt-3'>New record created successfully</p>";
                        header("Location: ../Audioguides");
                    } else {
                        echo "<p class='alert alert-danger mt-3'>Error: " . $sql . "<br>" . $conn->error . "</p>";
                    }
                }
                ?>
            </div>
        </div>
    </div>
</body>
</html>

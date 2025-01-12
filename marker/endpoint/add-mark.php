<?php
include("../conn/conn.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['mark_name'], $_POST['mark_long'], $_POST['mark_lat'], $_POST['mark_info'], $_POST['mark_status'], $_POST['mark_petFriendly'])) {
        $markName = $_POST['mark_name'];
        $markLong = $_POST['mark_long'];
        $markLat = $_POST['mark_lat'];
        $markInfo = $_POST['mark_info'];
        $markStatus = $_POST['mark_status'];
        $markPetFriendly = $_POST['mark_petFriendly'];

        // File upload logic
        $markImage = '';
        $targetDirectory = "../uploads/";

        if (isset($_FILES['image_upload']) && $_FILES['image_upload']['error'] == 0) {
            $image = $_FILES['image_upload'];
            $imageName = basename($image['name']);
            $targetFile = $targetDirectory . $imageName;
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
            $allowedTypes = array('jpg', 'jpeg', 'png', 'gif');

            if (in_array($imageFileType, $allowedTypes)) {
                if (move_uploaded_file($image['tmp_name'], $targetFile)) {
                    $markImage = $imageName; // Store the image name
                    echo "<script>alert('Image uploaded successfully: $imageName');</script>";
                } else {
                    echo "<script>alert('Error uploading image.');</script>";
                }
            } else {
                echo "<script>alert('Invalid image file type. Only JPG, JPEG, PNG & GIF files are allowed.');</script>";
            }
        } else {
            echo "<script>alert('No image uploaded or there was an error.');</script>";
        }

        try {
            $stmt = $conn->prepare("INSERT INTO tbl_mark (mark_name, mark_long, mark_lat, mark_info, mark_status, mark_image, mark_petFriendly) VALUES (:mark_name, :mark_long, :mark_lat, :mark_info, :mark_status, :mark_image, :mark_petFriendly)");
            $stmt->bindParam(":mark_name", $markName, PDO::PARAM_STR);
            $stmt->bindParam(":mark_long", $markLong, PDO::PARAM_STR);
            $stmt->bindParam(":mark_lat", $markLat, PDO::PARAM_STR);
            $stmt->bindParam(":mark_info", $markInfo, PDO::PARAM_STR);
            $stmt->bindParam(":mark_status", $markStatus, PDO::PARAM_STR);
            $stmt->bindParam(":mark_image", $markImage, PDO::PARAM_STR);
            $stmt->bindParam(":mark_petFriendly", $markPetFriendly, PDO::PARAM_STR);
            $stmt->execute();
            header("Location: http://localhostmarker/");
            exit();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "<script>
                alert('Please fill in all fields!');
                window.location.href = 'http://localhostmarker/map.php';
              </script>";
    }
}
?>

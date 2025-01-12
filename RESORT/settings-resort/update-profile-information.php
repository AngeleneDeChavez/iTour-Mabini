<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "capstonedb";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch resort owner ID from session
$resort_owner_id = $_SESSION['user_id'];

// Fetch resort information based on owner ID
$sql = "SELECT * FROM resorts WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $resort_owner_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if resort data is found
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    echo "No resort found with the provided ID.";
    exit;
}

// Update resort information if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lastname = $_POST['lastname'];
    $firstname = $_POST['firstname'];
    $middlename = $_POST['middlename'];
    $resort_name = $_POST['resort_name'];
    $address = $_POST['address'];
    $business_permit_no = $_POST['business_permit_no'];
    $contact_no = $_POST['contact_no'];
    $email = $_POST['email'];

    // Profile Picture Upload Logic
    if (!empty($_FILES['profile_picture']['name'])) {
        $target_dir = "uploads/";
        $profile_picture = basename($_FILES["profile_picture"]["name"]);
        $target_file = $target_dir . $profile_picture;

        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
            // Update the path to include the directory
            $profile_picture_path = $target_file;
        } else {
            echo "<div class='alert alert-danger'>Error uploading profile picture.</div>";
            exit();
        }
    } else {
        // Retain the previous picture if no new file is uploaded
        $profile_picture_path = $row['profile_picture'];
    }

    // Update SQL statement
    $update_sql = "UPDATE resorts 
                   SET lastname = ?, firstname = ?, middlename = ?, resort_name = ?, address = ?, 
                       business_permit_no = ?, contact_no = ?, email = ?, profile_picture = ? 
                   WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("sssssssssi", $lastname, $firstname, $middlename, $resort_name, $address, $business_permit_no, $contact_no, $email, $profile_picture_path, $resort_owner_id);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Information updated successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error updating record: " . $conn->error . "</div>";
    }
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/cb01d7a304.js" crossorigin="anonymous"></script>
    <title>Profile Information</title>
    <link rel="stylesheet" href="../samejs-css/logout.css">
    <link rel="stylesheet" href="../samejs-css/sucessmodal.css">
    <link rel="stylesheet" href="update-profile-information.css">
    <link rel="shortcut icon" href="assets/img/favicon.png" type="image/x-icon">
    <style>
        /* Profile Picture CSS */
        .profile-picture-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
        }
        .profile-picture {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            cursor: pointer;
            border: 2px solid #ddd;
        }
        /* Modal CSS */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            width: 300px;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
    </style>
</head>

<body>
<?php include 'settings.php'; ?>

<div class="main-content">
    <div class="main-content-edit">
        <div class="welcome-section text-center">
            <h2>Edit Account Information</h2>

            <!-- Profile Picture Section -->
            <div class="profile-picture-container">
                <img src="<?php echo isset($row['profile_picture']) ? $row['profile_picture'] : 'default-avatar.png'; ?>" alt="Profile Picture" class="profile-picture" id="profilePicture">
            </div>

            <!-- Form Section -->
            <form action="update-profile-information.php" method="POST" id="forms" enctype="multipart/form-data">
                <!-- Owner Information -->

                <div class="row" id="basic-info">
                     <!-- Profile Picture Input -->
                <label for="profile_picture">Upload Profile Picture</label>
                <input type="file" name="profile_picture" accept="image/*" id="fileInput">
                </div>
                <div class="row" id="basic-info">
                    <h3>Owner Information</h3>
                    <label for="lastname">Last Name</label>
                    <input type="text" name="lastname" placeholder="Lastname" value="<?php echo htmlspecialchars($row['lastname']); ?>" required>
                    <label for="firstname">First Name</label>
                    <input type="text" name="firstname" placeholder="Firstname" value="<?php echo htmlspecialchars($row['firstname']); ?>" required>
                    <label for="middlename">Middle Name</label>
                    <input type="text" name="middlename" placeholder="Middlename" value="<?php echo htmlspecialchars($row['middlename']); ?>">
                </div>

                <!-- Resort Information -->
                <div class="row" id="basic-info">
                    <h3>Resort Information</h3>
                    <label for="resort_name">Resort Name</label>
                    <input type="text" name="resort_name" placeholder="Resort Name" value="<?php echo htmlspecialchars($row['resort_name']); ?>" required>
                    <label for="address">Resort Address</label>
                    <input type="text" name="address" placeholder="Resort Address" value="<?php echo htmlspecialchars($row['address']); ?>" required>
                </div>

                <!-- Contact Information -->
                <div class="row" id="basic-info">
                    <h3>Contact Information</h3>
                    <label for="contact_no">Contact Number</label>
                    <input type="text" name="contact_no" placeholder="Contact Number" value="<?php echo htmlspecialchars($row['contact_no']); ?>" required>
                    <label for="email">Email Address</label>
                    <input type="text" name="email" placeholder="Email Address" value="<?php echo htmlspecialchars($row['email']); ?>" required>
                </div>

                <!-- Business Permit Section -->
                <div class="row" id="basic-info">
                    <h3>Resort Registration</h3>
                    <label for="business_permit_no">Business Permit Number</label>
                    <input type="text" name="business_permit_no" placeholder="Business Permit Number" value="<?php echo htmlspecialchars($row['business_permit_no']); ?>" required>
                </div>


                <!-- Submit Button -->
                <div class="submit-button">
                    <button type="submit">Update Information</button>
                </div>
            </form>
        </div>
    </div>
    </div>
</div>

<script>
// JavaScript for Modal and Image Preview

// Open profile modal
document.getElementById("profilePicture").onclick = function() {
    document.getElementById("profileModal").style.display = "flex";
}

// Close modals
function closeModal() {
    document.getElementById("profileModal").style.display = "none";
}
function closePreviewModal() {
    document.getElementById("previewModal").style.display = "none";
}

// Image Preview
function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById("previewImage").src = e.target.result;
            document.getElementById("previewModal").style.display = "flex";
        };
        reader.readAsDataURL(file);
    }
}

// Confirm Image
function confirmImage() {
    const previewImage = document.getElementById("previewImage").src;
    document.getElementById("profilePicture").src = previewImage;
    closePreviewModal();
}
</script>

</body>
</html>

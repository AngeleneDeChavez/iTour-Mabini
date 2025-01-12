<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['employee_id'])) {
    header("Location: login.php");
    exit();
}

// Database connection
include '../../connection/db_connection.php';

// Fetch employee ID from session
$employee_id = $_SESSION['employee_id'];

// Fetch employee information based on employee ID
$sql = "SELECT * FROM employeesdb WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if employee data is found
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    echo "No employee found with the provided ID.";
    exit();
}

// Update employee information if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lastname = $_POST['lastname'];
    $firstname = $_POST['firstname'];
    $middlename = $_POST['middlename'];
    $email_address = $_POST['email_address'];
    $contact_number = $_POST['contact_number'];
    $address = $_POST['address'];
    $position = $_POST['position'];
    $department = $_POST['department'];
    $office_address = $_POST['office_address'];

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
    $update_sql = "UPDATE employeesdb 
                   SET lastname = ?, firstname = ?, middlename = ?, email_address = ?, contact_number = ?, address = ?, position = ?, department = ?, office_address = ?, profile_picture = ? 
                   WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ssssssssssi", $lastname, $firstname, $middlename, $email_address, $contact_number, $address, $position, $department, $office_address, $profile_picture_path, $employee_id);

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
    <title>Update Employee Information</title>
    <link rel="stylesheet" href="settings.css">
    <link rel="stylesheet" href="../samejs-css/logout.css">
    <link rel="stylesheet" href="../samejs-css/successmodal.css">
    <link rel="stylesheet" href="update-profile-information.css">
    <script src="https://kit.fontawesome.com/cb01d7a304.js" crossorigin="anonymous"></script>
    <link rel="shortcut icon" href="../../assets/img/favicon.png" type="image/x-icon">
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

<div class="sidebar">
<button id="closeBtn" class="close-btn">&times;</button>
    <img src="../../images/logo.png" alt="">
    <ul>
        <li><i class="fas fa-clipboard-list"></i><a href="home-settings.php">Home</a></li>
        <li><i class="fas fa-tachometer-alt"></i><a href="update-profile-information.php">Profile</a></li>
        <li><i class="fas fa-key"></i><a href="password.php">Password & Security</a></li>
        <li><i class="fas fa-globe"></i><a href="language.php">Language</a></li>
    </ul>
</div>

    <div class="main-content">
        
    <button id="menuToggle" class="menu-toggle">☰</button>
    <div class="main-content-edit">
        <div class="welcome-section text-center">
            <h2>Edit Employee Information</h2>

            <!-- Profile Picture Section -->
            <div class="profile-picture-container">
                <img src="<?php echo isset($row['profile_picture']) ? $row['profile_picture'] : 'default-avatar.png'; ?>" alt="Profile Picture" class="profile-picture" id="profilePicture">
            </div>

            <!-- Form Section -->
            <form action="update-profile-information.php" method="POST" id="forms" enctype="multipart/form-data">
                <!-- Profile Picture Input -->
                <div class="row" id="basic-info">
                    <label for="profile_picture">Upload Profile Picture</label>
                    <input type="file" name="profile_picture" accept="image/*" id="fileInput">
                </div>

                <!-- Employee Information -->
                <div class="row" id="basic-info">
                    <h3>Employee Information</h3>
                    <label for="lastname">Last Name</label>
                    <input type="text" name="lastname" placeholder="Lastname" value="<?php echo htmlspecialchars($row['lastname']); ?>" required>
                    <label for="firstname">First Name</label>
                    <input type="text" name="firstname" placeholder="Firstname" value="<?php echo htmlspecialchars($row['firstname']); ?>" required>
                    <label for="middlename">Middle Name</label>
                    <input type="text" name="middlename" placeholder="Middlename" value="<?php echo htmlspecialchars($row['middlename']); ?>">
                </div>

                <!-- Contact Information -->
                <div class="row" id="basic-info">
                    <h3>Contact Information</h3>
                    <label for="email_address">Email Address</label>
                    <input type="text" name="email_address" placeholder="Email Address" value="<?php echo htmlspecialchars($row['email_address']); ?>" required>
                    <label for="contact_number">Contact Number</label>
                    <input type="text" name="contact_number" placeholder="Contact Number" value="<?php echo htmlspecialchars($row['contact_number']); ?>" required>
                    <label for="address">Address</label>
                    <input type="text" name="address" placeholder="Address" value="<?php echo htmlspecialchars($row['address']); ?>" required>
                </div>

                <!-- Position and Department -->
                <div class="row" id="basic-info">
                    <h3>Position & Department</h3>
                    <label for="position">Position</label>
                    <input type="text" name="position" placeholder="Position" value="<?php echo htmlspecialchars($row['position']); ?>" required>
                    <label for="department">Department</label>
                    <input type="text" name="department" placeholder="Department" value="<?php echo htmlspecialchars($row['department']); ?>" required>
                    <label for="office_address">Office Address</label>
                    <input type="text" name="office_address" placeholder="Office Address" value="<?php echo htmlspecialchars($row['office_address']); ?>" required>
                </div>

                <!-- Submit Button -->
                <div class="submit-button">
                    <button type="submit">Update Information</button>
                </div>
            </form>
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
<script>
    const menuToggle = document.getElementById('menuToggle');
    const closeBtn = document.getElementById('closeBtn');
    const sidebar = document.querySelector('.sidebar');

    // Function to toggle sidebar visibility
    const toggleSidebar = () => {
        sidebar.classList.toggle('active');
    };

    // Event listener for the menu button
    menuToggle.addEventListener('click', toggleSidebar);

    // Event listener for the close button
    closeBtn.addEventListener('click', toggleSidebar);
</script>

</body>
</html>

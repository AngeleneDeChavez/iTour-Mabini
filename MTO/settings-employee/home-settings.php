<?php
session_start();
include '../../connection/db_connection.php';

// Check if the user is logged in by verifying the session
if (!isset($_SESSION['employee_id'])) {
    // If not logged in, redirect to the login page
    header("Location: ../../ index.html");
    exit();
}

// Fetch the user_id from the session
$employee_id = $_SESSION['employee_id'];

// Fetch the username and profile picture from the database using the user_id
$stmt = $conn->prepare("SELECT username, profile_picture FROM employeesdb WHERE id = ?");
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$stmt->bind_result($username, $profile_picture);
$stmt->fetch();
$stmt->close();

// If the username is found, store it in the session (optional)
if ($username) {
    $_SESSION['username'] = $username;
} else {
    // Handle the case where the username is not found
    $username = "Unknown User";
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/cb01d7a304.js" crossorigin="anonymous"></script>
    <title>Profile Information</title>
    <link rel="stylesheet" href="update-profile-information.css">
    <link rel="stylesheet" href="settings.css">
    <link rel="shortcut icon" href="../../assets/img/favicon.png" type="image/x-icon">
    <style>
        .profile-picture-account {
            width: 200px; /* Adjust size as needed */
            height: 200px; /* Adjust size as needed */
            border-radius: 50%;
            object-fit: cover;
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
        <div class="welcome-section">
            <?php if (!empty($profile_picture)): ?>
                    <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture" class="profile-picture-account">
                <?php else: ?>
                    <img src="default-avatar.png" alt="Default Profile Picture" class="profile-picture"> <!-- Default image if none exists -->
                <?php endif; ?>
            <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
            <p>Manage information, privacy, and security of your account</p>
            <div class="settings-boxes">
                <div class="box"></div>
                <div class="box"></div>
            </div>
        </div>
    </div>

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

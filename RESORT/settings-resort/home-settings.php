<?php
session_start();
include '../../connection/db_connection.php';

// Check if the user is logged in by verifying the session
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to the login page
    header("Location: ../../ index.html");
    exit();
}

// Fetch the user_id from the session
$user_id = $_SESSION['user_id'];

// Fetch the username and profile picture from the database using the user_id
$stmt = $conn->prepare("SELECT username, profile_picture FROM resorts WHERE id = ?");
$stmt->bind_param("i", $user_id);
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
    <link rel="shortcut icon" href="assets/img/favicon.png" type="image/x-icon">
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
    
<?php include 'settings.php'; ?>

    <div class="main-content">
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
</body>
</html>

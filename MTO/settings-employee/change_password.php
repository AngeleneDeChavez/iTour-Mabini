<?php
session_start();
include 'db_connection.php';  // Include your database connection file here

// Assuming $_SESSION['id'] contains the user ID
$userId = $_SESSION['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    // Check if new passwords match
    if ($newPassword !== $confirmPassword) {
        echo "New passwords do not match!";
        exit;
    }

    // Fetch current password from the database
    $query = "SELECT password FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($storedPassword);
    $stmt->fetch();
    $stmt->close();

    // Verify current password
    if (!password_verify($currentPassword, $storedPassword)) {
        echo "Current password is incorrect!";
        exit;
    }

    // Hash the new password
    $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    // Update password in the database
    $updateQuery = "UPDATE users SET password = ? WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("si", $hashedNewPassword, $userId);

    if ($stmt->execute()) {
        echo "Password changed successfully!";
    } else {
        echo "Error updating password!";
    }

    $stmt->close();
    $conn->close();
}
?>

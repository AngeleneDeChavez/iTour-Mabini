<link rel="shortcut icon" href="assets/img/favicon.png" type="image/x-icon">

<?php
session_start();
include '../connection/db_connection.php';

// Check if the 'id' is passed in the URL
if (isset($_GET['id'])) {
    $resort_id = $_GET['id'];

    // Update the account status to 'inactive'
    $sql = "UPDATE resorts SET status = 'inactive' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $resort_id);

    if ($stmt->execute()) {
        // Optionally, you can add a notification or log for deactivation
        $_SESSION['message'] = "Account has been deactivated successfully.";
        header("Location: management.php");  // Redirect back to management page
    } else {
        $_SESSION['error'] = "Error deactivating account: " . $conn->error;
        header("Location: management.php");
    }
    $stmt->close();
} else {
    // If no ID is passed, redirect to management page
    header("Location: management.php");
}

$conn->close();
?>

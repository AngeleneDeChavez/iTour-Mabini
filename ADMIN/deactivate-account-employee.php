<?php
session_start();
include '../connection/db_connection.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $employee_id = intval($_GET['id']); // Sanitize input

    // Prepare and execute SQL statement
    $sql = "UPDATE employeesdb SET status = 'inactive' WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $employee_id);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Employee account has been deactivated successfully.";
        } else {
            $_SESSION['error'] = "Error deactivating employee account: " . $conn->error;
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "SQL error: " . $conn->error;
    }
} else {
    $_SESSION['error'] = "Invalid employee ID.";
}

$conn->close();
header("Location: tourism-officer.php");
exit;
?>

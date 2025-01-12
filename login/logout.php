<?php
// Start the session
session_name('employee_session'); // For employee logout
session_start();

// Logout logic for employee
if (isset($_SESSION['employee_id'])) {
    unset($_SESSION['employee_id']); // Unset the employee session variable
    session_destroy(); // Destroy the employee session
    header("Location: login-tourism-officer/login.php"); // Redirect to employee login page
    exit();
}

// Start the session for resort
session_name('resort_session'); // For resort logout
session_start();

// Logout logic for resort
if (isset($_SESSION['user_id'])) {
    unset($_SESSION['user_id']); // Unset the resort session variable
    session_destroy(); // Destroy the resort session
    header("Location: login-resort-staff/login.php"); // Redirect to resort login page
    exit();
}

// Default redirect if no session is set
header("Location: ../index.html"); // General login page if neither session exists
exit();
?>

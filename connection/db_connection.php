<?php
// Database connection parameters
$servername = "localhost"; // Host name
$username = "root"; // Database username
$password = ""; // Database password
$dbname = "capstonedb"; // Database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<?php
session_start();
include '../connection/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employeeID = $_POST['EmployeeID'] ?? null;
    $status = $_POST['status'] ?? null;

    if ($employeeID && $status) {
        $employeeID = $conn->real_escape_string($employeeID);
        $status = $conn->real_escape_string($status);

        $sql = "UPDATE employeesdb SET status = '$status' WHERE id = '$employeeID'";

        if ($conn->query($sql) === TRUE) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $conn->error]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid input.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
}
$conn->close();
?>



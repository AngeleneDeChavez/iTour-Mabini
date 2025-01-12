<?php
include '../connection/db_connection.php';
$response = ['success' => false, 'error' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resortId = isset($_POST['resortId']) ? $conn->real_escape_string($_POST['resortId']) : null;
    $status = isset($_POST['status']) ? $conn->real_escape_string($_POST['status']) : null;

    // Debug: Log inputs
    error_log("Resort ID: $resortId, Status: $status");

    if ($resortId && $status) {
        $sql = "UPDATE resorts SET status = '$status' WHERE id = '$resortId'";

        if ($conn->query($sql)) {
            $response['success'] = true;
        } else {
            $response['error'] = "Database error: " . $conn->error;
        }
    } else {
        $response['error'] = "Invalid input data.";
    }
} else {
    $response['error'] = "Invalid request method.";
}

echo json_encode($response);
$conn->close();
?>

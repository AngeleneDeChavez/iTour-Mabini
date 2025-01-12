<?php
session_start();
require '../connection/db_connection.php';



if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];  // Fetch the user ID from the session

// Double-check user session to ensure correct user messages
$query = "SELECT id, sender_id, message_content, label FROM messages WHERE recipient_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

echo json_encode($messages);
?>

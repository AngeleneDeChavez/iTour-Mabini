<?php
session_start();
include '../connection/db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['employee_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

$employee_id = $_SESSION['employee_id'];

$sql = "SELECT 
            r.id AS resort_id, 
            r.resort_name, 
            m.message_content
        FROM resorts r
        LEFT JOIN messages m ON r.id = m.recipient_id
        WHERE m.sender_id = ?
        ORDER BY r.resort_name, m.created_at DESC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['error' => 'SQL Error: ' . $conn->error]);
    exit;
}

$stmt->bind_param("i", $employee_id);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $resort_id = $row['resort_id'];
    if (!isset($messages[$resort_id])) {
        $messages[$resort_id] = [
            'resort_name' => $row['resort_name'],
            'messages' => []
        ];
    }
    if (!empty($row['message_content'])) {
        $messages[$resort_id]['messages'][] = [
            'message_content' => $row['message_content'],
            'created_at' => $row['created_at']
        ];
    }
}

$stmt->close();
$conn->close();

echo json_encode($messages ?: ['error' => 'No messages found']);

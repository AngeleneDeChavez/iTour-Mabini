<?php
session_start();
include '../connection/db_connection.php'; // Replace with your DB connection file

// Get the logged-in user ID
$employee_id = $_SESSION['employee_id']; // Replace with your session variable

// Fetch unique recipients with their resort names
$queryRecipients = "
    SELECT DISTINCT 
        IF(sender_id = $employee_id, recipient_id, sender_id) AS recipient_id,
        (SELECT resort_name FROM resorts WHERE id = IF(sender_id = $employee_id, recipient_id, sender_id)) AS recipient_name
    FROM messages
    WHERE sender_id = $employee_id OR recipient_id = $employee_id";
$recipientsResult = $conn->query($queryRecipients);

// Fetch messages for a selected recipient
$selectedRecipientId = isset($_GET['recipient_id']) ? intval($_GET['recipient_id']) : 0;
$queryMessages = "
    SELECT * 
    FROM messages
    WHERE (sender_id = $employee_id AND recipient_id = $selectedRecipientId)
       OR (sender_id = $selectedRecipientId AND recipient_id = $employee_id)
    ORDER BY id ASC";
$messagesResult = $conn->query($queryMessages);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message System</title>
    <link rel="stylesheet" href="inbox.css">
    <link rel="stylesheet" href="../samejs-css/logout.css">
    <script src="https://kit.fontawesome.com/cb01d7a304.js" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="shortcut icon" href="../assets/img/favicon.png" type="image/x-icon">
    <style>
        .recipient { padding: 10px; margin-bottom: 5px; border: 1px solid #ddd; border-radius: 5px; cursor: pointer; }
        .recipient:hover, .recipient.active { background: #ddd; }
        .container {
             height: 90vh; /* Fixed height, doesn't change with content */
    display: flex;
    width: 100%;
    gap: 20px;
}
        .recipient {
    width: 280px; /* Fixed width */
   
    overflow-y: auto; /* Makes it scrollable if content exceeds the fixed height */
    background-color: #f5f5f5;
    border: 1px solid #ddd;
    border-radius: 5px;
    padding: 10px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.recipient h3 {
    margin: 0 0 10px 0;
    color: #333;
}

.recipient a {
    display: block;
    padding: 10px;
    margin-bottom: 5px;
    background-color: #ffffff;
    color: #333;
    text-decoration: none;
    font-size: 14px;
    font-weight: bold;
    border: 1px solid #ddd;
    border-radius: 5px;
    transition: background-color 0.3s, transform 0.2s;
}

.recipient a:hover {
    background-color: #e2e8f0;
    transform: scale(1.02);
}

.recipient a.active {
    background-color: #2c7dfa;
    color: white;
    border-color: #2c7dfa;
}

        .message { margin-bottom: 10px; padding: 10px; border-radius: 5px; }
        .message.sent { background: #d1f7c4; text-align: right; }
        .message.received { background: #f7d1d1; text-align: left; }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <img src="../images/logo.png" alt="">
        <ul>
            <li><i class="fas fa-clipboard-list"></i><a href="registration.php">Registration</a></li>
            <li><i class="fas fa-tachometer-alt"></i><a href="dashboard.php">Dashboard</a></li>
            <li><i class="fas fa-clipboard-list"></i><a href="list-of-activities-mto.php">List of Activities</a></li>
            <li><i class="fas fa-file-alt"></i><a href="reportmto.php">Reports</a></li>
            <li class="dropdown-btn"><i class="fas fa-envelope"></i>Message</li>
                <div class="dropdown-container">
                    <a href="message.php"><i class="fa-solid fa-paper-plane"></i> Send Message</a>
                    <a href="inbox.php"><i class="fa-solid fa-message"></i> Inbox</a>
                </div>
            <li><i class="fa-solid fa-arrow-right-from-bracket"></i><a id="logoutBtn">Log out</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="container">
            <!-- Left Side: List of Resorts -->
            <div class="recipient">
            <h3>Recipients</h3>
        <?php while ($recipient = $recipientsResult->fetch_assoc()): ?>
            <a href="?recipient_id=<?= $recipient['recipient_id'] ?>" class="recipient <?= $recipient['recipient_id'] == $selectedRecipientId ? 'active' : '' ?>">
                <?= htmlspecialchars($recipient['recipient_name']) ?>
            </a>
        <?php endwhile; ?>
            </div>

            <!-- Right Side: Messages Content -->
            <div class="message-content">
            <h3>Messages</h3>
                <?php if ($selectedRecipientId): ?>
                    <?php while ($message = $messagesResult->fetch_assoc()): ?>
                        <div class="message <?= $message['sender_id'] == $employee_id ? 'sent' : 'received' ?>">
                            <?= htmlspecialchars($message['message_content']) ?>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>Select a recipient to view messages.</p>
                <?php endif; ?>
                    </div>
        </div>
    </div>

   

    <script src="../samejs-css/logout.js"></script>
    <script src="../samejs-css/sidebarjs.js"></script>
</body>
</html>

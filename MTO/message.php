<?php
session_start();
include '../connection/db_connection.php';

if (!isset($_SESSION['employee_id'])) {
    die("You must be logged in to access this page.");
}

$user_id = $_SESSION['employee_id']; // Assuming this is the resort ID

// Handle message sending logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sender_id = $_SESSION['employee_id']; // Get the logged-in user's ID
    $recipient_id = $_POST['recipient']; // Recipient ID from dropdown
    $message_content = trim($_POST['message']); // Make sure 'message' matches the 'name' attribute in the form
    $label = $_POST['label']; // Make sure 'label' matches the 'name' attribute in the form
    $schedule = $_POST['schedule']; // 'send_now' or 'send_later'
    $scheduled_date = null;

    if ($schedule == 'send_later') {
        // Sanitize and convert the selected date to a valid format
        $scheduled_date = $_POST['scheduled_date']; // Format: YYYY-MM-DD
    }

    // Check if message content is empty
    if (empty($message_content)) {
        echo "Message content cannot be empty.";
        exit;
    }

    // Insert the message into the database
    if ($recipient_id == 'all') {
        // Fetch all resort IDs
        $resorts = $conn->query("SELECT id FROM resorts");
        while ($row = $resorts->fetch_assoc()) {
            $sql = "INSERT INTO messages (sender_id, recipient_id, message_content, label, scheduled_date) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iisss", $sender_id, $row['id'], $message_content, $label, $scheduled_date);
            $stmt->execute();
        }
        $_SESSION['success_message'] = "Message sent to all resorts successfully!";
    } else {
        // Send to a specific recipient
        $sql = "INSERT INTO messages (sender_id, recipient_id, message_content, label, scheduled_date) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iisss", $sender_id, $recipient_id, $message_content, $label, $scheduled_date);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Message sent successfully!";
        } else {
            $_SESSION['error_message'] = "Error: " . $stmt->error;
        }
    }

    // Redirect back to the same page to display the success message
    header('Location: message.php');
    exit;
}

// Fetch the list of resorts for the dropdown
$resorts = $conn->query("SELECT id, resort_name FROM resorts");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/cb01d7a304.js" crossorigin="anonymous"></script>
    <title>TOURISM MESSAGE</title>
    <link rel="stylesheet" href="message-mto.css">
    <link rel="stylesheet" href="../samejs-css/logout.css">
    <link rel="shortcut icon" href="../assets/img/favicon.png" type="image/x-icon">
    <style>
        .popup-message {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #28a745; /* Success Green */
            color: white;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            font-size: 16px;
            z-index: 1000;
            display: none; /* Hidden by default */
        }
        
        .popup-error {
            background-color: #dc3545; /* Error Red */
        }
        
        .popup-message.show {
            display: block;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
    <button id="closeBtn" class="close-btn">&times;</button>
        <img src="../images/logo.png" alt="">
        <ul>
            <li><i class="fas fa-clipboard-list"></i><a href="registration.php">Registration</a></li>
            <li><i class="fas fa-tachometer-alt"></i><a href="dashboard.php">Dashboard</a></li>
            <li><i class="fas fa-clipboard-list"></i><a href="list-of-activities-mto.php">List of Activities</a></li>
            <li><i class="fas fa-file-alt"></i><a href="reportmto.php">Reports</a></li>
            <li class="dropdown-btn"><i class="fas fa-envelope"></i>Message 
                </li>
                    <div class="dropdown-container">
                        <a href="message.php"><i class="fa-solid fa-paper-plane"></i> Send Message</a>
                        <a href="inbox.php"><i class="fa-solid fa-message"></i> Inbox</a>
                    </div>
            <li>
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                <a id="logoutBtn">Log out</a>
                <div id="logoutModal" class="modal">
                <div class="modal-content">
                    <p>Are you sure you want to log out?</p>
                    <button class="confirm-btn" id="confirmLogout">Yes</button>
                    <button class="cancel-btn" id="cancelLogout">No</button>
                </div>
            </div>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">

    <div class="topbar">
        <button id="menuToggle" class="menu-toggle">☰</button>
            <div class="search-box">
            </div>
            <div class="icons"> 
                <a href="settings-employee/home-settings.php" target="_blank"><i class="fas fa-gear"></a></i>
            </div>
        </div>

        <p id="tab">Message</p>

       <!-- Pop-up message box -->
       <?php if (isset($_SESSION['success_message'])): ?>
            <div id="popup" class="popup-message show">
                <p><?= $_SESSION['success_message']; ?></p>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php elseif (isset($_SESSION['error_message'])): ?>
            <div id="popup" class="popup-message popup-error show">
                <p><?= $_SESSION['error_message']; ?></p>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <div class="mail-container">
            <h2>Compose Mail</h2>
            <form action="message.php" method="post">
                <label for="recipients" id="labels1">To:</label>
                                <select name="recipient" id="recipients" required>
                                    <option value="" disabled selected>Select recipient</option>
                                    <option value="all">All Resorts</option>
                                    <?php
                                    if ($resorts->num_rows > 0) {
                                        while ($row = $resorts->fetch_assoc()) {
                                            echo "<option value='" . $row['id'] . "'>" . $row['resort_name'] . "</option>";
                                        }
                                    }
                                    ?>
                                </select>

                <label for="schedule">Schedule</label>
                <select id="schedule" name="schedule" class="form-control" required>
                    <option value="send_now">Send Now</option>
                    <option value="send_later">Send Later</option>
                </select>

                <div id="calendar-container" style="display:none;">
                    <label for="scheduled_date">Select Date:</label>
                    <input type="date" id="scheduled_date" name="scheduled_date">
                </div>

                <label for="label">Label as:</label>
                <select id="label" name="label" class="form-control">
                    <option value="Announcement">Announcement</option>
                    <option value="Reminders">Reminders</option>
                </select>

                <label for="message">Compose Message:</label>
                
                <textarea id="message" name="message" class="form-control compose-area" placeholder="Compose your message here..."></textarea>

                <div class="attachments">
                    <button type="submit" class="send-btn">Send</button>
                </div>
            </form>
        </div>

    </div>

    <script>
        // Show calendar when "Send Later" is selected
        document.getElementById('schedule').addEventListener('change', function() {
            var calendarContainer = document.getElementById('calendar-container');
            if (this.value == 'send_later') {
                calendarContainer.style.display = 'block';
            } else {
                calendarContainer.style.display = 'none';
            }
        });
    </script>

    <script src="../samejs-css/logout.js"></script>
    <script src="../samejs-css/sidebarjs.js"></script>

    <script>
    const menuToggle = document.getElementById('menuToggle');
    const closeBtn = document.getElementById('closeBtn');
    const sidebar = document.querySelector('.sidebar');

    // Function to toggle sidebar visibility
    const toggleSidebar = () => {
        sidebar.classList.toggle('active');
    };

    // Event listener for the menu button
    menuToggle.addEventListener('click', toggleSidebar);

    // Event listener for the close button
    closeBtn.addEventListener('click', toggleSidebar);
</script>

<script> 
setTimeout(function() {
            const popup = document.getElementById('popup');
            if (popup) {
                popup.classList.remove('show');
            }
        }, 5000);</script>
    

</body>
</html>

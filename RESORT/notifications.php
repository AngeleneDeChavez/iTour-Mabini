<?php
include '../connection/db_connection.php';

session_start(); // Start session to access user_id

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to access this page.");
}

$user_id = $_SESSION['user_id']; // Assuming this is the resort ID

$profile_picture_url = 'settings-resort/uploads/';

$stmt = $conn->prepare("SELECT username, profile_picture FROM resorts WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $profile_picture);
$stmt->fetch();
$stmt->close();

// If the username is found, store it in the session (optional)
if ($username) {
    $_SESSION['username'] = $username;
} else {
    // Handle the case where the username is not found
    $username = "Unknown User";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message System</title>
    <link rel="stylesheet" href="notifications.css">
    <link rel="stylesheet" href="../samejs-css/logout.css">
    <script src="https://kit.fontawesome.com/cb01d7a304.js" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="shortcut icon" href="assets/img/favicon.png" type="image/x-icon">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
    <button id="closeBtn" class="close-btn">&times;</button>
        <img src="../images/logo.png" alt="">
        <ul>
            <li class="active"><i class="fas fa-clipboard-list"></i><a href="index.php">Registration</a></li>
            <li><i class="fas fa-tachometer-alt"></i><a href="list-of-activities.php">List of Activities</a></li>
            <li><i class="fa fa-map-marker" aria-hidden="true"></i><a href="../marker/index.php">Map</a></li>
            <li><i class="fas fa-file-alt"></i><a href="reports.php">Reports</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="topbar">
        <button id="menuToggle" class="menu-toggle">☰</button>
            <div></div>
            <div class="icons">
                <a href="notifications.php"><i class="fas fa-bell" id="notif"></i></a>
                <a href="settings-resort/home-settings.php" target="_blank"><i class="fas fa-gear"></i></a>
                <div class="dropdown">
                    <a href="#" class="dropbtn"><i class="fas fa-circle-user"></i></a>
                    <div class="dropdown-content">
                        <div class="user-icon">
                            <i class="fas fa-circle-user" id="hello"></i>
                        </div>
                        <a id="logoutBtn"><i class="fas fa-sign-out-alt"></i>Log out</a>
                        <div id="logoutModal" class="modal">
                            <div class="modal-content">
                                <p>Are you sure you want to log out?</p>
                                <div class="button-container">
                                    <button class="confirm-btn" id="confirmLogout">Yes</button>
                                    <button class="cancel-btn" id="cancelLogout">No</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <h2 id="tab">Notifications</h2>
        
        <!-- Tabs for filtering notifications -->
        <div class="tabs">
            <button class="tab-button active" onclick="filterNotifications('all')">All</button>
            <button class="tab-button" onclick="filterNotifications('announcement')">Announcements</button>
            <button class="tab-button" onclick="filterNotifications('reminders')">Reminders</button>
        </div>

        <div class="notification-list" id="notificationList"></div>
        <div class="message-view" id="messageView" style="display:none;">
            <button class="back-button" onclick="showNotificationList()"><i class="fa-solid fa-arrow-left"></i></button>
            <div id="messageContent"></div>
        </div>

        <script>
            let allMessages = [];
            let openedMessages = new Set(); // Track opened messages

            // Load opened messages from localStorage
            function loadOpenedMessages() {
                const storedMessages = localStorage.getItem('openedMessages');
                if (storedMessages) {
                    openedMessages = new Set(JSON.parse(storedMessages)); // Initialize openedMessages from stored data
                }
            }

            async function fetchNotifications() {
                const response = await fetch('get_messages.php');
                const messages = await response.json();
                if (messages.error) {
                    document.getElementById('notificationList').innerText = messages.error;
                    return;
                }
                allMessages = messages; // Store all messages
                displayNotifications(allMessages); // Display all initially
            }

            // Add the filterNotifications function
function filterNotifications(type) {
    let filteredMessages = [];
    
    if (type === 'all') {
        filteredMessages = allMessages; // Show all messages
    } else {
        filteredMessages = allMessages.filter(msg => msg.label.toLowerCase() === type);
    }
    
    displayNotifications(filteredMessages); // Display the filtered messages
}

// Adjust the existing displayNotifications function to reset message states
function displayNotifications(messages) {
    const list = document.getElementById('notificationList');
    list.innerHTML = '';
    messages.forEach(msg => {
        const div = document.createElement('div');
        div.className = 'notification';
        
        // Create a container for the checkbox and message
        const messageContainer = document.createElement('div');
        messageContainer.className = 'message-container'; // New class for styling

        // Create the checkbox
        const checkbox = document.createElement('input');
        checkbox.type = 'checkbox'; // Set checkbox type
        checkbox.className = 'notification-checkbox'; // Optional class for styling
        checkbox.id = `msg-${msg.id}`; // Assuming each message has a unique ID

        // Prevent checkbox clicks from expanding the message
        checkbox.onclick = (event) => {
            event.stopPropagation(); // Stop the event from bubbling up
        };

        // Create a label for the checkbox
        const label = document.createElement('label');
        label.htmlFor = checkbox.id; // Link label to checkbox

        // Set the inner HTML of the message
        // Make the message bold if it has not been opened yet
        label.innerHTML = `<strong>${msg.label}:</strong> <span class="${openedMessages.has(msg.id) ? '' : 'unread'}">${msg.message_content}</span>`;

        // Append checkbox and label to the message container
        messageContainer.appendChild(checkbox);
        messageContainer.appendChild(label);

        // Expand the message on click of the message container
        messageContainer.onclick = () => {
            openedMessages.add(msg.id); // Mark message as opened
            localStorage.setItem('openedMessages', JSON.stringify(Array.from(openedMessages))); // Save to localStorage
            expandMessage(div, msg.message_content);
        };
        div.appendChild(messageContainer);
        list.appendChild(div);
    });
}

            function expandMessage(element, content) {
                document.getElementById('notificationList').style.display = 'none';
                document.getElementById('messageView').style.display = 'block';
                document.getElementById('messageContent').innerHTML = `<p>${content}</p>`;
            }

            function showNotificationList() {
                document.getElementById('notificationList').style.display = 'block';
                document.getElementById('messageView').style.display = 'none';
                displayNotifications(allMessages); // Refresh the list to apply styles
            }

            // CSS for unread messages
            const style = document.createElement('style');
            style.innerHTML = `
                .unread {
                    font-weight: bold; /* Bold style for unread messages */
                }
            `;
            document.head.appendChild(style);

            // Load opened messages when the script runs
            loadOpenedMessages();
            fetchNotifications();


        </script>

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

        <script src="../samejs-css/logout.js"></script>
        <script src="../samejs-css/sidebarjs.js"></script>

    </div>
</body>
</html>

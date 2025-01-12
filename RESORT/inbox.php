<?php
include '../connection/db_connection.php';

// Fetch the list of resorts with their latest messages
$resorts = $conn->query("SELECT r.id, r.resort_name, m.message_content 
                         FROM resorts r 
                         LEFT JOIN messages m ON r.id = m.recipient_id 
                         GROUP BY r.id 
                         ORDER BY m.id DESC");

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
    <link rel="shortcut icon" href="assets/img/favicon.png" type="image/x-icon">
    <style>
        /* Add any additional styles you want */
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <img src="../images/logo.png" alt="">
        <ul>
            <li class="active"><i class="fas fa-clipboard-list"></i><a href="index.php">Registration</a></li>
            <li><i class="fas fa-tachometer-alt"></i><a href="list-of-activities.php">List of Activities</a></li>
            <li><i class="fa fa-map-marker" aria-hidden="true"></i></i><a href="../marker/index.php">Map</a></li>
            <li><i class="fa-solid fa-message" aria-hidden="true"></i></i><a href="inbox.php">Inbox</a></li>
            <li><i class="fas fa-file-alt"></i><a href="reports.php">Reports</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="topbar">
            <div></div>
                <div class="icons">
                    <a href="notifications.php"><i class="fas fa-bell" id="notif"></i></a>
                    <a href="settings-resort/home-settings.php" target="_blank"><i class="fas fa-gear"></i></a>
                    <div class="dropdown">
                        <a href="#" class="dropbtn"><img src="<?= htmlspecialchars($profile_picture_url); ?>" alt="Profile Picture" class="profile-pic"></a>
                        </a>
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
    <div class="main-content">
        <div class="container">
            <div class="recipient">
                <input type="text" placeholder="Search" class="search-bar">
                <ul class="resort-list">
                    <?php
                    while ($resort = $resorts->fetch_assoc()) {
                        echo "<li data-resort-id='" . $resort['id'] . "'>
                                <div class='resort-name'>" . $resort['resort_name'] . "</div>
                              </li>";
                    }
                    ?>
                    
                </ul>
            </div>
            <div class="message-content">
                <div class="message-header">
                    <h2 id="message-sender">Select a resort to view messages</h2>
                </div>
                <div id="message-body">
                    <p id="sent-to-you-text"><i class="fa-solid fa-user-tie"></i> Tourism office sent you:</p>
                    <div id="message-content">
                        <!-- All messages for the selected resort will be dynamically loaded here -->
                    </div>
                </div>
            </div>

        </div>
    </div>


    <script>
    $(document).ready(function () {
        $('.resort-list li').click(function () {
            var resortId = $(this).data('resort-id');

            // AJAX request to get all messages for the selected resort
            $.ajax({
                url: 'get_messages.php',
                type: 'GET',
                data: { resort_id: resortId },
                success: function (response) {
                    var messages = JSON.parse(response);

                    // Update the message header to show the selected resort name
                    var resortName = $('.resort-list li[data-resort-id="' + resortId + '"] .resort-name').text();
                    $('#message-sender').text("Messages from " + resortName);

                    // Clear previous messages and display the new ones
                    var messageContentHtml = '';
                    if (messages.length > 0) {
                        messages.forEach(function (message) {
                            messageContentHtml += '<p>' + message + '</p>';
                        });
                    } else {
                        messageContentHtml = '<p>No messages found for this resort.</p>';
                    }
                    $('#message-content').html(messageContentHtml);
                }
            });
        });
    });
</script>


    <script src="../samejs-css/logout.js"></script>
    <script src="../samejs-css/sidebarjs.js"></script>

</body>
</html>

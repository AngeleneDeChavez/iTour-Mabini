<?php
include '../connection/db_connection.php';

session_start(); // Start session to access user_id

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to access this page.");
}

$user_id = $_SESSION['user_id']; // Assuming this is the resort ID

$message = ""; // Initialize message variable

// Handling the addition of a new activity
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_activity'])) {
    $activity_name = $conn->real_escape_string($_POST['activity_name']);
    $price = $conn->real_escape_string($_POST['price']);

    $stmt = $conn->prepare("INSERT INTO activities (activity_name, price, resort_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $activity_name, $price, $user_id);

    if ($stmt->execute()) {
        $message = "Activity added successfully!";
    } else {
        $message = "Error adding activity: " . $stmt->error;
    }
    $stmt->close();
}

// Handling the deletion of an activity
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_activity'])) {
    $activity_id = $conn->real_escape_string($_POST['activity_id']);

    $stmt = $conn->prepare("DELETE FROM activities WHERE id = ? AND resort_id = ?");
    $stmt->bind_param("ii", $activity_id, $user_id);

    if ($stmt->execute()) {
        $message = "Activity deleted successfully!";
    } else {
        $message = "Error deleting activity: " . $stmt->error;
    }
    $stmt->close();
}

$sql = "SELECT id, activity_name, price FROM activities WHERE resort_id='$user_id'";
$result = $conn->query($sql);

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/cb01d7a304.js" crossorigin="anonymous"></script>
    <title>RESORT LIST OF ACTIVITIES</title>
    <link rel="stylesheet" href="list-of-activities.css">
    <link rel="stylesheet" href="../samejs-css/logout.css">
    <link rel="shortcut icon" href="assets/img/favicon.png" type="image/x-icon">
    <style>
        .activity-card {
    border: 1px solid #ccc;
    padding: 10px;
    margin: 5px 0;
    border-radius: 5px;
    background-color: #f9f9f9;
}

.activity-card h3 {
    margin: 0;
    color: #333;
}
    </style>
</head>
<body>

<div class="sidebar">
    <img src="../images/logo.png" alt="Logo">
    <button class="close-btn" id="closeBtn">&times;</button>
    <ul>
        <li class="active"><i class="fas fa-clipboard-list"></i><a href="index.php">Registration</a></li>
        <li><i class="fas fa-tachometer-alt"></i><a href="list-of-activities.php">List of Activities</a></li>
        <li><i class="fa fa-map-marker" aria-hidden="true"></i><a href="../marker/index.php">Map</a></li>
        <li><i class="fas fa-file-alt"></i><a href="reports.php">Reports</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="topbar">

        <!-- Add this button inside your .topbar -->
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
                            <button class="confirm-btn" id="confirmLogout">Yes</button>
                            <button class="cancel-btn" id="cancelLogout">No</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <p id="tab">List of Activities</p>

    <div class="table-container">
        <table class="activities-table">
            <thead>
                <tr>
                    <th>Activities</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['activity_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['price']) . "</td>";
                        echo "<td>
                            <button class='add-btn'>Add</button>
                            <form action='' method='POST' style='display:inline-block;' onsubmit='return confirmDelete();'>
                                <input type='hidden' name='activity_id' value='" . $row['id'] . "'>
                                <button type='submit' name='delete_activity' class='delete-btn'>Delete</button>
                            </form>
                        </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No activities found</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <div class="parent-container">
            <button id="Edit-act">Add Activity</button>
        </div>
        
    </div>

    <p id="tab-2nd">Added Activities</p>

    <div class="table-container added-activities">
        <table class="added-table">
            <thead>
                <tr>
                    <th>Activity</th>
                    <th>Date Avail</th>
                    <th>Price</th>
                    <th>Remove</th>
                </tr>
            </thead>
            <tbody>
                <tr><td colspan="4">No added activities yet</td></tr>
            </tbody>
        </table>
    </div>
    
    <!-- Confirm Button -->
    <button id="confirmBtn">Confirm</button>
</div>

<!-- Modal for adding activities -->
<div id="activityModal" class="modal-activity">
    <div class="modal-content-activities">
        <span id="closeModal" style="float:right; cursor:pointer;">&times;</span>
        <h3>Add Activity</h3>
        <form action="" method="POST">
            <label for="activity_name">Activity Name:</label>
            <input type="text" id="activity_name" name="activity_name" required>
            <label for="price">Price:</label>
            <input type="text" id="price" name="price" required>
            <button type="submit" name="add_activity">Add</button>
        </form>
    </div>
</div>

<!-- Modal for confirming activities -->
<div id="confirmModal" class="modal-confirm">
    <div class="modal-content-confirm">
        <span id="closeConfirmModal" style="float:right; cursor:pointer;">&times;</span>
        <h3>Confirm Activities</h3>
        <div id="summaryContainer">
            <!-- Summary will be populated here -->
        </div>
        <button id="payBtn">Pay</button>
    </div>
</div>

<script>
    const editActButton = document.getElementById("Edit-act");
    const activityModal = document.getElementById("activityModal");
    const confirmModal = document.getElementById("confirmModal");
    const closeModal = document.getElementById("closeModal");
    const closeConfirmModal = document.getElementById("closeConfirmModal");
    const confirmBtn = document.getElementById("confirmBtn");
    
    // Show add activity modal
    editActButton.onclick = function() {
        activityModal.style.display = "block";
    }

    // Close add activity modal
    closeModal.onclick = function() {
        activityModal.style.display = "none";
    }

    // Show confirm modal
    confirmBtn.onclick = function() {
        const summaryContainer = document.getElementById("summaryContainer");
        summaryContainer.innerHTML = ""; // Clear previous summary
        const rows = document.querySelectorAll(".added-table tbody tr");
        
        rows.forEach(row => {
            if (row.children[0].innerText !== "No added activities yet") {
                const activityName = row.children[0].innerText;
                const dateAvail = row.children[1].innerText; // Placeholder for date
                const price = row.children[2].innerText;
                summaryContainer.innerHTML += `
                    <div class="activity-card">
                        <h3>${activityName}</h3>
                        <p><strong>Date Available:</strong> ${dateAvail}</p>
                        <p><strong>Price:</strong> $${price}</p>
                    </div>`;

            }
        });

        confirmModal.style.display = "block"; // Show the confirm modal
    }

    // Close confirm modal
    closeConfirmModal.onclick = function() {
        confirmModal.style.display = "none";
    }

    // Handle pay button click
    document.getElementById("payBtn").onclick = function() {
        window.print(); // Print the current window
    }

    // Close modals when clicking outside of them
    window.onclick = function(event) {
        if (event.target == activityModal) {
            activityModal.style.display = "none";
        }
        if (event.target == confirmModal) {
            confirmModal.style.display = "none";
        }
    }
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





<script src="../samejs-css/activities-resort.js"></script>
<script src="../samejs-css/logout.js"></script>

<script>
    function confirmDelete() {
        return confirm('Are you sure you want to delete this activity?');
    }
</script>
</body>
</html>

<?php 
session_start();
include '../connection/db_connection.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.html");
    exit();
}

// Fetch deleted accounts for resorts
$stmt = $conn->prepare("
    SELECT da.id, r.resort_name, da.reason, da.deletion_date 
    FROM deleted_accounts da
    INNER JOIN resorts r ON da.resort_id = r.id
    ORDER BY da.deletion_date DESC
");
$stmt->execute();
$stmt->bind_result($id, $resort_name, $reason, $deletion_date);

$resort_notifications = [];
while ($stmt->fetch()) {
    $resort_notifications[] = [
        'id' => $id,
        'resort_name' => $resort_name,
        'reason' => $reason,
        'deletion_date' => $deletion_date
    ];
}
$stmt->close();

// Fetch deleted accounts for employees
$stmt = $conn->prepare("
    SELECT dae.id, e.username, dae.reason, dae.deletion_date 
    FROM deleted_accounts_officer dae
    INNER JOIN employeesdb e ON dae.employee_id = e.id
    ORDER BY dae.deletion_date DESC
");
$stmt->execute();
$stmt->bind_result($id, $full_name, $reason, $deletion_date);

$officer_notifications = [];
while ($stmt->fetch()) {
    $officer_notifications[] = [
        'id' => $id,
        'full_name' => $full_name,
        'reason' => $reason,
        'deletion_date' => $deletion_date
    ];
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/cb01d7a304.js" crossorigin="anonymous"></script>
    <title>Admin Notifications</title>
    <link rel="stylesheet" href="notifications.css">
    <link rel="stylesheet" href="../samejs-css/logout.css">
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .tabs {
            display: flex;
            margin-bottom: 20px;
        }

        .tab {
            flex: 1;
            padding: 10px;
            text-align: center;
            cursor: pointer;
            background-color: #f4f4f9;
            border: 1px solid #ddd;
            border-radius: 5px 5px 0 0;
        }

        .tab.active {
            background-color: #fff;
            border-bottom: 2px solid #007BFF;
            font-weight: bold;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .notifications-container {
            margin: 20px;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .notification-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #ddd;
            padding: 10px 0;
        }

        .notification-item:last-child {
            border-bottom: none;
        }

        .notification-title {
            font-weight: bold;
            color: #007BFF;
        }

        .notification-reason {
            font-size: 14px;
            color: #555;
        }

        .notification-date {
            font-size: 12px;
            color: #999;
        }

        .notification-actions button {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        .notification-actions button:hover {
            background-color: #0056b3;
        }

        .empty-notifications {
            text-align: center;
            color: #999;
            margin: 20px 0;
        }

        .back-button {
            background-color: #007BFF;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .back-button:hover {
            background-color: #0056b3;
        }

        .notification-details {
            display: none;
            padding: 10px;
            margin-top: 10px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }

    </style>
</head>
<body>

<div class="sidebar">
    <div> 
        <div class="profile">
            <div class="profile-pic"></div>
            <div class="profile-name">Bernadeth B. Ortega</div>
            <div class="profile-role">Admin</div>
        </div>
    </div>     
    <ul>
        <li class="active">
            <i class="fas fa-home"></i>
            <a href="home.php">Home</a>
        </li>
        <li class="dropdown-btn">
            <i class="fa-solid fa-users-gear"></i>Management 
            <i class="fa fa-caret-down"></i>
        </li>
        <div class="dropdown-container">
            <a href="tourism-officer.php">Tourism Officer</a>
            <a href="management.php"><i class="fa-solid fa-hotel"></i> Resorts</a>
        </div>
        <li>
            <i class="fas fa-bell"></i>
            <a href="notifications.php">Notifications</a>
        </li>
        <li>
            <i class="fa-solid fa-arrow-right-from-bracket"></i>
            <a id="logoutBtn">Log out</a>
        </li>
    </ul>
</div>

<div class="main-content">
    <!-- Topbar -->
    <div class="topbar">
        <div class="search-box">
            <input type="text" placeholder="Search...">
            <button><i class="fas fa-search"></i></button>
        </div>
    </div>

    <p id="tab">Notifications</p>
    <br>

    <div class="tabs">
        <div class="tab active" onclick="switchTab('resortTab')">RESORT</div>
        <div class="tab" onclick="switchTab('officerTab')">OFFICER</div>
    </div>

    <div id="resortTab" class="tab-content active">
        <div class="notifications-container">
            <?php if (!empty($resort_notifications)): ?>
                <?php foreach ($resort_notifications as $notification): ?>
                    <div class="notification-item">
                        <div>
                            <p class="notification-title"><?php echo htmlspecialchars($notification['resort_name']); ?></p>
                            <p class="notification-reason"><?php echo htmlspecialchars($notification['reason']); ?></p>
                            <p class="notification-date">Scheduled Deletion: <?php echo htmlspecialchars($notification['deletion_date']); ?></p>
                        </div>
                        <div class="notification-actions">
                            <button onclick="viewDetails('<?php echo $notification['id']; ?>', 'resort')">View Details</button>
                        </div>
                    </div>
                    <div id="details-resort-<?php echo $notification['id']; ?>" class="notification-details">
                        <button class="back-button" onclick="backToList('resort')">Back</button>
                        <p>Details for the resort: <?php echo htmlspecialchars($notification['resort_name']); ?></p>
                        <p>Reason: <?php echo htmlspecialchars($notification['reason']); ?></p>
                        <p>Deletion Date: <?php echo htmlspecialchars($notification['deletion_date']); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="empty-notifications">No notifications found.</p>
            <?php endif; ?>
        </div>
    </div>

    <div id="officerTab" class="tab-content">
        <div class="notifications-container">
            <?php if (!empty($officer_notifications)): ?>
                <?php foreach ($officer_notifications as $notification): ?>
                    <div class="notification-item">
                        <div>
                            <p class="notification-title"><?php echo htmlspecialchars($notification['full_name']); ?></p>
                            <p class="notification-reason"><?php echo htmlspecialchars($notification['reason']); ?></p>
                            <p class="notification-date">Scheduled Deletion: <?php echo htmlspecialchars($notification['deletion_date']); ?></p>
                        </div>
                        <div class="notification-actions">
                            <button onclick="viewDetails('<?php echo $notification['id']; ?>', 'officer')">View Details</button>
                        </div>
                    </div>
                    <div id="details-officer-<?php echo $notification['id']; ?>" class="notification-details">
                        <button class="back-button" onclick="backToList('officer')">Back</button>
                        <p>Details for the officer: <?php echo htmlspecialchars($notification['full_name']); ?></p>
                        <p>Reason: <?php echo htmlspecialchars($notification['reason']); ?></p>
                        <p>Deletion Date: <?php echo htmlspecialchars($notification['deletion_date']); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="empty-notifications">No notifications found.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    function switchTab(tabId) {
        const tabs = document.querySelectorAll('.tab');
        const contents = document.querySelectorAll('.tab-content');

        tabs.forEach(tab => tab.classList.remove('active'));
        contents.forEach(content => content.classList.remove('active'));

        document.querySelector(`[onclick="switchTab('${tabId}')"]`).classList.add('active');
        document.getElementById(tabId).classList.add('active');
    }

    function viewDetails(notificationId, type) {
        const detailsSection = document.getElementById('details-' + type + '-' + notificationId);
        detailsSection.style.display = 'block';

        // Hide list of notifications
        const notificationItems = document.querySelectorAll('.notification-item');
        notificationItems.forEach(item => item.style.display = 'none');
    }

    function backToList(type) {
        const detailsSections = document.querySelectorAll('.notification-details');
        detailsSections.forEach(section => section.style.display = 'none');

        // Show list of notifications again
        const notificationItems = document.querySelectorAll('.notification-item');
        notificationItems.forEach(item => item.style.display = 'flex');
    }
</script>

<script src="../samejs-css/logout.js"></script>
    <script src="../samejs-css/sidebarjs.js"> </script>

</body>
</html>

<?php
session_start();
include '../connection/db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['employee_id'])) {
    die("You must be logged in to access this page.");
}

// Initialize selected resort variable
$selected_resort_id = null;

// Get the list of resorts for the filter
$resorts_sql = "SELECT id, resort_name FROM resorts";
$resorts_result = $conn->query($resorts_sql);

// Fetch activities based on the selected resort
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Use intval to prevent SQL injection
    $selected_resort_id = isset($_POST['resort_id']) ? intval($_POST['resort_id']) : null;
}

$activities_sql = "SELECT activity_name, price FROM activities";
if ($selected_resort_id) {
    $activities_sql .= " WHERE resort_id = " . $selected_resort_id;
}
$activities_result = $conn->query($activities_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/cb01d7a304.js" crossorigin="anonymous"></script>
    <title>TOURISM TOURIST TRACKER</title>
    <link rel="stylesheet" href="registration.css">
    <link rel="stylesheet" href="../samejs-css/logout.css">
    <link rel="shortcut icon" href="../assets/img/favicon.png" type="image/x-icon">

    <style>
         label {
            font-weight: bold;
        }

        select {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
            width: 100%;
            font-size: 16px;
        }

        select:focus {
            outline: none;
            border-color: #007bff;
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
        <li class="active"><i class="fas fa-clipboard-list"></i><a href="list-of-activities-mto.php">List of Activities</a></li>
        <li><i class="fas fa-file-alt"></i><a href="reportmto.php">Reports</a></li>
        <li class="dropdown-btn"><i class="fas fa-envelope"></i>Message 
                </li>
                    <div class="dropdown-container">
                        <a href="message.php"><i class="fa-solid fa-paper-plane"></i> Send Message</a>
                        <a href="management.php"><i class="fa-solid fa-message"></i> Inbox</a>
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

    <!-- Topbar -->
    <div class="topbar">
    <button id="menuToggle" class="menu-toggle">☰</button>
        <div class="search-box">
        </div>
        <div class="icons"> 
                <a href="settings-employee/home-settings.php" target="_blank"><i class="fas fa-gear"></a></i>
            </div>
    </div>

    <p id="tab">List of Activities</p>

    <br> <br>

    <!-- Resort Filter -->
    <form method="POST" action="">
        <label for="resort_id">Select Resort:</label>
        <select name="resort_id" id="resort_id" onchange="this.form.submit()">
            <option value="">All Resorts</option>
            <?php if ($resorts_result->num_rows > 0): ?>
                <?php while ($resort = $resorts_result->fetch_assoc()): ?>
                    <option value="<?php echo $resort['id']; ?>" <?php echo ($selected_resort_id == $resort['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($resort['resort_name']); ?>
                    </option>
                <?php endwhile; ?>
            <?php endif; ?>
        </select>
    </form>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Activity Name</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($activities_result->num_rows > 0) {
                    while ($row = $activities_result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['activity_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['price']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='2'>No activities found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

</div>
<script src="../samejs-css/logout.js"></script>
<script src="../samejs-css/sidebarjs.js"> </script>

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

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>

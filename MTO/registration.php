<?php
session_start();
include '../connection/db_connection.php';

// Corrected SQL query to sum tourists registered for each resort
$sql = "SELECT r.id, r.resort_name, r.address, 
        COALESCE(SUM(tr.no_of_tourist), 0) AS no_of_tourists 
        FROM resorts r
        LEFT JOIN tourist_registration tr ON r.id = tr.resort_id  -- Make sure to use the correct column name
        GROUP BY r.id, r.resort_name, r.address";

$result = $conn->query($sql);
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
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
    <button id="closeBtn" class="close-btn">&times;</button>
        <img src="../images/logo.png" alt="">
        <ul>
            <li class="active"><i class="fas fa-clipboard-list"></i><a href="registration.php">Registration</a></li>
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

        <!-- Topbar -->
        <div class="topbar">
        <button id="menuToggle" class="menu-toggle">☰</button>
            <div class="search-box">
                <input type="text" placeholder="Search...">
                <button><i class="fas fa-search"></i></button>
            </div>
            <div class="icons"> 
                <a href="settings-employee/home-settings.php" target="_blank"><i class="fas fa-gear"></a></i>
            </div>
        </div>

        <p id="tab">Registration</p>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Resort Name</th>
                        <th>Address</th>
                        <th>No. of Tourists</th>
                        
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Check if the query returns any rows
                    if ($result->num_rows > 0) {
                        // Loop through each row and display the data
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row['id'] . "</td>";
                            echo "<td>" . $row['resort_name'] . "</td>";
                            echo "<td>" . $row['address'] . "</td>";
                            echo "<td>" . $row['no_of_tourists'] . "</td>";  // Show sum of tourists
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No data available</td></tr>";
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

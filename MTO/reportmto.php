<?php
include '../connection/db_connection.php';

// Get the selected month if provided in the URL
$selected_month = isset($_GET['month']) ? $_GET['month'] : '';

// Prepare the SQL query
$sql = "SELECT 
            resort_id, report_month, resort_name, resort_type, no_rooms, total_guests, total_guest_nights, rooms_total, average_guest_per_night,
        average_room_occupancy_night, foreign_visitors, 
        this_municipality_male, this_province_male, other_provinces_male,this_municipality_female, this_province_female, other_provinces_female,
        this_municipality_total, this_province_total, other_provinces_total,foreign_country_male, 
        foreign_country_female, foreign_country_total, grand_total_visitors, 
        grand_total_male, grand_total_female
        FROM submitted_reports";

// Apply month filter if selected
if ($selected_month) {
    $sql .= " WHERE report_month = " . intval($selected_month);
}

// Execute the query and store the result
$result = $conn->query($sql);

// Check if data is returned
if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/cb01d7a304.js" crossorigin="anonymous"></script>
    <title>TOURISM REPORT</title>
    <link rel="stylesheet" href="reportmto.css">
    <link rel="stylesheet" href="../samejs-css/logout.css">
    <link rel="shortcut icon" href="../assets/img/favicon.png" type="image/x-icon">
    <style>
    /* Style for the Export to CSV button */
    </style>
</head>
<body>
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
    
        <!-- Topbar -->
        <div class="topbar">
        <button id="menuToggle" class="menu-toggle">☰</button>
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Search...">
                <button onclick="searchTable()"><i class="fas fa-search"></i></button>
            </div>
            <div class="icons"> 
                <a href="settings-employee/home-settings.php" target="_blank"><i class="fas fa-gear"></a></i>
            </div>
            </div>

        <div class="month">
            <div class="month-filter">
                <form method="GET" action="">
                    <select name="month" id="month" onchange="this.form.submit()">
                        <option value="">Month</option>
                        <option value="1" <?= (isset($_GET['month']) && $_GET['month'] == '1') ? 'selected' : ''; ?>>January</option>
                        <option value="2" <?= (isset($_GET['month']) && $_GET['month'] == '2') ? 'selected' : ''; ?>>February</option>
                        <option value="3" <?= (isset($_GET['month']) && $_GET['month'] == '3') ? 'selected' : ''; ?>>March</option>
                        <option value="4" <?= (isset($_GET['month']) && $_GET['month'] == '4') ? 'selected' : ''; ?>>April</option>
                        <option value="5" <?= (isset($_GET['month']) && $_GET['month'] == '5') ? 'selected' : ''; ?>>May</option>
                        <option value="6" <?= (isset($_GET['month']) && $_GET['month'] == '6') ? 'selected' : ''; ?>>June</option>
                        <option value="7" <?= (isset($_GET['month']) && $_GET['month'] == '7') ? 'selected' : ''; ?>>July</option>
                        <option value="8" <?= (isset($_GET['month']) && $_GET['month'] == '8') ? 'selected' : ''; ?>>August</option>
                        <option value="9" <?= (isset($_GET['month']) && $_GET['month'] == '9') ? 'selected' : ''; ?>>September</option>
                        <option value="10" <?= (isset($_GET['month']) && $_GET['month'] == '10') ? 'selected' : ''; ?>>October</option>
                        <option value="11" <?= (isset($_GET['month']) && $_GET['month'] == '11') ? 'selected' : ''; ?>>November</option>
                        <option value="12" <?= (isset($_GET['month']) && $_GET['month'] == '12') ? 'selected' : ''; ?>>December</option>
                    </select>
                </form>
            </div>
        </div>

        <!-- Report Table -->
        <table id="reportTable">
            <div class="export-btn">
                <form method="post" action="export_to_csv.php">
                    <!-- Add FontAwesome download icon to the button -->
                    <button type="submit" name="export_csv" class="btn">
                        <i class="fas fa-download"></i> Export to CSV
                    </button>
                </form>
            </div>


            <thead>
                <tr>
                    <th rowspan="4">Resort Name</th>
                    <th rowspan="4">Type/Class</th>
                    <th rowspan="4">Number of Rooms</th>
                    <th colspan="6" class="blue">Night Stays</th>
                    <th colspan="12" class="light-blue">Place of Residences</th>
                    <th rowspan="3" colspan="3">Grand Total Visitors</th>
                </tr>
                <tr>
                    <th rowspan="3">Number of Guests Check in (unit: visitors)</th>
                    <th rowspan="3">Number of Guests staying overnight (Guest Nights)</th>
                    <th rowspan="3">Number of Rooms Occupied by Guests (unit: rooms)</th>
                    <th rowspan="3">Average Guests/Night</th>
                    <th rowspan="3">Average Room Occupancy Rate</th>
                    <th rowspan="3">Total Foreign Visitors</th>

                    <th colspan="9">Philippines</th>
                    <th colspan="3" rowspan="2">Foreign Country Residence</th>
                </tr>
                <tr>
                    <th colspan="3">This Municipality</th>
                    <th colspan="3">This Province</th>
                    <th colspan="3">Other Provinces</th>
                </tr>
                <tr>
                    <th>Male</th>
                    <th>Female</th>
                    <th>Total</th>
                    <th>Male</th>
                    <th>Female</th>
                    <th>Total</th>
                    <th>Male</th>
                    <th>Female</th>
                    <th>Total</th>
                    <th>Male</th>
                    <th>Female</th>
                    <th>Total</th>
                    <th>Male</th>
                    <th>Female</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $grand_total_male = $row['this_municipality_male'] + $row['this_province_male'] + $row['other_provinces_male'];
                        $grand_total_female = $row['this_municipality_female'] + $row['this_province_female'] + $row['other_provinces_female'];
                        $grand_total_visitors = $grand_total_male + $grand_total_female;
                        echo "
                        <tr>
                            <td>{$row['resort_name']}</td>
                            <td>{$row['resort_type']}</td>
                            <td>{$row['no_rooms']}</td>
                            <td>{$row['total_guests']}</td>
                            <td>{$row['total_guest_nights']}</td>
                            <td>{$row['rooms_total']}</td>
                            <td>{$row['average_guest_per_night']}</td>
                            <td>{$row['average_room_occupancy_night']}</td>
                            <td>{$row['foreign_visitors']}</td>
                            <td>{$row['this_municipality_male']}</td>
                            <td>{$row['this_municipality_female']}</td>
                            <td>" . ($row['this_municipality_male'] + $row['this_municipality_female']) . "</td>
                            <td>{$row['this_province_male']}</td>
                            <td>{$row['this_province_female']}</td>
                            <td>" . ($row['this_province_male'] + $row['this_province_female']) . "</td>
                            <td>{$row['other_provinces_male']}</td>
                            <td>{$row['other_provinces_female']}</td>
                            <td>" . ($row['other_provinces_male'] + $row['other_provinces_female']) . "</td>
                            <td>{$row['foreign_country_male']}</td>
                            <td>{$row['foreign_country_female']}</td>
                            <td>" . ($row['foreign_country_male'] + $row['foreign_country_female']) . "</td>
                            <td>{$row['grand_total_male']}</td>
                            <td>{$row['grand_total_female']}</td>
                            <td>{$row['grand_total_visitors']}</td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='28'>No data available</td></tr>";
                }
                ?>
            </tbody>
        </table>

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

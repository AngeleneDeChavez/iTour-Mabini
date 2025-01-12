<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


session_start();
include '../connection/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to access this page.");
}

$user_id = $_SESSION['user_id'];

// Get the resort name for the logged-in user
$resort_name = '';
$no_rooms = '';
$tour_type = ''; // Initialize the tour_type variable
$resort_query = "SELECT resort_name, tour_type, no_rooms FROM resorts WHERE id = ?";
$stmt = $conn->prepare($resort_query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($resort_name, $tour_type, $no_rooms); // Fetch resort name and tour_type
$stmt->fetch();
$stmt->close();

// Default to 'Unknown' if tour_type is not set
$tour_type = $tour_type ? $tour_type : 'Unknown';
$no_rooms = $no_rooms ? $no_rooms : '0';



// Get the selected month and year for filtering
$selected_month = isset($_GET['month']) ? $_GET['month'] : null;
$selected_year = isset($_GET['year']) ? $_GET['year'] : date('Y'); // Default to current year


$rooms_query = "SELECT SUM(rooms) as rooms_total 
                FROM tourist_registration 
                WHERE resort_id = ? 
                AND DATE_FORMAT(date_column, '%Y-%m') = ?";
$selected_month = isset($_GET['month']) ? $_GET['month'] : date('m'); // Default to current month if not set
$formatted_date = $selected_year . '-' . str_pad((string)$selected_month, 2, '0', STR_PAD_LEFT);
$stmt = $conn->prepare($rooms_query);
$stmt->bind_param('ii', $user_id, $formatted_date);
$stmt->execute();
$stmt->bind_result($rooms_total);
$stmt->fetch();
$stmt->close();

$rooms_total = $rooms_total ? $rooms_total : 0;



// Get the total number of guests checked in, filtered by month and year
$total_guests = 0;
$guests_query = "SELECT SUM(no_of_tourist) as total_guests FROM tourist_registration WHERE resort_id = ? AND MONTH(date_column) = ? AND YEAR(date_column) = ?";
$stmt = $conn->prepare($guests_query);
$stmt->bind_param('iii', $user_id, $selected_month, $selected_year);
$stmt->execute();
$stmt->bind_result($total_guests);
$stmt->fetch();
$stmt->close();

// Default to 0 if no data
$total_guests = $total_guests ? $total_guests : 0;

$total_guest_nights = 0;

$place_query = "SELECT no_of_tourist, stay_nights, city, province, nationality, male, female FROM tourist_registration WHERE resort_id = ? AND MONTH(date_column) = ? AND YEAR(date_column) = ?";
$stmt = $conn->prepare($place_query);
$stmt->bind_param('iii', $user_id, $selected_month, $selected_year);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $no_of_tourist = $row['no_of_tourist'];
    $night_stays = $row['stay_nights'];  // Retrieve the night stays for each record
    $municipality = strtolower($row['city']);
    $province = strtolower($row['province']);
    $nationality = $row['nationality'];

    // Calculate guest nights (no_of_tourist * night_stays)
    $guest_nights = $no_of_tourist * $night_stays;
    $total_guest_nights += $guest_nights;  // Add to the total guest nights

    // Update municipality counts (gender, etc.)
    // Other existing counts go here
}

$average_guest_per_night = ($total_guests > 0) ? round($total_guest_nights / $total_guests, 2) : 0;

$average_room_occupancy_night = ($total_guests > 0) ? round($rooms_total/($no_rooms*31), precision:2) : 0;


// Get the total number of foreign visitors (non-Philippine nationality) filtered by month and year
$foreign_visitors = 0;
$foreign_query = "SELECT SUM(no_of_tourist) as foreign_visitors FROM tourist_registration WHERE resort_id = ? AND nationality != '134' AND MONTH(date_column) = ? AND YEAR(date_column) = ?";
$stmt = $conn->prepare($foreign_query);
$stmt->bind_param('iii', $user_id, $selected_month, $selected_year);
$stmt->execute();
$stmt->bind_result($foreign_visitors);
$stmt->fetch();
$stmt->close();

// Default to 0 if no data
$foreign_visitors = $foreign_visitors ? $foreign_visitors : 0;



// Calculate place of residence statistics
// Initialize counters for male, female, and total for each category
$this_municipality_male = 0;
$this_municipality_female = 0;
$this_municipality_total = 0;

$this_province_male = 0;
$this_province_female = 0;
$this_province_total = 0;

$other_provinces_male = 0;
$other_provinces_female = 0;
$other_provinces_total = 0;

$foreign_country_male = 0;
$foreign_country_female = 0;
$foreign_country_total = 0;

// Fetch data for place of residence statistics filtered by month and year
$place_query = "SELECT no_of_tourist, city, province, nationality, male, female FROM tourist_registration WHERE resort_id = ? AND MONTH(date_column) = ? AND YEAR(date_column) = ?";
$stmt = $conn->prepare($place_query);
$stmt->bind_param('iii', $user_id, $selected_month, $selected_year);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $no_of_tourist = $row['no_of_tourist'];
    $municipality = strtolower($row['city']);
    $province = strtolower($row['province']);
    $nationality = $row['nationality'];

    // Check gender counts directly from columns
    $male_count = (int)$row['male'];
    $female_count = (int)$row['female'];

    // Update municipality counts
    if ($municipality === '041016') {  // Specific municipality ID
        $this_municipality_male += $male_count;
        $this_municipality_female += $female_count;
        $this_municipality_total += $no_of_tourist;
    }

    // Update province counts
    if ($province === '0410') {  // Specific province ID
        $this_province_male += $male_count;
        $this_province_female += $female_count;
        $this_province_total += $no_of_tourist;
    } else {
        $other_provinces_male += $male_count;
        $other_provinces_female += $female_count;
        $other_provinces_total += $no_of_tourist;
    }

    // Update foreign country counts based on nationality
    if ($nationality !== 134) {  // Assuming 134 is the ID for the Philippines (nationality code)
        $foreign_country_total += $no_of_tourist;
        $foreign_country_male += $male_count;
        $foreign_country_female += $female_count;
    }
}

// Get grand totals for visitors, filtered by month and year
$grand_total_visitors = 0;
$grand_query = "SELECT SUM(no_of_tourist) as grand_total_visitors FROM tourist_registration WHERE resort_id = ? AND MONTH(date_column) = ? AND YEAR(date_column) = ?";
$stmt = $conn->prepare($grand_query);
$stmt->bind_param('iii', $user_id, $selected_month, $selected_year);
$stmt->execute();
$stmt->bind_result($grand_total_visitors);
$stmt->fetch();
$stmt->close();

// Default to 0 if no data
$grand_total_visitors = $grand_total_visitors ? $grand_total_visitors : 0;

$grand_total_male = 0;
$grand_total_female = 0;

// Fetch data for total male and female counts filtered by month and year
$gender_query = "SELECT SUM(male) as total_male, SUM(female) as total_female FROM tourist_registration WHERE resort_id = ? AND MONTH(date_column) = ? AND YEAR(date_column) = ?";
$stmt = $conn->prepare($gender_query);
$stmt->bind_param('iii', $user_id, $selected_month, $selected_year);
$stmt->execute();
$stmt->bind_result($grand_total_male, $grand_total_female);
$stmt->fetch(); 
$stmt->close();

// Default to 0 if no data
$grand_total_male = $grand_total_male ? $grand_total_male : 0;
$grand_total_female = $grand_total_female ? $grand_total_female : 0;
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/cb01d7a304.js" crossorigin="anonymous"></script>
    <title>RESORT REPORTS</title>
    <link rel="stylesheet" href="reports.css">
    <link rel="shortcut icon" href="assets/img/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="../samejs-css/logout.css">
    <style>
        
    </style>
</head>
<body>

    <div class="sidebar">
    <button id="closeBtn" class="close-btn">&times;</button>
        <img src="../images/logo.png" alt="">
        <ul>
        <li class="active"><i class="fas fa-clipboard-list"></i><a href="index.php">Registration</a></li>
            <li><i class="fas fa-tachometer-alt"></i><a href="list-of-activities.php">List of Activities</a></li>
            <li><i class="fa fa-map-marker" aria-hidden="true"></i></i><a href="../marker/index.php">Map</a></li>
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
                                <button class="confirm-btn" id="confirmLogout">Yes</button>
                                <button class="cancel-btn" id="cancelLogout">No</button>
                            </div>
                        </div>
                    </div>
                </div>
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

                <select name="year" id="year" onchange="this.form.submit()">
                    <option value="">Year</option>
                    <!-- Example of dynamic year range from current year to past years -->
                    <?php
                    $current_year = date("Y");
                    for ($year = $current_year; $year >= 2010; $year--) {
                        echo "<option value=\"$year\" " . (isset($_GET['year']) && $_GET['year'] == $year ? 'selected' : '') . ">$year</option>";
                    }
                    ?>
                </select>
            </form>
        </div>
        </div>

        <p id="tab">Night Stays Report</p>
        <form action="submit-report.php" method="post">
        
    <div class="report-table">
        <table>
            <thead>
                <tr>
                    <th>Resort Name</th>
                    <th>Type/Class</th>
                    <th>Number of Rooms</th>
                    <th>Number of Guests Check IN (unit: visitors)</th>
                    <th>Number of Guests staying overnight (Guest Nights)</th>
                    <th>Number of Rooms Occupied by Guests (unit: rooms)</th>
                    <th>Average Guest/Night</th>
                    <th>Average Room Occupancy Rate</th>
                    <th>Total Foreign Visitors</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= htmlspecialchars($resort_name); ?></td>
                    <td><?= $tour_type; ?></td>
                    <td><?= $no_rooms; ?></td>
                    <td><?= $total_guests; ?></td>
                    <td><?= $total_guest_nights; ?></td>
                    <td><?= $rooms_total; ?></td>
                    <td><?= $average_guest_per_night; ?></td>
                    <td><?= $average_room_occupancy_night; ?></td>
                    <td><?= $foreign_visitors; ?></td>
                </tr>
            </tbody>
        </table>
    </div>


        <!-- Hidden fields to store the data -->
        <input type="hidden" name="resort_id" value="<?= htmlspecialchars($user_id); ?>">  <!-- Resort ID from the session -->
        <input type="hidden" name="resort_name" value="<?= htmlspecialchars($resort_name); ?>">
        <input type="hidden" name="resort_type" value="<?= $tour_type; ?>">
        <input type="hidden" name="report_month" value="<?= $selected_month; ?>">  <!-- Current Month -->
        <input type="hidden" name="report_year" value="<?= $selected_year; ?>">
        <input type="hidden" name="no_rooms" value="<?= $no_rooms; ?>">
        <input type="hidden" name="total_guests" value="<?= $total_guests; ?>">
        <input type="hidden" name="total_guest_nights" value="<?= $total_guest_nights; ?>">
        <input type="hidden" name="rooms_total" value="<?= $rooms_total; ?>">
        <input type="hidden" name="average_guest_per_night" value="<?= $average_guest_per_night; ?>">
        <input type="hidden" name="average_room_occupancy_night" value="<?= $average_room_occupancy_night; ?>">

        <input type="hidden" name="foreign_visitors" value="<?= $foreign_visitors; ?>">
        <input type="hidden" name="this_municipality_male" value="<?= $this_municipality_male; ?>">
        <input type="hidden" name="this_province_male" value="<?= $this_province_male; ?>">
        <input type="hidden" name="other_provinces_male" value="<?= $other_provinces_male; ?>">
        <input type="hidden" name="this_municipality_female" value="<?= $this_municipality_female; ?>">
        <input type="hidden" name="this_province_female" value="<?= $this_province_female; ?>">
        <input type="hidden" name="other_provinces_female" value="<?= $other_provinces_female; ?>">
        <input type="hidden" name="this_municipality_total" value="<?= $this_municipality_total; ?>">
        <input type="hidden" name="this_province_total" value="<?= $this_province_total; ?>">
        <input type="hidden" name="other_provinces_total" value="<?= $other_provinces_total; ?>">
        <input type="hidden" name="foreign_country_male" value="<?= $foreign_country_male; ?>">
        <input type="hidden" name="foreign_country_female" value="<?= $foreign_country_female; ?>">
        <input type="hidden" name="foreign_country_total" value="<?= $foreign_country_total; ?>">
        <input type="hidden" name="grand_total_visitors" value="<?= $grand_total_visitors; ?>">
        <input type="hidden" name="grand_total_male" value="<?= $grand_total_male; ?>">
        <input type="hidden" name="grand_total_female" value="<?= $grand_total_female; ?>">

    

    <div class="place">
        <p id="places">Place of Residences</p>
    </div>

    <div class="report-table">
        <table>
            <thead>
                <tr>
                    <th colspan="9">Philippines</th>
                    <th colspan="3" rowspan="2">Foreign Country Residence</th>
                    <th colspan="3" rowspan="2">Grand Total Number of Visitors</th>
                </tr>
                <tr>
                    <td colspan="3">This Municipality</td>
                    <td colspan="3">This Province</td>
                    <td colspan="3">Other Province</td>
                    
                </tr>
                <tr>
                    <td>Male</td>
                    <td>Female</td>
                    <td>Total</td>
                    <td>Male</td>
                    <td>Female</td>
                    <td>Total</td>
                    <td>Male</t>
                    <td>Female</td>
                    <td>Total</td>
                    <td>Male</td>
                    <td>Female</td>
                    <td>Total</td>
                    <td>Male</td>
                    <td>Female</td>
                    <td>Total</td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= $this_municipality_male; ?></td>
                    <td><?= $this_municipality_female; ?></td>
                    <td><?= $this_municipality_total; ?></td>
                    <td><?= $this_province_male; ?></td>
                    <td><?= $this_province_female; ?></td>
                    <td><?= $this_province_total; ?></td>
                    <td><?= $other_provinces_male; ?></td>
                    <td><?= $other_provinces_female; ?></td>
                    <td><?= $other_provinces_total; ?></td>
                    <td><?= $foreign_country_male; ?></td>
                    <td><?= $foreign_country_female; ?></td>
                    <td><?= $foreign_country_total; ?></td>
                    <td><?= $grand_total_male; ?></td>  <!-- Updated for Grand Total Male -->
                    <td><?= $grand_total_female; ?></td>
                    <td><?= $grand_total_visitors; ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="sub-but">
            <button type="submit" id="submit-report">Submit</button>
        </div>
        
</form>

    </div>
    <script src="../samejs-css/logout.js"></script>

    
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

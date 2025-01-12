<link rel="shortcut icon" href="../assets/img/favicon.png" type="image/x-icon">

<?php
include '../connection/db_connection.php';

// Initialize total tourists variable
$total_tourists = 0; // Default value

// Get current year if no year selected
$selected_year = !empty($_POST['year']) ? intval($_POST['year']) : date('Y');
$selected_month = !empty($_POST['month']) ? intval($_POST['month']) : '';

// Fetch the total number of tourists filtered by year and month
$sql = "SELECT SUM(no_of_tourist) AS total_tourists 
        FROM tourist_registration 
        WHERE YEAR(arrival) = ?";

// If a specific month was selected, add it to the query
if (!empty($selected_month)) {
    $sql .= " AND MONTH(arrival) = ?";
}

$stmt = $conn->prepare($sql);
if (!empty($selected_month)) {
    $stmt->bind_param("ii", $selected_year, $selected_month); // Bind year and month
} else {
    $stmt->bind_param("i", $selected_year); // Bind only year
}
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Fetch data
    $row = $result->fetch_assoc();
    $total_tourists = $row['total_tourists'];
}
$stmt->close();

// Initialize an array to hold the total number of tourists per month
$tourists_per_month = array_fill(1, 12, 0); // Fill array with 12 months, all initialized to 0

// Query to fetch data grouped by month, filtering by the selected year
$sql = "SELECT MONTH(arrival) AS month, SUM(no_of_tourist) AS total_tourists
        FROM tourist_registration
        WHERE YEAR(arrival) = ?";

// If a specific month was selected, modify the query to filter by that month
if (!empty($selected_month)) {
    $sql .= " AND MONTH(arrival) = $selected_month";
}

$sql .= " GROUP BY MONTH(arrival)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $selected_year); // Bind only the year
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Loop through the result and store the total number of tourists for each month
    while ($row = $result->fetch_assoc()) {
        $month = intval($row['month']);
        $tourists_per_month[$month] = $row['total_tourists'];
    }
}
$stmt->close();

// Initialize total day tourists and overnight tourists
$totalDayTourists = 0;
$total_overnight_tourists = 0;

// Modify query to filter day tours by selected year and month
$sql = "SELECT SUM(no_of_tourist) AS total_day_tourists 
        FROM tourist_registration 
        WHERE stay_days < 2 
        AND YEAR(arrival) = ?";

// If a specific month was selected, add it to the query
if (!empty($selected_month)) {
    $sql .= " AND MONTH(arrival) = ?";
}

$stmt = $conn->prepare($sql);

if (!empty($selected_month)) {
    $stmt->bind_param("ii", $selected_year, $selected_month); // Bind year and month
} else {
    $stmt->bind_param("i", $selected_year); // Bind only year
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $totalDayTourists = $row['total_day_tourists'] ?: 0; // Handle null case
}

$stmt->close();

// Modify query to filter overnight tourists by selected year and month
$sql = "SELECT SUM(no_of_tourist) AS total_overnight_tourists
        FROM tourist_registration 
        WHERE stay_days > 1
        AND YEAR(arrival) = ?";

if (!empty($selected_month)) {
    $sql .= " AND MONTH(arrival) = ?";
}

$stmt = $conn->prepare($sql);

if (!empty($selected_month)) {
    $stmt->bind_param("ii", $selected_year, $selected_month); // Bind year and month
} else {
    $stmt->bind_param("i", $selected_year); // Bind only year
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $total_overnight_tourists = $row['total_overnight_tourists'] ?: 0;
}

$stmt->close();



// Initialize an array for nationality data
$nationality_data = ['Local' => 0, 'Foreign' => 0];

// Query to fetch nationality data
$sql = "SELECT nationality, SUM(no_of_tourist) AS tourist_count 
        FROM tourist_registration";

if (!empty($selected_month)) {
    $sql .= " WHERE MONTH(arrival) = $selected_month";
}

$sql .= " GROUP BY nationality";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Loop through the result and categorize nationalities
    while ($row = $result->fetch_assoc()) {
        if ($row['nationality'] == 134) {
            $nationality_data['Local'] += $row['tourist_count'];
        } else {
            $nationality_data['Foreign'] += $row['tourist_count'];
        }
    }
}


// Query to fetch top 5 resorts based on total tourists
$sql = "SELECT r.resort_name, SUM(tr.no_of_tourist) AS total_tourists
        FROM tourist_registration tr
        JOIN resorts r ON tr.resort_id = r.id";

if (!empty($selected_month)) {
    $sql .= " WHERE MONTH(tr.arrival) = $selected_month"; // Adjust based on your date column
}

$sql .= " GROUP BY r.resort_name
          ORDER BY total_tourists DESC
          LIMIT 5";

$result = $conn->query($sql);

// Initialize an array for top resorts
$top_resorts = [];

if ($result->num_rows > 0) {
    // Loop through the result and store the top resorts
    while ($row = $result->fetch_assoc()) {
        $top_resorts[] = [
            'resort_name' => $row['resort_name'],
            'total_tourists' => $row['total_tourists'],
        ];
    }
}


// Query to fetch the total number of tourists for each category (children, youth, adults) for the selected year and month
$sql = "SELECT 
            SUM(children) AS total_children,
            SUM(youth) AS total_youth,
            SUM(adults) AS total_adults
        FROM tourist_registration
        WHERE YEAR(arrival) = ?";

// If a specific month was selected, add it to the query
if (!empty($selected_month)) {
    $sql .= " AND MONTH(arrival) = ?";
}

$stmt = $conn->prepare($sql);

if (!empty($selected_month)) {
    $stmt->bind_param("ii", $selected_year, $selected_month); // Bind year and month
} else {
    $stmt->bind_param("i", $selected_year); // Bind only year
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    // Assign the sums to the age_group_data array
    $age_group_data['children'] = $row['total_children'] ?: 0;
    $age_group_data['youth'] = $row['total_youth'] ?: 0;
    $age_group_data['adults'] = $row['total_adults'] ?: 0;
}

$stmt->close();

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/cb01d7a304.js" crossorigin="anonymous"></script>
    <title>TOURISM DASHBOARD</title>
    <link rel="stylesheet" href="dashboardmto.css">
    <link rel="stylesheet" href="../samejs-css/logout.css">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script> <!-- Include ApexCharts -->
    <style>
        
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
    <button id="closeBtn" class="close-btn">&times;</button>
        <img src="../images/logo.png" alt="">
        <ul>
            <li><i class="fas fa-clipboard-list"></i><a href="registration.php">Registration</a></li>
            <li class="active"><i class="fas fa-tachometer-alt"></i><a href="dashboard.php">Dashboard</a></li>
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
            </div>
            <div class="icons"> 
                <a href="settings-employee/home-settings.php" target="_blank"><i class="fas fa-gear"></a></i>
            </div>
        </div>

        <p id="tab">Dashboard</p>

        <!-- Month and Year Filter -->
        <div class="month-filter">
            <form method="POST" action="" id="filterForm">
                <label for="year">Select Year:</label>
                <select name="year" id="year">
                    <?php
                    $currentYear = date('Y');
                    for ($year = $currentYear; $year >= 2000; $year--) {
                        echo "<option value=\"$year\"" . ($selected_year == $year ? ' selected' : '') . ">$year</option>";
                    }
                    ?>
                </select>

                <label for="month">Select Month:</label>
                <select name="month" id="month">
                    <option value="">--Select Month--</option>
                    <?php
                    $months = [
                        1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                        5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                        9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
                    ];
                    foreach ($months as $num => $name) {
                        echo "<option value=\"$num\"" . ($selected_month == $num ? ' selected' : '') . ">$name</option>";
                    }
                    ?>
                </select>
            </form>
        </div>

        <br>

        <div class="dashboard">
    <!-- Card Section -->

    <div class="card-container">
        <div class="card">
            <div class="left-section">
            <i class="fa-solid fa-users"></i>
            </div>
            <div class="right-section">
                <h3>NO. OF TOURIST</h3>
                <p><strong><?php echo $total_tourists; ?></strong></p>
            </div>
        </div>
        <div class="card">
            <div class="left-section">
                <i class="fa-solid fa-sun"></i>
            </div>
            <div class="right-section">
                <h3>NO. OF DAYTOUR</h3>
                <p><?php echo $totalDayTourists; ?></p> <!-- Display the total number of day tourists -->
            </div>
        </div>

        <div class="card">
            <div class="left-section">
                <i class="fa-solid fa-moon"></i>
            </div>
            <div class="right-section">
                <h3>NO. OF OVERNIGHT</h3>
                <p><?php echo $total_overnight_tourists; ?></p> <!-- Display the calculated number here -->
            </div>
        </div>

    </div>


    <!-- Chart and Top Resorts Section -->
    <div class="chart-section">
        <div class="charts">
            <div class="chart-container">
            <div id="chart1"></div> <!-- First chart (Tourist Trends) -->
        </div>
        </div>
        
        <div class="chart-container">
        <div id="ageGroupChart" style="width: 100%;"></div> <!-- Chart Container -->
        </div>
        <div class="resort-card">
            <div class="resort-header">
            <i class="fa-solid fa-ranking-star"></i>
            </div>
            <div class="resort-content">
                <h2>Top 5 Resorts</h2>
                <ul>
                <?php foreach ($top_resorts as $resort): ?>
                    <li><?php echo $resort['resort_name']?></li>
                <?php endforeach; ?>
                </ul>
            </div>
        </div>

    </div>

    <div class="chart2">
        <div class="chart-container">
            <div id="myChart"></div> <!-- Second chart (Pie chart) -->
        </div>
    </div>
</div>


            
        </div>
        
    </div>

    <script>
        // Auto-submit the form when either year or month is changed
        document.getElementById('year').addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
        document.getElementById('month').addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });

        // Your ApexCharts and other chart-related scripts
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
        // Pass the PHP array to JavaScript
        var touristsPerMonth = <?php echo json_encode(array_values($tourists_per_month)); ?>;

        // Chart: Number of Tourists per Month using ApexCharts
        var options = {
            series: [{
                name: "Tourists",
                data: touristsPerMonth // Use the data from PHP
            }],
            chart: {
                height: 350,
                type: 'line',
                zoom: {
                    enabled: false
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'straight'
            },
            title: {
                text: 'Tourists Trends by Month',
                align: 'left'
            },
            grid: {
                row: {
                    colors: ['#f3f3f3', 'transparent'],
                    opacity: 0.5
                },
            },
            xaxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            }
        };

        var chart = new ApexCharts(document.querySelector("#myChart"), options); // Render the chart in the #myChart div
        chart.render();


        var nationalityData = <?php echo json_encode(array_values($nationality_data)); ?>;
        var options2 = {
            series: [{
                name: 'Tourists',
                data: nationalityData // Use the data from PHP
            }],
            chart: {
                type: 'bar',
                height: 350,
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    endingShape: 'rounded'
                },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: ['Local', 'Foreign'], // Categories for the nationality chart
            },
            yaxis: {
                title: {
                    text: 'Number of Tourists'
                }
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val + " tourists"
                    }
                }
            }
        };

        var chart2 = new ApexCharts(document.querySelector("#chart1"), options2); // Render the second chart
        chart2.render();
                });
    </script>

    <script>
        // Example data from PHP (replace this with your actual PHP code)
        var ageGroupData = <?php echo json_encode(array_values($age_group_data)); ?>;

        // Convert string values to numbers
        ageGroupData = ageGroupData.map(Number);

        console.log("Age Group Data for Chart: ", ageGroupData);  // Verify the data

        document.addEventListener('DOMContentLoaded', function () {
            console.log("DOM fully loaded");

            // Set up the pie chart
            var options = {
                series: ageGroupData,
                chart: {
                    type: 'pie',
                    height: 400
                },
                labels: ['Children', 'Youth', 'Adults'],
                title: {
                    text: 'Tourists by Age Group',
                    align: 'center'
                }
            };

            // Initialize chart
            var chart = new ApexCharts(document.querySelector("#ageGroupChart"), options);

            // Render chart
            chart.render().then(function() {
                console.log("Chart rendered successfully");
            }).catch(function(error) {
                console.error("Error rendering chart: ", error);
            });
        });
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
<script src="../samejs-css/sidebarjs.js"> </script>

</body>
</html>

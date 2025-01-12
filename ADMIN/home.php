<link rel="shortcut icon" href="../assets/img/favicon.png" type="image/x-icon">

<?php
session_start();
include '../connection/db_connection.php';

// Increment visit count
$sql_update = "UPDATE page_visits SET visit_count = visit_count + 1 WHERE id = 1";
$conn->query($sql_update);

// Fetch updated visit count
$sql = "SELECT visit_count FROM page_visits WHERE id = 1";
$result = $conn->query($sql);
$visit_count = 0;

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $visit_count = $row['visit_count'];
}

// Fetch total resorts
$resort_query = "SELECT COUNT(*) as resort_count FROM resorts";
$resort_result = $conn->query($resort_query);
$resort_row = $resort_result->fetch_assoc();
$resort_count = $resort_row['resort_count'];

// Fetch total tourism accounts
$tourism_query = "SELECT COUNT(*) as tourism_count FROM employeesdb"; 
$tourism_result = $conn->query($tourism_query);
$tourism_row = $tourism_result->fetch_assoc();
$tourism_count = $tourism_row['tourism_count'];
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMIN PANEL</title>
    <link rel="stylesheet" href="home.css">
    <link rel="stylesheet" href="../samejs-css/logout.css">
    <script src="https://kit.fontawesome.com/cb01d7a304.js" crossorigin="anonymous"></script>
</head>
<body>

<div class="sidebar">
        <div> 
            <div class="profile">
                <div class="profile-pic"></div>
                <div class="profile-name">Bernadeth P. Ortega</div>
                <div class="profile-role">Admin</div>
            </div>
        </div>     
        <ul>
            <li class="active">
                <i class="fas fa-home"></i>
                <a href="home.php">Home</a>
            </li>

            <li class="dropdown-btn"><i class="fa-solid fa-users-gear"></i>Management 
                <i class="fa fa-caret-down"></i>
            </li>
                <div class="dropdown-container">
                    <a href="tourism-officer.php">Tourism Officer</a>
                   <a href="management.php"><i class="fa-solid fa-hotel"></i>    Resorts</a>
                    
                </div>
            
            <li>
                <i class="fas fa-bell"></i>
                <a href="notifications.php">Notifications</a>
            </li>
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

    <div class="main-content">

        <div class="container">
            <div class="user-welcome">
                   <div class="slideshow-container">

                        <div class="mySlides fade">
                            <img src="../images/resort.jpg" style="width:100%" height="200px">
                        </div>

                        <div class="mySlides fade">
                            <img src="../images/resort1.jpg" style="width:100%" height="200px">
                        </div>

                        <div class="mySlides fade">
                            <img src="../images/view.jpg" style="width:100%" height="200px">
                        </div>

                        </div>
                        <br>

                        <div style="text-align:center">
                            <span class="dot"></span> 
                            <span class="dot"></span> 
                            <span class="dot"></span> 
                        </div>
                        
                    </div>
                    
                    <div class="content">
                    <div class="info-cards">
                        <div class="card">
                            <i class="fa-solid fa-hotel" id="blue" style="color: blue"></i>
                            <p style="font-weight: 500">Total number of resort accounts</p>
                            <p><br><?php echo $resort_count; ?></p>
                        </div>
                        <div class="card">
                            <i class="fa-solid fa-users" id="blue" style="color: blue"></i>
                            <p style="font-weight: 500">Total number of tourism accounts</p>
                            <p><br><?php echo $tourism_count; ?></p>
                        </div>
                        <div class="card">
                            <i class="fa-solid fa-chart-line" id="blue" style="color: blue"></i>
                            <p style="font-weight: 500">Total number of page visits</p>
                            <p><br><?php echo $visit_count; ?></p>
                        </div>
                    </div>

                    </div>
    </div>

    <script src="../samejs-css/logout.js"></script>
    <script src="../samejs-css/sidebarjs.js"> </script>
    <script src="../samejs-css/pageviews.js"></script>
    <script src="../samejs-css/home.js"></script>
    

</body>
</html>

<?php
$conn->close();
?>

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <!--JQuery-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<?php
session_start(); // Make sure to start the session

include '../connection/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to access this page.");
}

$user_id = $_SESSION['user_id'];


$nationalities = [];
$nationality_sql = "SELECT id, nationality FROM nationalities"; // Update with your actual table and column names
$nationality_result = $conn->query($nationality_sql);

if ($nationality_result) {
    while ($row = $nationality_result->fetch_assoc()) {
        // Store both ID and nationality name
        $nationalities[] = [ 
            'id' => $row['id'], 
            'name' => $row['nationality']
        ];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $surname = $_POST['surname'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $province = $_POST['province'];
    $city = $_POST['city'];
    $barangay = $_POST['barangay'];
    $no_of_tourist = $_POST['no_of_tourist'];  
    $children = $_POST['children'];
    $youth = $_POST['youth'];
    $adults = $_POST['adults'];
    $male = $_POST['male'];
    $female = $_POST['female'];
    $nationality = $_POST['nationality']; // This will be the ID now
    $arrival = $_POST['arrival'];
    $arrival_time = $_POST['arrival_time'];
    $departure = $_POST['departure'];
    $stay_days = $_POST['stay_days'];
    $stay_nights = $_POST['stay_nights'];
    $rooms = $_POST['rooms'];

    // Updated SQL to include resort_id
    $sql = "INSERT INTO tourist_registration 
            (first_name, surname, email, contact, province, city, barangay, no_of_tourist, children, youth, adults, male, female, nationality, arrival, arrival_time, departure, stay_days, stay_nights, rooms, resort_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssiiiiiiiissssssi", $first_name, $surname, $email, $contact, $province, $city, $barangay, $no_of_tourist, $children, $youth, $adults, $male, $female, $nationality, $arrival, $arrival_time, $departure, $stay_days, $stay_nights, $rooms, $user_id);

    $message = ""; // Initialize message variable
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if ($stmt->execute()) {
            $message = "Registration successful!";
        } else {
            $message = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/cb01d7a304.js" crossorigin="anonymous"></script>
    <title>TOURIST REGISTRATION</title>
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="../samejs-css/logout.css">
    <link rel="stylesheet" href="../samejs-css/successmodal.css">
    <link rel="shortcut icon" href="assets/img/favicon.png" type="image/x-icon">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .profile-pic {
    width: 40px; /* Adjust size as needed */
    height: 40px;
    border-radius: 50%; /* Make it circular */
    object-fit: cover;
}
.popup-message {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #28a745; /* Success Green */
            color: white;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            font-size: 16px;
            z-index: 1000;
            display: none; /* Hidden by default */
        }
        
        .popup-error {
            background-color: #dc3545; /* Error Red */
        }
        
        .popup-message.show {
            display: block;
        }

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
                    <a href="#" class="dropbtn"><i class="fas fa-circle-user"></i></a
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

        <p id="tab">Registration Form</p>

        <?php if (isset($message)): ?>
            <div id="popup" class="popup-message show">
                <p><?= $message; ?></p>
            </div>
            <?php unset($message); ?>
        <?php elseif (isset($message)): ?>
            <div id="popup" class="popup-message popup-error show">
                <p><?= $message; ?></p>
            </div>
            <?php unset($message); ?>
        <?php endif; ?>

        <div class="container">
            <br><br><h2 id="form-name">Tourist Information Fill-Up Form</h2>
            <form action="" method="post">
                <div>
                    <label for="first_name">First Name <span id="required">*</span></label>
                    <input type="text" id="first_name" name="first_name" required>
                </div>
                <div>
                    <label for="surname">Surname <span id="required">*</span></label>
                    <input type="text" id="surname" name="surname" required>
                </div>
                <div>
                    <label for="email">E-mail <span id="required">*</span></label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div>
                    <label for="contact">Contact Number <span id="required">*</span></label>
                    <input type="tel" id="contact" name="contact" required>
                </div>
<!-------------------test---

                <div class="full-width">
                    <label for="regionDropdown">Region <span id="required">*</span></label>
                    <select id="regionDropdown" name="region">
                        <option value="" disabled selected>Please Select</option>
                </select>
                </div>
                <div class="full-width">
                    <label for="provinceDropdown">Province <span id="required">*</span></label>
                    <select id="provinceDropdown" name="province">
                        <option value="" disabled selected>Please Select</option>
                    </select>
                </div>
                <div>
                    <label for="cityDropdown">City <span id="required">*</span></label>
                    <select id="cityDropdown" name="city">
                        <option value="" disabled selected>Please Select</option>
                    </select>
                </div>
                <div>
                    <label for="municipalityDropdown">Municipality <span id="required">*</span></label>
                    <select id="municipalityDropdown" name="municipality">
                        <option value="" disabled selected>Please Select</option>
                    </select>
                </div>
                <div>
                    <label for="barangayDropdown">Barangay <span id="required">*</span></label>
                    <select id="barangayDropdown" name="barangay">
                        <option value="" disabled selected>Please Select</option>
                    </select>
                </div>
--------------->

<!--------------------------------------start test-------------------------------------------->

        <div>
            <label class="form-label">Region *</label>
            <select name="region" class="form-control form-control-md" id="region"></select>
            <input type="hidden" class="form-control form-control-md" name="region_text" id="region-text">
        </div>
        <div>
            <label class="form-label">Province *</label>
            <select name="province" class="form-control form-control-md" id="province"></select>
            <input type="hidden" class="form-control form-control-md" name="province_text" id="province-text">
        </div>
        <div>
            <label class="form-label">City / Municipality *</label>
            <select name="city" class="form-control form-control-md" id="city"></select>
            <input type="hidden" class="form-control form-control-md" name="city_text" id="city-text">
        </div>
        <div>
            <label class="form-label">Barangay *</label>
            <select name="barangay" class="form-control form-control-md" id="barangay"></select>
            <input type="hidden" class="form-control form-control-md" name="barangay_text" id="barangay-text">
        </div>
        <div>
            <label for="street-text" class="form-label">Street (Optional)</label>
            <input type="text" class="form-control form-control-md" name="street_text" id="street-text">
        </div>
      
<script src="ph-address-selector.js"></script>

<!--------------------------------------end test-------------------------------------------->
                <div>
                    <label for="no_of_tourist">No. of Tourist <span id="required">*</span></label>
                    <input type="text" id="no_of_tourist" name="no_of_tourist" required>
                </div>

                <div>
                    <label for="children">Number of Children Aged (0-10 years old) <span id="required">*</span></label>
                    <input type="text" id="children" name="children" required>
                </div>
                <div>
                    <label for="youth">Number of Youth Aged (11-20 years old) <span id="required">*</span></label>
                    <input type="text" id="youth" name="youth" required>
                </div>
                <div>
                    <label for="adults">Number of Adults Aged (21-60 years old) <span id="required">*</span></label>
                    <input type="text" id="adults" name="adults" required>
                </div>
                <div>
                    <label for="male">Number of Male <span id="required">*</span></label>
                    <input type="text" id="male" name="male" required>
                    
                </div>
                <div>
                    <label for="female">Number of Female <span id="required">*</span></label>
                    <input type="text" id="female" name="female" required>
                </div>

                <div>
                    <label for="nationality">Nationality <span id="required">*</span></label>
                    <select id="nationality" name="nationality" required>
                        <option value="" disabled selected>Please Select</option>
                        <?php foreach ($nationalities as $nat): ?>
                            <option value="<?= $nat['id']; ?>"><?= htmlspecialchars($nat['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="arrival">Arrival Date <span id="required">*</span></label>
                    <input type="date" id="arrival" name="arrival" required>
                </div>
                <div>
                    <label for="arrival_time">Arrival Time <span id="required">*</span></label>
                    <input type="time" id="arrival_time" name="arrival_time" required>
                </div>
                <div>
                    <label for="departure">Departure Date <span id="required">*</span></label>
                    <input type="date" id="departure" name="departure" required>
                </div>
                <div>
                    <label for="stay_days">Total Stay Days <span id="required">*</span></label>
                    <input type="number" id="stay_days" name="stay_days" required>
                </div>
                <div>
                    <label for="stay_nights">Total Stay Nights <span id="required">*</span></label>
                    <input type="number" id="stay_nights" name="stay_nights" required>
                </div>
                <div>
                    <label for="rooms">Number of Rooms <span id="required">*</span></label>
                    <input type="number" id="rooms" name="rooms" required>
                </div>
                <div class="submit-container">
                    <input type="submit" value="Submit" class="submit-btn">
                </div>

            </form>

            <?php if (isset($message)): ?>
                <div class="message"><?= htmlspecialchars($message); ?></div>
            <?php endif; ?>
        </div>
    </div>

    <script src="../samejs-css/logout.js"></script>

     <script>
        document.getElementById('arrival').addEventListener('change', calculateStay);
        document.getElementById('departure').addEventListener('change', calculateStay);

        function calculateStay() {
            const arrivalInput = document.getElementById('arrival');
            const departureInput = document.getElementById('departure');
            const arrivalDate = new Date(arrivalInput.value);
            const departureDate = new Date(departureInput.value);

            if (arrivalInput.value && departureInput.value && arrivalDate < departureDate) {
                const timeDifference = departureDate - arrivalDate;
                const daysDifference = Math.floor(timeDifference / (1000 * 60 * 60 * 24));

                // Calculate stay days as daysDifference + 1 and stay nights as daysDifference
                const stayDays = daysDifference + 1; 
                const stayNights = daysDifference;

                document.getElementById('stay_days').value = stayDays;
                document.getElementById('stay_nights').value = stayNights;
            } else {
                document.getElementById('stay_days').value = "";
                document.getElementById('stay_nights').value = "";
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

<script> 
setTimeout(function() {
            const popup = document.getElementById('popup');
            if (popup) {
                popup.classList.remove('show');
            }
        }, 5000);</script>
    

</body>
</html>

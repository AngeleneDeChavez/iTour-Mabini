<?php
session_start();
include '../connection/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to access this page.");
}

$user_id = $_SESSION['user_id']; // Assuming this is the resort ID

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marker</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <!-- Leaflet.js CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="shortcut icon" href="assets/img/favicon.png" type="image/x-icon">
</head>
<body>
    <div class="main">

    <style>
       

        /* Responsive X icon button styling */
        #exitButton {
            position: absolute;
            top: 15px;
            right: 15px;
            width: 40px;
            height: 40px;
            background-color: #ff4c4c;
            border: none;
            border-radius: 50%;
            color: white;
            font-size: 24px;
            font-weight: bold;
            line-height: 40px;
            text-align: center;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.1s;
        }

        #exitButton:hover {
            background-color: #ff1a1a;
            transform: scale(1.1);
        }

        /* Ensure proper scaling on smaller screens */
        @media (max-width: 600px) {
            #exitButton {
                width: 30px;
                height: 30px;
                font-size: 18px;
                line-height: 30px;
                top: 10px;
                right: 10px;
            }
        }

        @media (max-width: 400px) {
            #exitButton {
                width: 25px;
                height: 25px;
                font-size: 16px;
                line-height: 25px;
                top: 8px;
                right: 8px;
            }
        }
    </style>
</head>
<body>
    <button id="exitButton">&times;</button> <!-- X icon -->

    <script>
        document.getElementById("exitButton").addEventListener("click", function () {
            const confirmExit = confirm("Are you sure you want to exit?");
            if (confirmExit) {
                window.location.href = "RESORT/index.php";
            }
        });
    </script>

    </script>
        <div class="container">
            <div class="marker-container">
                <h1>Marker Area</h1>
                <hr>
                <form class="marker" action="./endpoint/add-mark.php" method="POST" enctype="multipart/form-data">
                    <div class="input-group">
                        <h3>Resort Name:</h3>
                        <input id="markerName" type="text" name="mark_name">
                    </div>
                    <div class="input-group">
                        <h3>Latitude:</h3>
                        <input id="latitude" type="text" name="mark_lat" readonly>
                    </div>
                    <div class="input-group">
                        <h3>Longitude:</h3>
                        <input id="longitude" type="text" name="mark_long" readonly>
                    </div>
                    <div class="input-group">
                        <h3>Information:</h3>
                        <textarea id="information" name="mark_info" rows="4" placeholder="Maximum Text 200" maxlength="200"></textarea>
                    </div>
                    <div class="input-group">
                        <h3>Status:</h3>
                        <select id="status" name="mark_status" onchange="updateStatusColor(this)">
                            <option value="not full">Not Full</option>
                            <option value="slightly full">Slightly Full</option>
                            <option value="full">Full</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <h3>Upload Image:</h3>
                        <input type="file" id="imageUpload" name="image_upload" accept="image/*">
                    </div>
                    <div class="input-group">
                        <h3>Pet Friendly:</h3>
                        <select id="petFriendly" name="mark_petFriendly">
                            <option value="yes">Yes</option>
                            <option value="no">No</option>
                        </select>
                    </div>
                    <button type="submit" id="saveMarker" style=" background-color: #007BFF; color: white;">Save Marker</button>
                </form>
                <hr>
                <?php include 'marked.php'; ?>
            </div>
            <div class="map-container">
                <div class="map" id="map"></div>
            </div>
        </div>
    </div>
    <!-- Leaflet.js JS -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        var map = L.map('map').setView([13.726879, 120.908337], 13);
        L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {}).addTo(map);
        
        // Update marked locations when page loads
        <?php
        include('./conn/conn.php');
        $stmt = $conn->prepare("SELECT * FROM tbl_mark");
        $stmt->execute();
        $result = $stmt->fetchAll();
        foreach($result as $row) {
            $markName = $row['mark_name'];
            $markLat = $row['mark_lat'];
            $markLong = $row['mark_long'];
            $markInfo = $row['mark_info'];
            $markStatus = $row['mark_status'];
            $markImage = $row['mark_image'];
            $markPetFriendly = $row['mark_petFriendly'];
            echo "updateMarkedLocations('$markName', $markLat, $markLong, '$markInfo', '$markStatus', '$markImage', '$markPetFriendly');";
        }
        ?>



        var markers = [];
        map.on('click', function(e) {
            var coordinates = e.latlng;
            document.getElementById('latitude').value = coordinates.lat.toFixed(6);
            document.getElementById('longitude').value = coordinates.lng.toFixed(6);
        });

        
function updateMarkedLocations(name, lat, long, info, status, image, petFriendly) {
    var marker = L.marker([lat, long]).addTo(map);
    if (markers === undefined) {
        markers = [marker];
    } else {
        markers.push(marker);
    }
    marker.on('click', function() {
        map.setView([lat, long], 15); // Zoom in on marker click
        openModal(name, lat, long, info, image, status, petFriendly);
    });
}

function openModal(name, lat, long, info, image, status, petFriendly) {
    var contentHTML = "Name: " + name + "<br>Latitude: " + lat + "<br>Longitude: " + long + "<br>Information: " + info;
    document.getElementById("modalText").innerHTML = contentHTML;

    var statusText = document.getElementById("statusText");
    var statusCircle = document.getElementById("statusCircle");
    if (status === "full") {
        statusCircle.style.backgroundColor = "red";
        statusText.textContent = "Full";
    } else if (status === "slightly full") {
        statusCircle.style.backgroundColor = "orange";
        statusText.textContent = "Slightly Full";
    } else {
        statusCircle.style.backgroundColor = "green";
        statusText.textContent = "Not Full";
    }

    var petFriendlyContainer = document.getElementById("modalPetFriendly");
    if (petFriendly === 'yes') {
        petFriendlyContainer.innerHTML = "Pet Friendly: <i class='fa fa-paw'></i>";
    } else {
        petFriendlyContainer.textContent = "Pet Friendly: No";
    }

    var modalImage = document.getElementById("modalImage");
    if (image) {
        modalImage.src = "http://localhostmarker/uploads/" + image; // Adjust the path accordingly
        modalImage.style.display = "block";
    } else {
        modalImage.style.display = "none";
    }

    modal.style.display = "block";
}



        function updateStatusColor(select) {
            var color;
            if (select.value === "full") {
                color = "red";
            } else if (select.value === "slightly full") {
                color = "orange";
            } else {
                color = "green";
            }
            select.style.backgroundColor = color;
        }
    </script>
</body>
</html>

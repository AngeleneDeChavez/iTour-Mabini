<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="shortcut icon" href="assets/img/favicon.png" type="image/x-icon">
    <title>Marked</title>
    <style>
       
        /* Adjust map size */
        .map-container {
            height: 500px; /* Adjust height as needed */
            width: 100%;
        }
        .map {
            height: 100%;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="main">
        <div class="container">
            <div class="marked">
                <h2>Marked Locations</h2>
                <input type="text" id="searchInput" placeholder="Search for Resort..." onkeyup="searchLocations()">
                <ul id="markedLocations">
                    <?php
                        include('./conn/conn.php');
                        $stmt = $conn->prepare("SELECT * FROM tbl_mark");
                        $stmt->execute();
                        $result = $stmt->fetchAll();
                        foreach ($result as $row) {
                            $markId = $row['tbl_mark_id'];
                            $markName = $row['mark_name'];
                            $markLat = $row['mark_lat'];
                            $markLong = $row['mark_long'];
                            $markInfo = $row['mark_info'];
                            $markStatus = $row['mark_status'];
                            $markImage = $row['mark_image'];
                    ?>
                    <div id="<?= $markId ?>" class="location-item">
                        <div class="location-info">
                            <span class="mark-name" style="background-color: <?= $markStatus == 'full' ? 'red' : ($markStatus == 'slightly full' ? 'orange' : 'green') ?>"><?= $markName ?></span>
                            <div class="button-container">
                                <button class="locate-button" onclick="viewLocation('<?= $markName ?>', <?= $markLat ?>, <?= $markLong ?>, '<?= $markInfo ?>', '<?= $markImage ?>', '<?= $markStatus ?>')">Locate</button>
                                <button class="delete-button" onclick="deleteMark(<?= $markId ?>)">Delete</button>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </ul>
            </div>
            <div class="map-container">
                <div class="map" id="map"></div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Location Details</h2>
            <p id="modalText"></p>
            <div id="statusCircle" class="status-circle"></div>
            <span id="statusText"></span>
            <img id="modalImage" src="" alt="Resort Image" width="200" style="display: none;" />
        </div>
    </div>

    <!-- Leaflet.js JS -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        var map = L.map('map').setView([13.726879, 120.908337], 13);
        L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {}).addTo(map);

        // Disable shadow
        L.Icon.Default.mergeOptions({
            shadowUrl: ''
        });

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
                echo "updateMarkedLocations('$markName', $markLat, $markLong, '$markInfo', '$markStatus', '$markImage');";
            }
        ?>

        var markers = [];
        map.on('click', function(e) {
            var coordinates = e.latlng;
            document.getElementById('latitude').value = coordinates.lat.toFixed(6);
            document.getElementById('longitude').value = coordinates.lng.toFixed(6);
        });

        function updateMarkedLocations(name, lat, long, info, status, image) {
            var marker = L.marker([lat, long]).addTo(map);
            if (markers === undefined) {
                markers = [marker];
            } else {
                markers.push(marker);
            }

            marker.on('click', function() {
                map.setView([lat, long], 15); // Zoom in on marker click
                openModal(name, lat, long, info, image, status); // Open modal on marker click
            });
        }

        function viewLocation(name, lat, long, info, image, status) {
            map.setView([lat, long], 15); // Zoom in on marker click
            openModal(name, lat, long, info, image, status); // Open modal on viewLocation call
        }

        function deleteMark(id) {
            if (confirm("Do you want to delete this mark?")) {
                window.location = "./endpoint/delete-mark.php?mark=" + id;
            }
        }

        function searchLocations() {
            var input, filter, ul, li, a, i, txtValue;
            input = document.getElementById('searchInput');
            filter = input.value.toUpperCase();
            ul = document.getElementById("markedLocations");
            li = ul.getElementsByClassName('location-item');
            for (i = 0; i < li.length; i++) {
                a = li[i].getElementsByClassName("mark-name")[0];
                txtValue = a.textContent || a.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    li[i].style.display = "";
                } else {
                    li[i].style.display = "none";
                }
            }
        }

        var modal = document.getElementById("myModal");
        var span = document.getElementsByClassName("close")[0];

        span.onclick = function() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        function openModal(name, lat, long, info, image, status) {
            var contentHTML = "Name: " + name + "<br>Latitude: " + lat + "<br>Longitude: " + long + "<br>Information: " + info;
            document.getElementById("modalText").innerHTML = contentHTML;
            var statusCircle = document.getElementById("statusCircle");
            var statusText = document.getElementById("statusText");
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

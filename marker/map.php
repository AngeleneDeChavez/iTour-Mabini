<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interactive Map with Markers</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <!-- Leaflet.js CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="shortcut icon" href="assets/img/favicon.png" type="image/x-icon">

</head>
<body>
 
        <div class="map-container">
                <div class="map" id="map"></div>
        </div>
    

    <!-- Leaflet.js JS -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <script>
        var map = L.map('map').setView([13.726879, 120.908337], 13);
        
        L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {    
            
        }).addTo(map);

        
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

                echo "updateMarkedLocations('$markName', $markLat, $markLong);";
            }
        ?>

        var markers = [];

        map.on('click', function(e) {
            var coordinates = e.latlng;
            
            document.getElementById('latitude').value = coordinates.lat.toFixed(6);
            document.getElementById('longitude').value = coordinates.lng.toFixed(6);
        });

        function updateMarkedLocations(name, lat, long) {
            var marker = L.marker([lat, long]).addTo(map);
            if (markers === undefined) {
                markers = [marker];
            } else {
                markers.push(marker);
            }
        }

        function viewLocation(name, lat, lng) {
            var marker = L.marker([lat, lng]).addTo(map);
            marker.bindPopup(name).openPopup();
            map.panTo(new L.LatLng(lat, lng));
        }

        function deleteMark(id) {
            if (confirm("Do you want to delete this mark?")) {
                window.location = "./endpoint/delete-mark.php?mark=" + id;
            }
        }
    </script>
</body>
</html>

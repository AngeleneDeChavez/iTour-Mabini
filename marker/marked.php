<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="shortcut icon" href="assets/img/favicon.png" type="image/x-icon">
    <title>Marked</title>
</head>
<body>
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
                    $markPetFriendly = $row['mark_petFriendly'];
                   
            ?>
            <div id="<?= $markId ?>" class="location-item">
                <div class="location-info">
                    <span class="mark-name" style="background-color: <?= $markStatus == 'full' ? 'red' : ($markStatus == 'slightly full' ? 'orange' : 'green') ?>"><?= $markName ?></span>
                    <div class="button-container">
                        <button class="locate-button" onclick="viewLocation('<?= $markName ?>', <?= $markLat ?>, <?= $markLong ?>, '<?= $markInfo ?>', '<?= $markImage ?>', '<?= $markStatus ?>', '<?= $markPetFriendly ?>')">Locate</button>
                        <button class="delete-button" onclick="deleteMark(<?= $markId ?>)">Delete</button>
                    </div>
                </div>
            </div>
            <?php } ?>
        </ul>
    </div>

    <!-- Modal -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Location Details</h2>
            <p id="modalText"></p>
            
            <img id="modalImage" src="" alt="Resort Image" width="200" style="display: none;" />
            <div class="status-container">
                    <div class="status-item">
                    <span id="statusText"></span>
                        <div id="statusCircle" class="status-circle"></div>
                            
                    </div>
                <div id="modalPetFriendly">
                    Pet Friendly <i class="fa fa-paw"></i>
                </div>
            </div>
        </div>
    </div>



    <script>
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

        function viewLocation(name, lat, long, info, image, status, petFriendly) {
            map.setView([lat, long], 15); // Zoom in on marker click
            openModal(name, lat, long, info, image, status, petFriendly);
        }

        function deleteMark(id) {
            if (confirm("Do you want to delete this mark?")) {
                window.location = "./endpoint/delete-mark.php?mark=" + id;
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

        function openModal(name, lat, long, info, image, status, petFriendly) {
            var contentHTML = "Name: " + name + "<br>Latitude: " + lat + "<br>Longitude: " + long + "<br>Information: " + info;
            if (petFriendly === 'yes') {
                contentHTML += "<br>Pet Friendly: <i class='fa fa-paw'></i>";
            } else {
                contentHTML += "<br>Pet Friendly: No";
            }
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

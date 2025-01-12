<?php
session_start();
include '../connection/db_connection.php';

// Default SQL query
$sql = "SELECT 
            id, 
            resort_name, 
            business_permit_no, 
            lastname, 
            firstname, 
            middlename, 
            address, 
            contact_no,
            email, 
            status
        FROM resorts";

// Check if a search query is submitted
if (isset($_POST['search'])) {
    $search_query = $_POST['search'];
    // Update the SQL query to filter resorts based on the search query
    $sql .= " WHERE resort_name LIKE '%" . $conn->real_escape_string($search_query) . "%' 
              OR business_permit_no LIKE '%" . $conn->real_escape_string($search_query) . "%' 
              OR lastname LIKE '%" . $conn->real_escape_string($search_query) . "%' 
              OR firstname LIKE '%" . $conn->real_escape_string($search_query) . "%' 
              OR middlename LIKE '%" . $conn->real_escape_string($search_query) . "%' 
              OR address LIKE '%" . $conn->real_escape_string($search_query) . "%' 
              OR contact_no LIKE '%" . $conn->real_escape_string($search_query) . "%' 
              OR email LIKE '%" . $conn->real_escape_string($search_query) . "%'";
}

// Execute the SQL query
$result = $conn->query($sql);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Administration</title>
    <link rel="stylesheet" href="management.css">
    <link rel="stylesheet" href="../samejs-css/logout.css">
    <script src="https://kit.fontawesome.com/cb01d7a304.js" crossorigin="anonymous"></script>
    <link rel="shortcut icon" href="../assets/img/favicon.png" type="image/x-icon">
    <style>
        .active-status {
    color: green;
    font-weight: bold;
}

.inactive-status {
    color: red;
    font-weight: bold;
}



/* Add to your current CSS file */
table td a {
    text-decoration: none;
    color: inherit;
    padding: 8px;
    display: inline-block;
    font-size: 18px;
    margin: 0 5px;
    border-radius: 4px;
    transition: background-color 0.3s, color 0.3s;
}

table td a i {
    pointer-events: none; /* Disable interaction with the icon */
}

table td a.edit-btn {
    color: #4CAF50; /* Green for Edit */
}

table td a.delete-btn {
    color: #f44336; /* Red for Delete */
}

table td a:hover {
    background-color: #f1f1f1;
    color: #333;
}

table td a.edit-btn:hover {
    background-color: #d4edda; /* Light green */
    color: #155724; /* Dark green text */
}

table td a.delete-btn:hover {
    background-color: #f8d7da; /* Light red */
    color: #721c24; /* Dark red text */
}

/* Ensures that buttons align horizontally and are nicely spaced */
table td {
    text-align: center;
}

table td a {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 8px 12px;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.3s ease;
}

/* Add some spacing around the table */
.table-container {
    margin-top: 20px;
    overflow-x: auto;
}

.edit-popup {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
}

.edit-content {
    background: white;
    width: 400px;
    margin: 100px auto;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
    position: relative;
}

.edit-content h2 {
    margin-top: 0;
    margin-bottom: 20px;
}

.edit-content .close-btn {
    position: absolute;
    top: 10px;
    right: 10px;
    font-size: 18px;
    cursor: pointer;
}

.edit-content label {
    display: block;
    margin-bottom: 5px;
}

.edit-content input, .edit-content select {
    width: 100%;
    margin-bottom: 15px;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.edit-content button {
    width: 100%;
    padding: 10px;
    background: #007BFF;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.edit-content button:hover {
    background: #0056b3;
}


    </style>
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
            <li>
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
    <div class="topbar">
        <form method="POST" action="">
            <div class="search-box">
                <input type="text" name="search" placeholder="Search..." value="<?php echo isset($_POST['search']) ? htmlspecialchars($_POST['search']) : ''; ?>">
                <button type="submit"><i class="fas fa-search"></i></button>
            </div>
        </form>
    </div>

    <p id="tab">Account Administration</p>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Resort Name</th>
                    <th>Business Permit No.</th>
                    <th>Resort Owner</th>
                    <th>Address</th>
                    <th>Contact No.</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $owner_fullname = htmlspecialchars($row["lastname"]) . ', ' .
                                        htmlspecialchars($row["firstname"]) . ' ' .
                                        htmlspecialchars($row["middlename"]);

                        $status = htmlspecialchars($row['status']) == 'active' ? 'Active' : 'Inactive';
                        $status_class = $row['status'] == 'active' ? 'active-status' : 'inactive-status';

                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row["id"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["resort_name"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["business_permit_no"]) . "</td>";
                        echo "<td>" . $owner_fullname . "</td>";
                        echo "<td>" . htmlspecialchars($row["address"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["contact_no"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["email"]) . "</td>";
                        echo "<td class='$status_class'>$status</td>";
                        echo "<td class='action-buttons'>";
                        echo "<a href='#' 
                                class='edit-btn' 
                                data-id='" . htmlspecialchars($row["id"]) . "' 
                                data-resort-name='" . htmlspecialchars($row["resort_name"]) . "' 
                                data-status='" . htmlspecialchars($row["status"]) . "'>
                                <i class='fas fa-edit'></i>
                              </a>";
                        echo "<a href='#' class='delete-btn' onclick='confirmDelete(" . htmlspecialchars($row["id"]) . ")'><i class='fas fa-trash-alt'></i></a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='9'>No records found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Structure -->
<div class="edit-popup" id="editPopup">
    <div class="edit-content">
        <span class="close-btn">&times;</span>
        <h2>Edit Resort Status</h2>
        <form id="editForm">
            <input type="hidden" id="resortId" name="resortId">
            
            <label for="resortName">Resort Name</label>
            <input type="text" id="resortName" name="resortName" disabled> <!-- Disabled field -->

            <label for="status">Status</label>
            <select id="status" name="status">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>

            <button type="submit">Update</button>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const editButtons = document.querySelectorAll(".edit-btn");
        const editPopup = document.getElementById("editPopup");
        const closeBtn = document.querySelector(".close-btn");
        const editForm = document.getElementById("editForm");

        // Open modal on edit button click
        editButtons.forEach(button => {
            button.addEventListener("click", function (e) {
                e.preventDefault();

                // Populate modal fields with data attributes
                document.getElementById("resortId").value = this.dataset.id;
                document.getElementById("resortName").value = this.dataset.resortName;
                document.getElementById("status").value = this.dataset.status;

                // Show the modal
                editPopup.style.display = "block";
            });
        });

        // Close modal
        closeBtn.addEventListener("click", () => {
            editPopup.style.display = "none";
        });

        // Submit form via AJAX
        editForm.addEventListener("submit", function (e) {
            e.preventDefault();

            const formData = new FormData(editForm);

            fetch("update-resort.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Resort status updated successfully!");
                    location.reload();
                } else {
                    alert("Error updating resort: " + data.error);
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("An error occurred.");
            });
        });
    });

    // Confirm delete action
    function confirmDelete(id) {
        if (confirm("Are you sure you want to deactivate this account?")) {
            window.location.href = "deactivate-account.php?id=" + id;
        }
    }
</script>

    <script src="../samejs-css/logout.js"></script>
    <script src="../samejs-css/sidebarjs.js"> </script>
</body>
</html>

<?php $conn->close(); ?>

<?php
// Start the session (if not already started)
session_start();
include '../connection/db_connection.php';

// Default SQL query without search filter
$sql = "SELECT 
            id, 
            lastname,
            firstname,
            middlename,
            email_address, 
            contact_number,
            address,
            position,
            department,
            office_address,
            status
          FROM employeesdb";

// Check if there is a search query
if (isset($_POST['search'])) {
    $search_query = $_POST['search'];
    // Add search condition to the SQL query
    $sql .= " WHERE lastname LIKE '%" . $conn->real_escape_string($search_query) . "%' OR
                  firstname LIKE '%" . $conn->real_escape_string($search_query) . "%' OR
                  middlename LIKE '%" . $conn->real_escape_string($search_query) . "%' OR
                  email_address LIKE '%" . $conn->real_escape_string($search_query) . "%' OR
                  contact_number LIKE '%" . $conn->real_escape_string($search_query) . "%' OR
                  address LIKE '%" . $conn->real_escape_string($search_query) . "%' OR
                  position LIKE '%" . $conn->real_escape_string($search_query) . "%' OR
                  department LIKE '%" . $conn->real_escape_string($search_query) . "%' OR
                  office_address LIKE '%" . $conn->real_escape_string($search_query) . "%'";
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

        table td a.edit-btn {
            color: #4CAF50;
        }

        table td a.delete-btn {
            color: #f44336;
        }

        table td a:hover {
            background-color: #f1f1f1;
            color: #333;
        }

        table td a.edit-btn:hover {
            background-color: #d4edda;
            color: #155724;
        }

        table td a.delete-btn:hover {
            background-color: #f8d7da;
            color: #721c24;
        }

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
    <div class="profile">
        <div class="profile-pic"></div>
        <div class="profile-name">Bernadeth P. Ortega</div>
        <div class="profile-role">Admin</div>
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

<!-- Main Content -->
<div class="main-content">
    <!-- Topbar -->
    <div class="topbar">
        <form method="POST" action="">
            <div class="search-box">
                <input type="text" name="search" placeholder="Search by name, email, position..." value="<?php echo isset($_POST['search']) ? htmlspecialchars($_POST['search']) : ''; ?>">
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
                    <th>Name</th>
                    <th>Email Address</th>
                    <th>Contact Number</th>
                    <th>Address</th>
                    <th>Position</th>
                    <th>Department</th>
                    <th>Office Address</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result && $result->num_rows > 0) {
                    // Output data of each row
                    while($row = $result->fetch_assoc()) {
                        $owner_fullname = htmlspecialchars($row["lastname"]) . ', ' . 
                                          htmlspecialchars($row["firstname"]) . ' ' . 
                                          htmlspecialchars($row["middlename"]);
                        $status = $row['status'] == 'active' ? 'Active' : 'Inactive';
                        $status_class = $row['status'] == 'active' ? 'active-status' : 'inactive-status';
                        
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row["id"]) . "</td>";
                        echo "<td>" . $owner_fullname . "</td>";
                        echo "<td>" . htmlspecialchars($row["email_address"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["contact_number"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["address"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["position"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["department"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["office_address"]) . "</td>";
                        echo "<td class='$status_class'>$status</td>";
                        echo "<td class='action-buttons'>";
                        echo "<a href='#' 
                                class='edit-btn' 
                                data-id='" . htmlspecialchars($row["id"]) . "' 
                                data-resort-name='" . htmlspecialchars($row["lastname"]) . ", " . htmlspecialchars($row["firstname"]) . " " . htmlspecialchars($row["middlename"]) . "' 
                                data-status='" . htmlspecialchars($row["status"]) . "'>
                                <i class='fas fa-edit'></i>
                            </a>";

                        echo "<a href='#' class='delete-btn' onclick='confirmDelete(" . htmlspecialchars($row["id"]) . ")'><i class='fas fa-trash-alt'></i></a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='10'>No records found.</td></tr>";
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
            <input type="hidden" id="EmployeeID" name="EmployeeID">
            
            <label for="EmployeeName">Employee Name</label>
            <input type="text" id="EmployeeName" name="EmployeeName" disabled> <!-- Disabled field -->

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

            // Check if the necessary data attributes are present
            const resortName = this.dataset.resortName;
            const id = this.dataset.id;
            const status = this.dataset.status;

            if (resortName && id && status) {
                // Populate modal fields with data attributes
                document.getElementById("EmployeeID").value = id;
                document.getElementById("EmployeeName").value = resortName;
                document.getElementById("status").value = status;

                // Show the modal
                editPopup.style.display = "block";
            } else {
                console.error("Missing data attributes for employee editing.");
            }
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

        fetch("update-employee.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Account status updated successfully!");
                location.reload();
            } else {
                alert("Error updating account: " + data.error);
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

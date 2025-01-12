<link rel="shortcut icon" href="assets/img/favicon.png" type="image/x-icon">

<?php
include '../../connection/db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $lastname = $conn->real_escape_string($_POST['lastname']);
    $firstname = $conn->real_escape_string($_POST['firstname']);
    $middlename = isset($_POST['middlename']) ? $conn->real_escape_string($_POST['middlename']) : null;
    $resort_name = $conn->real_escape_string($_POST['resort_name']);
    $address = $conn->real_escape_string($_POST['address']);
    $business_permit_no = $conn->real_escape_string($_POST['business_permit_no']);
    $contact_no = $conn->real_escape_string($_POST['contact_no']);
    $resort_email = $conn->real_escape_string($_POST['email']);
    $tour_type = $conn->real_escape_string($_POST['tour_type']);
    $no_rooms = $conn->real_escape_string($_POST['no_rooms']);

    $sql = "INSERT INTO resorts (lastname, firstname, middlename, resort_name, address, business_permit_no, contact_no, email, tour_type, no_rooms) 
            VALUES ('$lastname', '$firstname', '$middlename', '$resort_name', '$address', '$business_permit_no', '$contact_no', '$resort_email', '$tour_type', '$no_rooms')";

    if ($conn->query($sql) === TRUE) {
        
        header("Location: signup.php?owner_id=" . $conn->insert_id); 
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Page</title>
    <link rel="stylesheet" href="register.css">
</head>
<body>

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

       select{
        font-size: 20px;
       }

       select option{
        font-size: 20px;
       }
   </style>
</head>
<body>
   <button id="exitButton">&times;</button> <!-- X icon -->

   <script>
       document.getElementById("exitButton").addEventListener("click", function () {
           const confirmExit = confirm("Are you sure you want to exit?");
           if (confirmExit) {
               window.location.href = " ";
           }
       });
   </script>
     
    <div class="register-container">
        <h2>Business Information</h2>

        <form method="POST" action="register.php">
            <label>Resort Owner *</label>
            <div class="row">
                <input type="text" name="lastname" placeholder="Lastname" required>
                <input type="text" name="firstname" placeholder="Firstname" required>
                <input type="text" name="middlename" placeholder="Middlename">
            </div>

            <label for="resort_name">Resort Name *</label>
            <input type="text" name="resort_name" placeholder="Resort Name" required>

            <label>Address *</label>
            <input type="text" name="address" placeholder="Address" required>

            <label for="email">Email *</label>
            <input type="email" name="email" placeholder="Email" required>

            <label>Business Permit No *</label>
            <input type="text" name="business_permit_no" placeholder="Business Permit No" required>

            <label>Contact No. *</label>
            <input type="text" name="contact_no" placeholder="Contact No." required>

            <div class="row">
                <div>
                    <label for="tour_type">Type</label>
                    <select name="tour_type" id="tour_type" required>
                    <option value="RES">Resort</option>
                    </select>
                </div>
                <div>
                    <label>No of Rooms *</label>
                    <input type="text" name="no_rooms" placeholder="No of Rooms" required>
                </div>
            </div>

            <div class="submit-button">
                <button type="submit">Next &gt;</button>
            </div>
        </form>
    </div>

</body>
</html>


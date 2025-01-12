    <link rel="shortcut icon" href="assets/img/favicon.png" type="image/x-icon">

    <?php
    include '../../connection/db_connection.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $lastname = $conn->real_escape_string($_POST['lastname']);
        $firstname = $conn->real_escape_string($_POST['firstname']);
        $middlename = isset($_POST['middlename']) ? $conn->real_escape_string($_POST['middlename']) : null;
        $email_address = $conn->real_escape_string($_POST['email_address']);
        $contact_number = $conn->real_escape_string($_POST['contact_number']);
        $address = $conn->real_escape_string($_POST['address']);
        $position = $conn->real_escape_string($_POST['position']);
        $department = $conn->real_escape_string($_POST['department']);
        $office_address = $conn->real_escape_string($_POST['office_address']);

        $sql = "INSERT INTO employeesdb (lastname, firstname, middlename, email_address, contact_number, address, position, department, office_address) 
                VALUES ('$lastname', '$firstname', '$middlename', '$email_address', '$contact_number', '$address', '$position', '$department', '$office_address')";

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
        <link rel="stylesheet" href="tourismregister.css">
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

<div class="form-container">
            <h2>REGISTER</h2>

            <form method="POST" action="register.php">
                <label>Name *</label>
                <div class="row">
                    <input type="text" name="lastname" placeholder="Lastname" required>
                    <input type="text" name="firstname" placeholder="Firstname" required>
                    <input type="text" name="middlename" placeholder="Middlename">
                </div>

                <label>Email Address *</label>
                <input type="text" name="email_address" placeholder="Email Address" required>

                <label>Contact Number *</label>
                <input type="text" name="contact_number" placeholder="Contact Number" required>

                <label>Address *</label>
                <input type="text" name="address" placeholder="Address" required>

                <label>Position *</label>
                <input type="text" name="position" placeholder="Position" required>

                <label>Department *</label>
                <input type="text" name="department" placeholder="Department" required>

                <label>Office Address *</label>
                <input type="text" name="office_address" placeholder="Office Address" required>

                <div class="submit-button">
                    <button type="submit">Next &gt;</button>
                </div>
            </form>
        </div>

    </body>
    </html>


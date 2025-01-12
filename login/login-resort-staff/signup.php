<link rel="shortcut icon" href="assets/img/favicon.png" type="image/x-icon">

<?php
include '../../connection/db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize form data
    $username = $conn->real_escape_string($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); 
    $owner_id = $conn->real_escape_string($_POST['owner_id']); 

    $sql = "UPDATE resorts SET username='$username', password='$password' WHERE id='$owner_id'";

    if ($conn->query($sql) === TRUE) {
        header("Location: login.php"); 
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
    <title>Sign Up Page</title>
    <link rel="stylesheet" href="signup.css">
    <script src="https://kit.fontawesome.com/cb01d7a304.js" crossorigin="anonymous"></script>
    <style>
        .password-container {
            position: relative;
            width: 100%;
        }

        .password-container input[type="password"],
        .password-container input[type="text"] {
            width: 100%;
            padding-right: 40px; /* Space for the eye icon */
        }

        .password-container .toggle-eye {
            position: absolute;
            right: 10px;
            top: 35%;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 18px; /* Adjust size of the icon */
            color: #888; /* Default color */
        }

        .password-container .toggle-eye:hover {
            color: #000; /* Color on hover */
        }
    </style>
</head>

<body>

    <div class="register-container">
        <h2>SIGN UP</h2>

        <form method="POST" action="signup.php" onsubmit="return validatePasswords()">
            <input type="hidden" name="owner_id" value="<?php echo htmlspecialchars($_GET['owner_id']); ?>">

            <label>Create Username *</label>
            <input type="text" name="username" placeholder="Username" required>

            <label>Create Password *</label>
            <div class="password-container">
                <input type="password" id="password" name="password" placeholder="Password" required>
                <!-- Eye icon to toggle visibility -->
                <i class="toggle-eye fas fa-eye" onclick="togglePasswordVisibility('password')"></i> <!-- Open eye icon -->
            </div>

            <label>Confirm Password *</label>
            <div class="password-container">
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
                <!-- Eye icon to toggle visibility -->
                <i class="toggle-eye fas fa-eye" onclick="togglePasswordVisibility('confirm_password')"></i> <!-- Open eye icon -->
            </div>

            <div class="submit-button">
                <button type="submit">SIGN UP</button>
            </div>
        </form>
    </div>

    <script>
        function validatePasswords() {
            const password = document.getElementById("password").value;
            const confirmPassword = document.getElementById("confirm_password").value;

            if (password !== confirmPassword) {
                alert("Passwords do not match. Please try again.");
                return false;
            }

            return true;
        }

        function togglePasswordVisibility(fieldId) {
            const passwordField = document.getElementById(fieldId);
            const eyeIcon = passwordField.nextElementSibling;

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIcon.classList.remove('fa-eye'); // Remove open eye class
                eyeIcon.classList.add('fa-eye-slash'); // Add closed eye with slash class
            } else {
                passwordField.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash'); // Remove closed eye with slash class
                eyeIcon.classList.add('fa-eye'); // Add open eye class
            }
        }
    </script>  

</body>
</html>

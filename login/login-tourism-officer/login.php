<?php
include '../../connection/db_connection.php';   
session_start(); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Escape user input to prevent SQL Injection
    $input_username = $conn->real_escape_string($_POST['username']);
    $input_password = $_POST['password'];

    // Query to fetch the employee data, including the status and login-related fields
    $sql = "SELECT id, password, status, failed_attempts, lockout_time FROM employeesdb WHERE username='$input_username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {

        // Fetch the account details
        $row = $result->fetch_assoc();
        $current_time = new DateTime();
        $lockout_time = new DateTime($row['lockout_time']);
        
        // Check if the account is locked
        if ($row['failed_attempts'] >= 3 && $current_time < $lockout_time) {
            $remaining_time = $lockout_time->diff($current_time);
            $error_message = "Your account is locked. Try again in " . $remaining_time->i . " minutes.";
        } else {
            // Check if the account is inactive
            if ($row['status'] == 'inactive') {
                $error_message = "Your account has been deactivated. Please contact support.";
            } else {
                if (password_verify($input_password, $row['password'])) {
                    // Reset failed attempts on successful login
                    $stmt = $conn->prepare("UPDATE employeesdb SET failed_attempts = 0, lockout_time = NULL WHERE id = ?");
                    $stmt->bind_param("i", $row['id']);
                    $stmt->execute();

                    // Store the employee ID in session
                    $_SESSION['employee_id'] = $row['id'];

                    // Redirect to the employee dashboard (or desired page)
                    header("Location: ../../MTO/registration.php");
                    exit();
                } else {
                    // Increment failed attempts
                    $failed_attempts = $row['failed_attempts'] + 1;
                    $lockout_time_str = null;

                    if ($failed_attempts >= 3) {
                        // Lock the account for 15 minutes
                        $lockout_time_str = (new DateTime())->modify('+5 minutes')->format('Y-m-d H:i:s');
                        $error_message = "Too many failed attempts. Account locked for 5 minutes.";
                    } else {
                        $error_message = "Invalid password. Attempt $failed_attempts of 3.";
                    }

                    $stmt = $conn->prepare("UPDATE employeesdb SET failed_attempts = ?, lockout_time = ? WHERE id = ?");
                    $stmt->bind_param("isi", $failed_attempts, $lockout_time_str, $row['id']);
                    $stmt->execute();
                }
            }
        }
    } else {
        $error_message = "User not found.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="login.css">
    <!-- Link to Font Awesome for eye icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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

<button id="exitButton">&times;</button> <!-- X icon -->

<script>
    document.getElementById("exitButton").addEventListener("click", function () {
        const confirmExit = confirm("Are you sure you want to exit?");
        if (confirmExit) {
            window.location.href = " ";
        }
    });

    // Function to toggle password visibility
    function togglePasswordVisibility(fieldId, eyeIcon) {
        const field = document.getElementById(fieldId);

        // Check if the password field is of type password
        if (field.type === "password") {
            field.type = "text";  // Show the password
            eyeIcon.classList.remove("fa-eye"); // Remove open eye
            eyeIcon.classList.add("fa-eye-slash"); // Add eye-slash
        } else {
            field.type = "password";  // Hide the password
            eyeIcon.classList.remove("fa-eye-slash"); // Remove eye-slash
            eyeIcon.classList.add("fa-eye"); // Add open eye
        }
    }
</script>

<div class="login-container">
    <h2>LOGIN</h2>
    <p>Welcome Back!</p>

    <!-- Display error message if any -->
    <?php if (isset($error_message)): ?>
        <div class="error-message" style="color: red;"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <!-- Login Form -->
    <form method="POST" action="login.php">
        <input type="text" name="username" placeholder="Email/Username" required>

        <div class="password-container">
            <input type="password" id="password" name="password" placeholder="Password" required>
            <i class="toggle-eye fas fa-eye" onclick="togglePasswordVisibility('password', this)"></i> <!-- Eye icon -->
        </div>

        <a href="forgot-password.html" class="forgot-password">Forgot Password?</a>
        <button type="submit">LOGIN</button>
    </form>

    <p class="signup">Don't have an account? <a href="register.php">Register</a></p>
</div>

</body>
</html>

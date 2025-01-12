<?php
session_start();
include '../../connection/db_connection.php';


// Check if the user is logged in
if (!isset($_SESSION['employee_id'])) {
    header("Location: ../../index.html");
    exit();
}

$employee_id = $_SESSION['employee_id'];

// Fetch the username from the database
$stmt = $conn->prepare("SELECT username FROM employeesdb WHERE id = ?");
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$stmt->bind_result($username);
$stmt->fetch();
$stmt->close();

if ($username) {
    $_SESSION['username'] = $username;
} else {
    $username = "Unknown User";
}
// Initialize messages and error flags
$message = "";
$passwordError = false;
$passwordMismatch = false;
$passwordIncorrect = false;  // Track if the password is incorrect
$deleteAccount = false;  // Track if account is to be deleted

// Handle password change form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['current_password'], $_POST['new_password'], $_POST['confirm_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Verify if new password and confirm password match
    if ($new_password !== $confirm_password) {
        $passwordMismatch = true;
    } else {
        // Verify current password
        $stmt = $conn->prepare("SELECT password FROM employeesdb WHERE id = ?");
        $stmt->bind_param("i", $employee_id);
        $stmt->execute();
        $stmt->bind_result($hashed_password);
        $stmt->fetch();
        $stmt->close();

        if (password_verify($current_password, $hashed_password)) {
            // Update password
            $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_stmt = $conn->prepare("UPDATE employeesdb SET password = ? WHERE id = ?");
            $update_stmt->bind_param("si", $new_hashed_password, $employee_id);

            if ($update_stmt->execute()) {
                $message = "Password updated successfully!";
            } else {
                $message = "Error updating password: " . $conn->error;
            }
            $update_stmt->close();
        } else {
            $message = "Current password is incorrect.";
            $passwordError = true;
        }
    }
}

if (isset($_POST['delete_account_password'])) {
    $delete_account_password = $_POST['delete_account_password'];

    // Verify the password entered for account deletion
    $stmt = $conn->prepare("SELECT password FROM employeesdb WHERE id = ?");
    $stmt->bind_param("i", $employee_id);
    $stmt->execute();
    $stmt->bind_result($hashed_password);
    $stmt->fetch();
    $stmt->close();

    if (password_verify($delete_account_password, $hashed_password)) {
        $_SESSION['delete_account_verified'] = true; // Use session to track verification
    } else {
        $passwordIncorrect = true; // Show error if password is incorrect
    }
}

// Process deletion after reason is submitted
if (isset($_POST['delete_account_reason']) && isset($_SESSION['delete_account_verified']) && $_SESSION['delete_account_verified']) {
    $delete_account_reason = $_POST['delete_account_reason'];

    // Log the account deletion reason into the `deleted_accounts` table
    $log_stmt = $conn->prepare("
        INSERT INTO deleted_accounts_officer (employee_id, reason, deletion_date) 
        VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 30 DAY))
    ");
    $log_stmt->bind_param("is", $employee_id, $delete_account_reason);

    if ($log_stmt->execute()) {
        $message = "Your account deletion request has been received. Your account will be deleted within 30 days.";
        unset($_SESSION['delete_account_verified']); // Clear verification session
    } else {
        $message = "Error logging deletion request: " . $conn->error;
    }

    $log_stmt->close();
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/cb01d7a304.js" crossorigin="anonymous"></script>
    <title>Password and Account Management</title>
    <link rel="stylesheet" href="update-profile-information.css">
    <link rel="shortcut icon" href="assets/img/favicon.png" type="image/x-icon">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }

        .main-content {
            padding: 20px;
        }

        .welcome-section {
            max-width: 900px;
            margin: 0 auto;
            text-align: center;
        }

        .intro {
            font-size: 1.2rem;
            color: #555;
            margin-bottom: 20px;
        }

        .message {
            margin-bottom: 20px;
            color: green;
        }

        .button {
            display: block;
            width: 300px;
            padding: 15px;
            margin: 10px auto;
            border: 2px solid #007BFF;
            border-radius: 20px;
            text-align: center;
            cursor: pointer;
            font-size: 18px;
            background-color: #f8f9fa;
            transition: all 0.3s;
        }

        .button:hover {
            background-color: #e2e6ea;
        }

        .form-container {
            display: none;
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
            padding: 30px;
            border: none;
            border-radius: 10px;
            background-color: white;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .password-container {
            position: relative;
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .password-container input[type="password"], .password-container input[type="text"] {
            width: 540px;
            padding: 15px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ccc;
            margin-right: 10px;
            margin-left: 20px;
        }

        .toggle-password {
            position: absolute;
            right: 25px;
            cursor: pointer;
            font-size: 18px;
        }

        .input-group {
            margin-bottom: 15px;
        }

        input[type="submit"] {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }

        input[type="submit"]:hover {
            background-color: #218838;
        }

        .danger-button {
            background-color: #ff4c4c;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 5px;
            width: 100%;
        }

        .danger-button:hover {
            background-color: #e03e3e;
        }

        .back-arrow {
            cursor: pointer;
            font-size: 20px;
            margin-bottom: 50px;
            margin-left: -450px;
        }

        /* Error message */
        .error-message {
            color: red;
            font-size: 14px;
            margin-top: 10px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .button, .form-container {
                width: 100%;
            }
        }
    </style>

    <script>
        function togglePasswordVisibility(id, iconId) {
            var passwordField = document.getElementById(id);
            var icon = document.getElementById(iconId);
            if (passwordField.type === "password") {
                passwordField.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                passwordField.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }

        function showPasswordForm() {
            document.getElementById('passwordForm').style.display = 'block';
            document.getElementById('deleteAccountForm').style.display = 'none';
            document.getElementById('optionsContainer').style.display = 'none';
        }

        function showDeleteAccountForm() {
        document.getElementById('deleteAccountForm').style.display = 'block';
        document.getElementById('deleteAccountReasonForm').style.display = 'none';
        document.getElementById('optionsContainer').style.display = 'none';
    }

        function showDeleteAccountReasonForm() {
            document.getElementById('deleteAccountReasonForm').style.display = 'block';
            document.getElementById('deleteAccountForm').style.display = 'none';
        }


        function showOptions() {
            document.getElementById('passwordForm').style.display = 'none';
            document.getElementById('deleteAccountForm').style.display = 'none';
            document.getElementById('optionsContainer').style.display = 'block';
        }

        // Automatically show password form if there was a password error
        <?php if ($passwordError): ?>
            window.onload = function() {
                showPasswordForm();
            };
        <?php endif; ?>

        // Automatically show password form if passwords do not match
        <?php if ($passwordMismatch): ?>
            window.onload = function() {
                showPasswordForm();
            };
        <?php endif; ?>

        // Automatically show delete account form if password is verified
        <?php if (isset($_SESSION['delete_account_verified']) && $_SESSION['delete_account_verified']): ?>
        window.onload = function() {
            showDeleteAccountReasonForm();
        };
        <?php endif; ?>


        // Automatically show incorrect password message
        <?php if ($passwordIncorrect): ?>
            window.onload = function() {
                alert("Password is incorrect.");
            };
        <?php endif; ?>
    </script>
</head>

<body>
    <?php include 'settings.php'; ?>

    <div class="main-content">
        <div class="welcome-section">
            <h1>Welcome to Your Account Settings</h1>
            <p class="intro">Here you can change your password or delete your account. Please choose an option below.</p>

            <?php if ($message): ?>
                <p class="message"><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>

            <!-- Options container for buttons -->
            <div id="optionsContainer">
                <div class="button" onclick="showPasswordForm()">
                    Change Password
                </div>
                <div class="button" onclick="showDeleteAccountForm()">
                    Delete Account
                </div>
            </div>

            <!-- Hidden Change Password Form -->
            <div class="form-container" id="passwordForm">
                <div class="back-arrow" onclick="showOptions()">
                    <i class="fas fa-arrow-left"></i>
                </div>
                <form method="POST">
                    <div class="input-group">
                        <label for="oldPassword">Old <br> Password</label>
                        <div class="password-container">
                            <input type="password" id="oldPassword" name="current_password" required>
                            <i class="fas fa-eye toggle-password" id="oldPasswordIcon" onclick="togglePasswordVisibility('oldPassword', 'oldPasswordIcon')"></i>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="newPassword">New <br> Password</label>
                        <div class="password-container">
                            <input type="password" id="newPassword" name="new_password" required>
                            <i class="fas fa-eye toggle-password" id="newPasswordIcon" onclick="togglePasswordVisibility('newPassword', 'newPasswordIcon')"></i>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="confirmPassword">Confirm <br> Password</label>
                        <div class="password-container">
                            <input type="password" id="confirmPassword" name="confirm_password" required>
                            <i class="fas fa-eye toggle-password" id="confirmPasswordIcon" onclick="togglePasswordVisibility('confirmPassword', 'confirmPasswordIcon')"></i>
                        </div>
                    </div>

                    <?php if ($passwordMismatch): ?>
                        <div class="error-message">Passwords do not match!</div>
                    <?php endif; ?>

                    <input type="submit" value="Change Password">
                </form>
            </div>

            <!-- Modal for Delete Account -->
            <div class="form-container" id="deleteAccountForm">
                <div class="back-arrow" onclick="showOptions()">
                    <i class="fas fa-arrow-left"></i>
                </div>
                <form method="POST">
                    <label for="delete_account_password">Please enter your password to confirm account deletion:</label>
                    <input type="password" name="delete_account_password" required>
                    <input type="submit" value="Verify Password">
                </form>
            </div>

            <!-- Reason for deleting account -->
            <div class="form-container" id="deleteAccountReasonForm" style="display: none;">
                <form method="POST">
                    <label for="delete_account_reason">Why are you deleting your account?</label>
                    <select name="delete_account_reason" required>
                        <option value="Too expensive">Too expensive</option>
                        <option value="Not satisfied with services">Not satisfied with services</option>
                        <option value="Other">Other</option>
                    </select>
                    <input type="submit" value="Confirm Deletion">
                </form>
            </div>
        </div>
    </div>
</body>
</html>


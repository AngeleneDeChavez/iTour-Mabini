<?php
include 'db_connection.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Sanitize token to prevent SQL injection
    $token = $conn->real_escape_string($token);

    // Check if the token exists and is not expired
    $sql = "SELECT * FROM resorts WHERE reset_token = ? AND reset_token_expiry > NOW()";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Token is valid, show the reset password form
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];

            if ($new_password === $confirm_password) {
                // Update the password
                $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
                $sql = "UPDATE resorts SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE reset_token = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ss", $hashed_password, $token);
                $stmt->execute();

                echo "Your password has been reset successfully!";
            } else {
                echo "Passwords do not match.";
            }
        }
    } else {
        echo "Invalid or expired token.";
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>New Credentials</title>
  <link rel="stylesheet" href="new-credentials.css">
</head>
<body>

  <div class="container">
    <div class="icon">
      <i class="fas fa-mobile-alt"></i> <!-- Mobile Icon -->
    </div>
    <div class="title">NEW CREDENTIALS</div>
    <div class="subtitle">Your identity has been verified<br>Set your new password!</div>
    
    <div class="input-container">
      <i class="fas fa-lock input-icon"></i> <!-- Lock Icon -->
      <input type="password" class="input-box" id="new-password" placeholder="New Password">
    </div>

    <div class="input-container">
      <i class="fas fa-lock input-icon"></i> <!-- Lock Icon -->
      <input type="password" class="input-box" id="confirm-password" placeholder="Confirm Password">
    </div>

    <button type="submit" class="update-button" onclick="updatePassword()">Update</button>
  </div>


</body>
</html>

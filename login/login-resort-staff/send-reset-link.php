<?php
// Include your database connection
include 'db_connection.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    // Validate email format
   // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
            exit;
        }

        // Sanitize email to avoid SQL injection
        $email = $conn->real_escape_string($email);  // Prevent SQL injection

        // Check if the email exists in the database
        $sql = "SELECT * FROM resorts WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();


    if ($result->num_rows > 0) {
        // Generate a unique token
        // Generate a unique token and expiry time
        $token = bin2hex(random_bytes(50)); // Random 50-byte token
        $expiry_time = date("Y-m-d H:i:s", strtotime("+1 hour")); // Token expiry time (1 hour)

        // Insert the token and expiry into the database
        $sql = "UPDATE resorts SET reset_token = ?, reset_token_expiry = ? WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $token, $expiry_time, $email);
        $stmt->execute();


        // Send the reset password link via email
        $reset_link = "localhost/final-website/reset-password.php?token=$token";
        
        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.example.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'gelyynnn@gmail.com';
            $mail->Password = 'Lenedel26';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            //Recipients
            $mail->setFrom('no-reply@yourwebsite.com', 'Your Website');
            $mail->addAddress($email);

            //Content
            $mail->isHTML(true);
            $mail->Subject = 'Reset Your Password';
            $mail->Body    = "Click on the link below to reset your password:<br><a href='$reset_link'>$reset_link</a>";

            $mail->send();
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Mailer Error: ' . $mail->ErrorInfo]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Email not found in our records.']);
    }
}
?>

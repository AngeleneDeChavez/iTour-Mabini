<link rel="shortcut icon" href="assets/img/favicon.png" type="image/x-icon">

<?php
include '../../connection/db_connection.php';
session_start(); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $input_username = $conn->real_escape_string($_POST['username']);
    $input_password = $_POST['password'];

   
    $sql = "SELECT id, password FROM admin WHERE username='$input_username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {

        $row = $result->fetch_assoc();
        
        if (password_verify($input_password, $row['password'])) {
            $_SESSION['admin_id'] = $row['id']; 
            header("Location: ../../ADMIN/home.php");
            exit();
        } else {
            $error_message = "Invalid password.";
        }
    } else {
        $error_message = "Admin not found.";
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
               window.location.href = "";
           }
       });
   </script>

    <div class="login-container">
        
        <h2>LOGIN</h2>
        <p>Welcome Back!</p>

        <?php if (isset($error_message)): ?>
            <div class="error-message" style="color: red;"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <input type="text" name="username" placeholder="Email/Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">LOGIN</button>
        </form>
    </div>

</body>
</html>

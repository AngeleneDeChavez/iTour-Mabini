<?php
// Example password
$password = 'asdfghjkl'; // Your chosen password
// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
echo $hashed_password; // Output the hashed password
?>

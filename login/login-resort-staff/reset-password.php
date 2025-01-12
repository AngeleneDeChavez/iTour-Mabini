<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Forget Password</title>
  <script src="https://kit.fontawesome.com/cb01d7a304.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="forgot-password.css">
</head>
<body>

  <div class="back-button">
    <a href="login.html"><i class="fas fa-arrow-left"></i></a> <!-- Back Icon -->
  </div>
  <div class="container">
    <div class="icon">
      <i class="fas fa-lock"></i> 
    </div>
    <div class="title">FORGET PASSWORD</div>
    <div class="subtitle">Provide your account's email to reset your password!</div>
    <div class="input-group"> 
      <i class="fas fa-envelope"></i> 
      <input type="email" id="email" name="email" placeholder="Email" required>
    </div>
    <button type="button" class="submit-button" onclick="sendResetLink()">
      Submit
      <i class="fas fa-arrow-right"></i> 
    </button>
  </div>

  <script>
    function sendResetLink() {
      var email = document.getElementById("email").value;
      
      // Check if email is empty
      if (email.trim() === "") {
        alert("Please enter your email address.");
        return;
      }

      // Check if email is valid (simple email validation)
      var emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
      if (!emailRegex.test(email)) {
        alert("Please enter a valid email address.");
        return;
      }

      // Debugging: Check if email value is being captured
      console.log("Email entered:", email);

      // Make an AJAX request to send the reset password link
      var xhr = new XMLHttpRequest();
      xhr.open("POST", "send-reset-link.php", true);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
          // Handle response from the server
          var response = JSON.parse(xhr.responseText);
          if (response.success) {
            alert("A reset link has been sent to your email!");
          } else {
            alert(response.message); // Display error if email not found
          }
        }
      };
      xhr.send("email=" + encodeURIComponent(email));
    }
  </script>
</body>
</html>

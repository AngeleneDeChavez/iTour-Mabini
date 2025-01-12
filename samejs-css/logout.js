
        // Get elements
        var modal = document.getElementById("logoutModal");
        var logoutBtn = document.getElementById("logoutBtn");
        var confirmLogout = document.getElementById("confirmLogout");
        var cancelLogout = document.getElementById("cancelLogout");

        // Show the modal when clicking the "Log out" button
        logoutBtn.onclick = function() {
            modal.style.display = "block";
        }

        // Confirm logout: redirect to logout.php
        confirmLogout.onclick = function() {
            window.location.href = '../login/logout.php'; // Redirect to logout page
        }

        // Cancel logout: close the modal
        cancelLogout.onclick = function() {
            modal.style.display = "none"; // Hide the modal
        }

        // Close modal when clicking outside of the modal content
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
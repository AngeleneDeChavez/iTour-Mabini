
    $(document).ready(function() {
        var message = "<?php echo addslashes($message); ?>"; // Safely escape the message
        if (message) {
            $('#modalMessage').text(message); // Set message in modal
            $('#successModal').show(); // Show modal
        }

        // Close the modal when the user clicks on <span> (x)
        $('#closeModal').click(function() {
            $('#successModal').hide();
        });

        // Close the modal when the user clicks on OK button
        $('#okButton').click(function() {
            $('#successModal').hide();
        });
    });

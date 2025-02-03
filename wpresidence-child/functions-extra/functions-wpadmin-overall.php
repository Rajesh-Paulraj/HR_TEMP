<?php

add_action('admin_footer', function() {
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const notices = document.querySelectorAll('.notice');
            notices.forEach(function(notice) {
                if (notice.textContent.includes('You are using FileBird Lite plugin, which serve the same purpose as our folder module.')) {
                    notice.style.display = 'none';
                }
            });
        });
    </script>
    <?php
});

// Disable all emails
// add_action('phpmailer_init', function($phpmailer) {
//     // Disable email by overriding the send() method
//     $phpmailer->ClearAllRecipients(); // Clear all recipients to ensure no email is sent
// });



?>

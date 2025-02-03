<?php
// Template Name: User Dashboard - Customer Comments (Redirect to wp-admin Comments)
// Wp Estate Pack

// wp_redirect(admin_url('edit-comments.php'));
wp_redirect(admin_url('edit.php?post_type=site-review'));
exit; // Always call exit after wp_redirect
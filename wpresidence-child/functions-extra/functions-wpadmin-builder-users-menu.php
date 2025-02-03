<?php

// START - Builder Users menu in wp-admin
function add_builder_users_menu() {
    add_menu_page(
        'Builder Users',               // Page title
        'Builder Users',               // Menu title
        'manage_options',         // Capability required
        'builder-users-menu',          // Menu slug
        'redirect_to_builder_users_page', // Callback function
        'dashicons-businessperson', // Icon
        6                         // Position in the menu
    );
}
add_action('admin_menu', 'add_builder_users_menu');

function redirect_to_builder_users_page() {
    wp_redirect( admin_url( 'users.php?role=contributor' ) );
    exit;
}
// END - Builder Users menu in wp-admin

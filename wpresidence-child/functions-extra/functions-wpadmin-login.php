<?php


add_filter('login_redirect', 'custom_reset_password_redirect', 10, 3);
function custom_reset_password_redirect($redirect_to, $requested_redirect_to, $user) {
    // Redirect after password reset
    if (isset($_GET['action']) && $_GET['action'] === 'resetpass') {
        return home_url('/?action=loginpopup'); // Replace with your desired URL
    }
    return $redirect_to;
}

add_filter('login_message', 'custom_login_message');
function custom_login_message($message) {
    // Modify the login message
    if (strpos($message, 'Log in') !== false) {
        $message = str_replace(
            'Log in',
            '<a href="' . esc_url(home_url('/?action=loginpopup')) . '">Log in</a>',
            $message
        );
    }
    return $message;
}



?>

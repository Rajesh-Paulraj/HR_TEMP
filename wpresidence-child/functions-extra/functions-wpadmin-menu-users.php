<?php

// START - Remove unwanted fileds in User page in wp-admin
function remove_unwanted_user_profile_fields($user) {
    // Example: Remove the "First Name" field
    ?>
    <style>
        .user-rich-editing-wrap,
        .user-admin-color-wrap,
        .user-comment-shortcuts-wrap,
        .user-admin-bar-front-wrap,
        .user-url-wrap,
        .user-language-wrap,
        .user-facebook-wrap,
        .user-twitter-wrap,
        .user-linkedin-wrap,
        .user-pinterest-wrap,
        .user-instagram-wrap,
        .user-mobile-wrap,
        .user-skype-wrap,
        .user-title-wrap,
        .user-small_custom_picture-wrap,
        .user-package_id-wrap,
        .user-package_listings-wrap,
        .user-package_featured_listings-wrap,
        .user-profile_id-wrap,
        .user-stripe-wrap,
        .user-custom_picture-wrap,
        .user-stripe_subscription_id-wrap,
        .user-has_stripe_recurring-wrap,
        .user-paypal_agreement-wrap,
        .user-user_agent_id-wrap,
        .user-description-wrap { 
            display: none; 
        }
    </style>
    <?php
}
add_action('show_user_profile', 'remove_unwanted_user_profile_fields');
add_action('edit_user_profile', 'remove_unwanted_user_profile_fields');
// END - Remove unwanted fileds in User page in wp-admin




?>

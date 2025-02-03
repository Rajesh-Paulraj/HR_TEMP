<?php

// START - GIFT VOUCHER
function add_gift_voucher_admin_menu() {
    add_menu_page(
        'Gift Voucher',
        'Gift Voucher',
        'manage_options',
        'gift-voucher',
        'render_gift_voucher_admin_page',
        'dashicons-tickets',
        30
    );
}
add_action('admin_menu', 'add_gift_voucher_admin_menu');

function render_gift_voucher_admin_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'gift_vouchers';

    // Handle filter values
    $filter_user = isset($_GET['filter_user']) ? intval($_GET['filter_user']) : null;
    $filter_voucher = isset($_GET['filter_voucher']) ? sanitize_text_field($_GET['filter_voucher']) : null;
    $filter_redeemed = isset($_GET['filter_redeemed']) ? intval($_GET['filter_redeemed']) : null;
    $sort_order = isset($_GET['order']) && $_GET['order'] === 'asc' ? 'ASC' : 'DESC'; // Default to DESC

    // Build query
    $query = "SELECT * FROM $table_name WHERE 1=1";
    if ($filter_user) {
        $query .= $wpdb->prepare(" AND user_id = %d", $filter_user);
    }
    if ($filter_voucher) {
        $query .= $wpdb->prepare(" AND voucher_type = %s", $filter_voucher);
    }
    if (!is_null($filter_redeemed)) {
        $query .= $wpdb->prepare(" AND redeemed = %d", $filter_redeemed);
    }
    $query .= " ORDER BY date_requested $sort_order"; // Sorting based on date

    $results = $wpdb->get_results($query);

    // Get distinct filter values for dropdowns
    $users = $wpdb->get_results("SELECT DISTINCT user_id FROM $table_name");
    $vouchers = $wpdb->get_results("SELECT DISTINCT voucher_type FROM $table_name");

    echo '<div class="wrap">';
    echo '<h1 class="wp-heading-inline">Gift Voucher Records</h1>';

    // Filters Form
    echo '<form method="get" action="">';
    echo '<input type="hidden" name="page" value="gift-voucher">';
    echo '<select name="filter_user">';
    echo '<option value="">Filter by User</option>';
    foreach ($users as $user) {
        $user_data = get_userdata($user->user_id);
        $selected = $filter_user == $user->user_id ? 'selected' : '';
        echo "<option value='{$user->user_id}' {$selected}>{$user_data->display_name}</option>";
    }
    echo '</select>';

    echo '<select name="filter_voucher">';
    echo '<option value="">Filter by Voucher</option>';
    foreach ($vouchers as $voucher) {
        $selected = $filter_voucher == $voucher->voucher_type ? 'selected' : '';
        echo "<option value='{$voucher->voucher_type}' {$selected}>{$voucher->voucher_type}</option>";
    }
    echo '</select>';

    echo '<select name="filter_redeemed">';
    echo '<option value="">Filter by Redeemed</option>';
    echo '<option value="1" ' . selected($filter_redeemed, 1, false) . '>Received</option>';
    echo '<option value="0" ' . selected($filter_redeemed, 0, false) . '>Not Received</option>';
    echo '</select>';

    echo '<button type="submit" class="button button-primary">Filter</button>';
    echo '<a href="' . admin_url('admin.php?page=gift-voucher') . '" class="button button-secondary">Reset</a>';
    echo '</form>';

    // Table Display
    $current_url = admin_url('admin.php?page=gift-voucher');
    $new_order = $sort_order === 'ASC' ? 'desc' : 'asc';
    $sortable_date_header = "<a href='{$current_url}&order={$new_order}' class='sortable'>" . 
                             "Date Requested" . 
                             " <span class='dashicons dashicons-arrow-". ($sort_order === 'ASC' ? "up" : "down") ."'></span>" . 
                             "</a>";

    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead>
        <tr>
            <th>ID</th>
            <th>User Name</th>
            <th>Email</th>
            <th>Property</th>
            <th>Voucher Type</th>
            <th>Received</th>
            <th>' . $sortable_date_header . '</th>
        </tr>
    </thead>
    <tbody>';
    if ($results) {
        foreach ($results as $row) {
            $user = get_userdata($row->user_id);
            $property_title = get_the_title($row->property_id);
            $property_link = get_permalink($row->property_id);

            // Actionable Redeemed Column
            $redeemed_action = $row->redeemed
            ? '<button class="button redeem-toggle button-green" data-id="' . $row->id . '" data-redeemed="0">Received</button>'
            : '<button class="button redeem-toggle button-red" data-id="' . $row->id . '" data-redeemed="1">Not Received</button>';

            echo '<tr>';
            echo '<td>' . esc_html($row->id) . '</td>';
            echo '<td>' . esc_html($user->display_name) . '</td>';
            echo '<td>' . esc_html($user->user_email) . '</td>';
            echo '<td><a href="' . esc_url($property_link) . '" target="_blank">' . esc_html($property_title) . '</a></td>';
            echo '<td>' . esc_html($row->voucher_type) . '</td>';
            echo '<td>' . $redeemed_action . '</td>';
            echo '<td>' . esc_html($row->date_requested) . '</td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="7">No records found.</td></tr>';
    }

    echo '</tbody></table></div>';

    // Add inline JavaScript for AJAX handling
    ?>
    <script>
        jQuery(document).ready(function ($) {
            $('.redeem-toggle').on('click', function () {
                var button = $(this);
                var recordId = button.data('id');
                var newStatus = button.data('redeemed');

                // Change button text to "Loading..." and disable it
                button.prop('disabled', true).text('Loading...');

                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'update_gift_voucher_redeemed',
                        id: recordId,
                        redeemed: newStatus,
                    },
                    success: function (response) {
                        if (response.success) {
                            // Update button text and styles based on the new status
                            if (newStatus === 1) {
                                button.removeClass('button-red').addClass('button-green').text('Received');
                                button.data('redeemed', 0); // Update the data attribute
                            } else {
                                button.removeClass('button-green').addClass('button-red').text('Not Received');
                                button.data('redeemed', 1); // Update the data attribute
                            }
                        } else {
                            alert('Failed to update the redeemed status.');
                        }
                        button.prop('disabled', false); // Re-enable the button
                    },
                    error: function () {
                        alert('An error occurred while updating the redeemed status.');
                        button.prop('disabled', false); // Re-enable the button
                    },
                });
            });
        });

    </script>
    <?php
}


// AJAX handler to update the redeemed status
function update_gift_voucher_redeemed() {
    if (!current_user_can('manage_options') || !isset($_POST['id'], $_POST['redeemed'])) {
        wp_send_json_error(['message' => 'Unauthorized request.']);
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'gift_vouchers';
    $id = intval($_POST['id']);
    $redeemed = intval($_POST['redeemed']);

    $updated = $wpdb->update(
        $table_name,
        ['redeemed' => $redeemed],
        ['id' => $id],
        ['%d'],
        ['%d']
    );

    if ($updated !== false) {
        wp_send_json_success(['message' => 'Redeemed status updated successfully.']);
    } else {
        wp_send_json_error(['message' => 'Failed to update the redeemed status.']);
    }
}
add_action('wp_ajax_update_gift_voucher_redeemed', 'update_gift_voucher_redeemed');



function enqueue_admin_custom_styles() {
    // Check if we are on the Gift Voucher admin page
    $screen = get_current_screen();
    if ($screen->id === 'toplevel_page_gift-voucher') {
        wp_add_inline_style(
            'wp-admin',
            '
            .button-green {
                background-color: #28a745 !important; /* Green */
                color: white !important;
                border: none !important;
            }
            .button-green:hover {
                background-color: #218838 !important; /* Darker green */
            }

            .button-red {
                background-color: #dc3545 !important; /* Red */
                color: white !important;
                border: none !important;
            }
            .button-red:hover {
                background-color: #c82333 !important; /* Darker red */
            }
            '
        );
    }
}
add_action('admin_enqueue_scripts', 'enqueue_admin_custom_styles');


// *********************************************************************************************
// ************************************DONT DELETE**********************************************
// *********************************************************************************************

// // DONT DELETE IT - I JUST RUN THIS CREATE TABLE BY SWITCH THE THEME - SO CURRENTLY ITS NOT USE -BUT KEEP IT

// function create_gift_voucher_table() {
//     global $wpdb;
//     $table_name = $wpdb->prefix . 'gift_vouchers';
//     $charset_collate = $wpdb->get_charset_collate();

//     $sql = "CREATE TABLE $table_name (
//         id mediumint(9) NOT NULL AUTO_INCREMENT,
//         user_id bigint(20) UNSIGNED NOT NULL,
//         property_id bigint(20) UNSIGNED NOT NULL, -- Column for property ID
//         voucher_type varchar(50) NOT NULL,
//         redeemed tinyint(1) DEFAULT 0 NOT NULL, -- Column for redeemed status (0 = Not Redeemed, 1 = Redeemed)
//         date_requested datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
//         PRIMARY KEY  (id)
//     ) $charset_collate;";

//     require_once ABSPATH . 'wp-admin/includes/upgrade.php';
//     dbDelta($sql);
// }
// add_action('after_switch_theme', 'create_gift_voucher_table');

// *********************************************************************************************
// ************************************DONT DELETE**********************************************
// *********************************************************************************************

function handle_gift_voucher_submission() {
    if (!is_user_logged_in() || !isset($_POST['voucher_type'])) {
        wp_send_json_error('Invalid request.');
    }

    // Get the property ID from the AJAX request
    $property_id = isset($_POST['property_id']) ? intval($_POST['property_id']) : 0;

    global $wpdb;
    $user_id = get_current_user_id();
    $voucher_type = sanitize_text_field($_POST['voucher_type']);
    $table_name = $wpdb->prefix . 'gift_vouchers';

    $wpdb->insert($table_name, array(
        'user_id' => $user_id,
        'voucher_type' => $voucher_type,
        'property_id' => $property_id
    ));

    $user = wp_get_current_user();

    $voucher_image_url = '';

    switch ($voucher_type) {
        case 'amazon':
            $voucher_image_url = 'https://homereviewz.in/wp-content/uploads/2024/11/amazon-gift-card.png';
            break;
        case 'croma':
            $voucher_image_url = 'https://homereviewz.in/wp-content/uploads/2024/11/croma-gift-card.png';
            break;
        case 'lifestyle':
            $voucher_image_url = 'https://homereviewz.in/wp-content/uploads/2024/11/lifestyle-gift-card.png';
            break;
        default:
            $voucher_image_url = 'https://homereviewz.in/wp-content/uploads/2024/11/amazon-gift-card.png';
            break;
    }

    // Define the email content with the dynamic image URL
    $email_content = "
        <html>
            <head>
                <style>
                    /* Email body styles */
                    body {
                        font-family: Arial, sans-serif;
                        background-color: #f9f9f9;
                        color: #333;
                        margin: 0;
                        padding: 0;
                    }
                    .email-container {
                        width: 600px;
                        margin: 20px auto;
                        background-color: #fff;
                        border-radius: 8px;
                        overflow: hidden;
                        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                        padding: 20px;
                    }
                    .email-header {
                        background-color: #4CAF50;
                        color: white;
                        padding: 15px;
                        text-align: center;
                        font-size: 24px;
                        font-weight: bold;
                        border-radius: 8px 8px 0 0;
                    }
                    .email-body {
                        padding: 20px;
                        line-height: 1.6;
                    }
                    .voucher-info {
                        font-size: 18px;
                        margin: 20px 0;
                        background-color: #f4f4f4;
                        padding: 15px;
                        border-radius: 5px;
                        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
                    }
                    .voucher-info p {
                        margin: 0;
                        color: #333;
                        font-weight: bold;
                    }
                    .voucher-info img {
                        max-width: 100%;
                        margin-top: 10px;
                        border-radius: 5px;
                    }
                    .email-footer {
                        background-color: #f4f4f4;
                        color: #555;
                        padding: 10px;
                        text-align: center;
                        font-size: 14px;
                        border-radius: 0 0 8px 8px;
                    }
                    .button {
                        display: inline-block;
                        background-color: #4CAF50;
                        color: white;
                        padding: 12px 20px;
                        text-align: center;
                        text-decoration: none;
                        font-size: 16px;
                        border-radius: 5px;
                        margin-top: 20px;
                    }
                    .button:hover {
                        background-color: #45a049;
                    }
                </style>
            </head>
            <body>
                <div class='email-container'>
                    <div class='email-header'>
                        Gift Voucher Request
                    </div>
                    <div class='email-body'>
                        <p><strong>Dear Admin,</strong></p>
                        <p><strong>{$user->display_name}</strong> has requested a <strong>{$voucher_type}</strong> voucher. Please process this request at your earliest convenience.</p>
                        <div class='voucher-info'>
                            <p>Voucher Type: <strong>{$voucher_type}</strong></p>
                            <img src='{$voucher_image_url}' alt='{$voucher_type} Voucher Image' />
                        </div>
                    </div>
                </div>
            </body>
        </html>
    ";

    // Set the email headers to send HTML email
    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
    );

    // Send the email with the HTML content and dynamic image
    wp_mail(
        get_option('admin_email'),
        'Gift Voucher Request',
        $email_content,
        $headers
    );
    // END - Send admin email

    // Define the email content with enhanced styling for the user
    $email_content_user = "
        <html>
            <head>
                <style>
                    /* Email body styles */
                    body {
                        font-family: Arial, sans-serif;
                        background-color: #f9f9f9;
                        color: #333;
                        margin: 0;
                        padding: 0;
                    }
                    .email-container {
                        width: 600px;
                        margin: 20px auto;
                        background-color: #fff;
                        border-radius: 8px;
                        overflow: hidden;
                        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                        padding: 20px;
                    }
                    .email-header {
                        background-color: #4CAF50;
                        color: white;
                        padding: 15px;
                        text-align: center;
                        font-size: 24px;
                        font-weight: bold;
                        border-radius: 8px 8px 0 0;
                    }
                    .email-body {
                        padding: 20px;
                        line-height: 1.6;
                    }
                    .voucher-info {
                        font-size: 18px;
                        margin: 20px 0;
                        background-color: #f4f4f4;
                        padding: 15px;
                        border-radius: 5px;
                        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
                    }
                    .voucher-info p {
                        margin: 0;
                        color: #333;
                        font-weight: bold;
                    }
                    .voucher-info img {
                        max-width: 100%;
                        margin-top: 10px;
                        border-radius: 5px;
                    }
                    .email-footer {
                        background-color: #f4f4f4;
                        color: #555;
                        padding: 10px;
                        text-align: center;
                        font-size: 14px;
                        border-radius: 0 0 8px 8px;
                    }
                    .button {
                        display: inline-block;
                        background-color: #4CAF50;
                        color: white;
                        padding: 12px 20px;
                        text-align: center;
                        text-decoration: none;
                        font-size: 16px;
                        border-radius: 5px;
                        margin-top: 20px;
                    }
                    .button:hover {
                        background-color: #45a049;
                    }
                </style>
            </head>
            <body>
                <div class='email-container'>
                    <div class='email-header'>
                        Gift Voucher Request Confirmation
                    </div>
                    <div class='email-body'>
                        <p><strong>Dear {$user->display_name},</strong></p>
                        <p>Thank you for requesting a <strong>{$voucher_type}</strong> voucher. We have received your request, and the voucher code will be sent to you soon.</p>
                        <div class='voucher-info'>
                            <p>Voucher Type: <strong>{$voucher_type}</strong></p>
                            <img src='{$voucher_image_url}' alt='{$voucher_type} Voucher Image' />
                        </div>
                        <p>If you have any questions or concerns, feel free to reach out to us. We are happy to assist you!</p>
                    </div>
                </div>
            </body>
        </html>
        ";

        // Set the email headers to send HTML email
        $headers_user = array(
        'Content-Type: text/html; charset=UTF-8',
        );

        // Send the email to the user with the HTML content and dynamic image
        wp_mail(
        $user->user_email,          // Recipient (User's email)
        'Your Gift Voucher Request',  // Subject
        $email_content_user,         // Body (HTML content)
        $headers_user                // Headers for HTML content
        );

    // END - Send user email

    wp_send_json_success('Request submitted successfully.');
}
add_action('wp_ajax_process_gift_voucher', 'handle_gift_voucher_submission');



function check_user_gift_voucher() {
    if (!isset($_POST['user_id'])) {
        wp_send_json_error(['message' => 'Invalid user ID.']);
    }

    $user_id = intval($_POST['user_id']);

    // Query the database to check if the user already collected a voucher
    global $wpdb;
    $table_name = $wpdb->prefix . 'gift_vouchers';
    $voucher_exists = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name WHERE user_id = %d",
        $user_id
    ));

    if ($voucher_exists > 0) {
        wp_send_json_success(['already_collected' => true]);
    } else {
        wp_send_json_success(['already_collected' => false]);
    }
}
add_action('wp_ajax_check_user_gift_voucher', 'check_user_gift_voucher');

// END - GIFT VOUCHER 
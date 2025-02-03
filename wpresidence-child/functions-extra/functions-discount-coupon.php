<?php
// functions-discount-coupon.php

// Enqueue scripts and styles
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style('discount-coupon-styles', get_stylesheet_directory_uri() . '/custom-css/discount-coupon.css');
    wp_enqueue_script('custom-script-discount-coupon', get_stylesheet_directory_uri() . '/js/custom-script-discount-coupon.js', ['jquery'], null, true);
    wp_localize_script('custom-script-discount-coupon', 'discountCouponData', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'sms_api_url' => 'http://site.ping4sms.com/api/smsapi',
        'sms_api_key' => 'c32abbde3c33a7176f2bd2b3545dcdbe',
        'sms_template_id' => '1507165967974501361'
    ]);
});

include_once  get_stylesheet_directory() .'/templates/extra-custom/discount-coupon-modal.php';

// AJAX to handle form submission
add_action('wp_ajax_handle_coupon_submission', 'handle_coupon_submission');
add_action('wp_ajax_nopriv_handle_coupon_submission', 'handle_coupon_submission');
function handle_coupon_submission() {
    $name = sanitize_text_field($_POST['name']);
    $mobile = sanitize_text_field($_POST['mobile']);
    $email = sanitize_email($_POST['email']);
    $occupation = sanitize_text_field($_POST['occupation']);
    $bhk_size = sanitize_text_field($_POST['bhk_size']);
    $property_id = intval($_POST['property_id']);
    $appointment = isset($_POST['appointment']) && $_POST['appointment'] === 'true';

    global $wpdb;
    $table_name = $wpdb->prefix . 'discount_coupons';
    $coupon_code = wp_generate_password(8, false);

    $wpdb->insert($table_name, [
        'property_id' => $property_id,
        'name' => $name,
        'mobile' => $mobile,
        'email' => $email,
        'occupation' => $occupation,
        'bhk_size' => $bhk_size,
        'coupon_code' => $coupon_code,
        'created_at' => current_time('mysql'),
    ]);

    $builder_email = get_post_meta($property_id, 'builder_email', true);
    $admin_email = get_option('admin_email');

    $subject = 'Discount Coupon Request';
    $message = "<p>Dear $name,</p><p>Thank you for requesting a discount coupon for the property. Your coupon code is: <strong>$coupon_code</strong>.</p><p>Contact us for more details.</p>";

    $headers = ['Content-Type: text/html; charset=UTF-8'];

    // Generate PDF
    require_once WP_CONTENT_DIR . '/plugins/fpdf186/fpdf.php';

    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, "Customer Name: $name", 0, 1);
    $pdf->Cell(0, 10, "Mobile: $mobile", 0, 1);
    $pdf->Cell(0, 10, "Email: $email", 0, 1);
    $pdf->Cell(0, 10, "Coupon Code: $coupon_code", 0, 1);
    $pdf->Cell(0, 10, "Date: " . date('d-m-Y'), 0, 1);

    $file_path = wp_upload_dir()['basedir'] . "/coupons/Discount-Coupon-$coupon_code.pdf";
    $pdf->Output('F', $file_path);

    // Add file to email
    $attachments = [$file_path];
    wp_mail($email, $subject, $message, $headers, $attachments);
    if ($appointment && $builder_email) {
        wp_mail($builder_email, $subject, $message, $headers, $attachments);
    }
    wp_mail($admin_email, $subject, $message, $headers, $attachments);

    wp_send_json_success(["message" => "Coupon generated successfully.", "pdf_url" => "https://homereviewz.in/wp-content/uploads/2024/11/Discount-Coupon-WDSM5mIe.pdf"]);
}

// AJAX to handle OTP sending
add_action('wp_ajax_send_otp', 'send_otp');
add_action('wp_ajax_nopriv_send_otp', 'send_otp');
function send_otp() {
    // Clear any previously buffered output
    ob_clean();
    $otp = rand(10000, 99999);
    $contact = sanitize_text_field($_POST['contact']);
    $type = sanitize_text_field($_POST['type']); // 'mobile' or 'email'

    if ($type === 'mobile') {
        $api_url = 'http://site.ping4sms.com/api/smsapi?key=897047674784d7d01dce93ecf29f95fa&route=4&sender=HRZPPL&number=' . $contact . '&sms=' . $otp . 'is your Home Reviewz access code. This code is valid for 3 minutes. Please do not share this code with anyone. Team HRZ&templateid=1607100000000332086';
        wp_remote_get($api_url);
    } elseif ($type === 'email') {
        wp_mail($contact, 'Your OTP Code', "Your OTP code is: $otp ", ['Content-Type: text/html; charset=UTF-8']);
    }

    set_transient("otp_$contact", $otp, 15 * 15);
    wp_send_json_success("OTP sent successfully.");
}

// AJAX to handle OTP verification
add_action('wp_ajax_verify_otp', 'verify_otp');
add_action('wp_ajax_nopriv_verify_otp', 'verify_otp');
function verify_otp() {
    $contact = sanitize_text_field($_POST['contact']);
    $entered_otp = sanitize_text_field($_POST['otp']);
    $saved_otp = get_transient("otp_$contact");

    if ($entered_otp == $saved_otp) {
        delete_transient("otp_$contact");
        wp_send_json_success("OTP verified successfully.");
    } else {
        wp_send_json_error("Invalid OTP.");
    }
}

// Create the database table on theme activation
register_activation_hook(__FILE__, function() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'discount_coupons';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        property_id BIGINT UNSIGNED NOT NULL,
        name VARCHAR(255) NOT NULL,
        mobile VARCHAR(15) NOT NULL,
        email VARCHAR(255) NOT NULL,
        occupation VARCHAR(255) NOT NULL,
        bhk_size VARCHAR(255) NOT NULL,
        coupon_code VARCHAR(100) NOT NULL,
        created_at DATETIME NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
});


// Add the "Discount Coupons" admin menu
function add_discount_coupons_admin_menu() {
    add_menu_page(
        'Discount Coupons',              // Page Title
        'Discount Coupons',              // Menu Title
        'manage_options',                // Capability
        'discount-coupons',              // Menu Slug
        'render_discount_coupons_admin_page', // Callback Function
        'dashicons-tickets',             // Icon
        31                               // Position
    );
}
add_action('admin_menu', 'add_discount_coupons_admin_menu');

function render_discount_coupons_admin_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'discount_coupons';

    // Allowed columns for sorting
    $allowed_columns = [
        'id',
        'property_id',
        'name',
        'mobile',
        'email',
        'occupation',
        'bhk_size',
        'coupon_code',
        'created_at'
    ];

    // Handle filter/sort values
    $filter_name = isset($_GET['filter_name']) ? sanitize_text_field($_GET['filter_name']) : '';
    $filter_email = isset($_GET['filter_email']) ? sanitize_email($_GET['filter_email']) : '';
    $filter_mobile = isset($_GET['filter_mobile']) ? sanitize_text_field($_GET['filter_mobile']) : '';
    $filter_occupation = isset($_GET['filter_occupation']) ? sanitize_text_field($_GET['filter_occupation']) : '';
    $filter_bhk_size = isset($_GET['filter_bhk_size']) ? sanitize_text_field($_GET['filter_bhk_size']) : '';
    // $filter_property_id = isset($_GET['filter_property_id']) ? intval($_GET['filter_property_id']) : '';
    $filter_property_name = isset($_GET['filter_property_name']) ? sanitize_text_field($_GET['filter_property_name']) : '';
    $filter_start_date = isset($_GET['filter_start_date']) ? sanitize_text_field($_GET['filter_start_date']) : '';
    $filter_end_date = isset($_GET['filter_end_date']) ? sanitize_text_field($_GET['filter_end_date']) : '';

    $orderby = isset($_GET['orderby']) && in_array($_GET['orderby'], $allowed_columns) ? $_GET['orderby'] : 'created_at';
    $order = isset($_GET['order']) && strtolower($_GET['order']) === 'asc' ? 'ASC' : 'DESC';

    // Build the query
    $query = "SELECT * FROM $table_name WHERE 1=1";

    // Filters
    if (!empty($filter_name)) {
        $query .= $wpdb->prepare(" AND name LIKE %s", '%' . $filter_name . '%');
    }
    if (!empty($filter_email)) {
        $query .= $wpdb->prepare(" AND email LIKE %s", '%' . $filter_email . '%');
    }
    if (!empty($filter_mobile)) {
        $query .= $wpdb->prepare(" AND mobile LIKE %s", '%' . $filter_mobile . '%');
    }
    if (!empty($filter_occupation)) {
        $query .= $wpdb->prepare(" AND occupation LIKE %s", '%' . $filter_occupation . '%');
    }
    if (!empty($filter_bhk_size)) {
        $query .= $wpdb->prepare(" AND bhk_size LIKE %s", '%' . $filter_bhk_size . '%');
    }
    // if (!empty($filter_property_id)) {
    //     $query .= $wpdb->prepare(" AND property_id = %d", $filter_property_id);
    // }

    // Filter by property name
    if (!empty($filter_property_name)) {
        // Find matching property IDs by title
        $post_ids = $wpdb->get_col($wpdb->prepare(
            "SELECT ID FROM $wpdb->posts WHERE post_type='property' AND post_status='publish' AND post_title LIKE %s",
            '%' . $filter_property_name . '%'
        ));

        if (!empty($post_ids)) {
            $post_ids_placeholders = implode(',', array_fill(0, count($post_ids), '%d'));
            $query .= " AND property_id IN (" . $post_ids_placeholders . ")";
            $query = $wpdb->prepare($query, $post_ids);
        } else {
            // No properties match this name, ensure empty result
            $query .= " AND 1=0";
        }
    }

    // Filter by date range (based on created_at)
    if (!empty($filter_start_date)) {
        // Convert to proper datetime format if needed
        $query .= $wpdb->prepare(" AND created_at >= %s", $filter_start_date . ' 00:00:00');
    }
    if (!empty($filter_end_date)) {
        $query .= $wpdb->prepare(" AND created_at <= %s", $filter_end_date . ' 23:59:59');
    }

    // Order by selected column
    $query .= " ORDER BY $orderby $order";

    $results = $wpdb->get_results($query);

    // Helper function to create sortable column headers
    function sortable_header($column_key, $current_orderby, $current_order, $label) {
        $base_url = admin_url('admin.php?page=discount-coupons');
        $order = ($current_orderby === $column_key && $current_order === 'ASC') ? 'desc' : 'asc';
        $url = add_query_arg(['orderby' => $column_key, 'order' => $order], $base_url . build_query_without(['orderby','order']));
        $arrow = '';
        if ($current_orderby === $column_key) {
            $arrow = $current_order === 'ASC' ? '↑' : '↓';
        }
        return "<a href='" . esc_url($url) . "'>$label $arrow</a>";
    }

    // Function to rebuild query params without overwriting orderby/order
    function build_query_without($keys) {
        $params = $_GET;
        foreach ($keys as $key) {
            unset($params[$key]);
        }
        return '&' . http_build_query($params);
    }

    echo '<div class="wrap">';
    echo '<h1 class="wp-heading-inline">Discount Coupons</h1>';

    // Filters Form
    echo '<form method="get" action="" style="margin-bottom:20px;">';
    echo '<input type="hidden" name="page" value="discount-coupons">';
    echo '<input type="text" name="filter_name" placeholder="Name" value="' . esc_attr($filter_name) . '" style="margin-right:10px;">';
    echo '<input type="text" name="filter_email" placeholder="Email" value="' . esc_attr($filter_email) . '" style="margin-right:10px;">';
    echo '<input type="text" name="filter_mobile" placeholder="Mobile" value="' . esc_attr($filter_mobile) . '" style="margin-right:10px;">';
    echo '<input type="text" name="filter_occupation" placeholder="Occupation" value="' . esc_attr($filter_occupation) . '" style="margin-right:10px;">';
    echo '<input type="text" name="filter_bhk_size" placeholder="BHK/Size" value="' . esc_attr($filter_bhk_size) . '" style="margin-right:10px;">';
    // echo '<input type="number" name="filter_property_id" placeholder="Property ID" value="' . esc_attr($filter_property_id) . '" style="margin-right:10px;">';
    echo '<input type="text" name="filter_property_name" placeholder="Property Name" value="' . esc_attr($filter_property_name) . '" style="margin-right:10px;">';
    echo '<div></div>From <input type="date" name="filter_start_date" value="' . esc_attr($filter_start_date) . '" style="margin-right:10px;">';
    echo 'To <input type="date" name="filter_end_date" value="' . esc_attr($filter_end_date) . '" style="margin-right:10px;">';

    echo '<button type="submit" class="button button-primary">Filter</button>';
    echo '<a href="' . admin_url('admin.php?page=discount-coupons') . '" class="button button-secondary" style="margin-left:10px;">Reset</a>';
    echo '</form>';

    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead>
            <tr>
                <th>' . sortable_header('id', $orderby, $order, 'ID') . '</th>
                <th>' . sortable_header('property_id', $orderby, $order, 'Property Name') . '</th>
                <th>' . sortable_header('name', $orderby, $order, 'Name') . '</th>
                <th>' . sortable_header('mobile', $orderby, $order, 'Mobile') . '</th>
                <th>' . sortable_header('email', $orderby, $order, 'Email') . '</th>
                <th>' . sortable_header('occupation', $orderby, $order, 'Occupation') . '</th>
                <th>' . sortable_header('bhk_size', $orderby, $order, 'BHK/Size') . '</th>
                <th>' . sortable_header('coupon_code', $orderby, $order, 'Coupon Code') . '</th>
                <th>' . sortable_header('created_at', $orderby, $order, 'Created At') . '</th>
            </tr>
          </thead>
          <tbody>';

    if ($results) {
        foreach ($results as $row) {
            $property_title = get_the_title($row->property_id);
            $property_link = get_permalink($row->property_id);
            if (!$property_title) {
                $property_title = 'N/A';
            }
            if (!$property_link) {
                $property_link = '#';
            }

            echo '<tr>';
            echo '<td>' . esc_html($row->id) . '</td>';
            echo '<td>' . ($property_title !== 'N/A' ? '<a href="' . esc_url($property_link) . '" target="_blank">' . esc_html($property_title) . '</a>' : 'N/A') . '</td>';
            echo '<td>' . esc_html($row->name) . '</td>';
            echo '<td>' . esc_html($row->mobile) . '</td>';
            echo '<td>' . esc_html($row->email) . '</td>';
            echo '<td>' . esc_html($row->occupation) . '</td>';
            echo '<td>' . esc_html($row->bhk_size) . '</td>';
            echo '<td>' . esc_html($row->coupon_code) . '</td>';
            echo '<td>' . esc_html($row->created_at) . '</td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="9">No records found.</td></tr>';
    }

    echo '</tbody></table></div>';
}




?>
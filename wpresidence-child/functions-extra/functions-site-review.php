
<?php

// START - wp-admin - Site Reviews - Customization

// function add_flag_delete_column($columns) {
//     $columns['submitted_date'] = __('Submitted Date', 'text-domain');
//     $columns['flag_delete'] = __('Action', 'text-domain');
//     return $columns;
// }
// add_filter('manage_site-review_posts_columns', 'add_flag_delete_column');

// function render_flag_delete_column($column, $post_id) {
//     if ($column === 'flag_delete') {
//         // Add a link to flag the review
//         echo '<a href="' . esc_url(admin_url('admin-post.php?action=flag_review&review_id=' . $post_id)) . '" class="button button-small">' . __('Flag/Delete', 'text-domain') . '</a>';
//     }
// }
// add_action('manage_site-review_posts_custom_column', 'render_flag_delete_column', 10, 2);

// function handle_flag_review_action() {
//     if (!isset($_GET['review_id']) || !current_user_can('edit_posts')) {
//         wp_die(__('You do not have sufficient permissions to access this page.', 'text-domain'));
//     }

//     $review_id = absint($_GET['review_id']);

//     // Change visibility to private
//     wp_update_post(array(
//         'ID'          => $review_id,
//         'post_status' => 'private',
//     ));

//     // Redirect back to the reviews page
//     wp_redirect(admin_url('edit.php?post_type=site-review'));
//     exit;
// }
// add_action('admin_post_flag_review', 'handle_flag_review_action');


function customize_site_review_columns($columns) {
    // Remove all default columns
    $columns = [];

    // Add custom columns
    $columns['title'] = __('Review Title', 'text-domain'); // Review Title
    $columns['assigned_posts'] = __('Assigned Posts', 'text-domain'); // Property Name
    $columns['date'] = __('Submitted Date', 'text-domain'); // Submitted Date
    $columns['toggle_action'] = __('Action', 'text-domain'); // Action Buttons

    return $columns;
}
add_filter('manage_site-review_posts_columns', 'customize_site_review_columns');

// function custom_reviews_columns($columns) {
//     return [
//         'cb' => $columns['cb'], // Checkbox for bulk actions
//         'title' => __('Review Title', 'text-domain'),
//         'assigned_posts' => __('Assigned Posts (Property Name)', 'text-domain'),
//         'date' => __('Submitted Date', 'text-domain'),
//         'current_status' => __('Current Status', 'text-domain'),
//         'action' => __('Action', 'text-domain'),
//     ];
// }
// add_filter('manage_site-review_posts_columns', 'custom_reviews_columns');

// function custom_reviews_column_content($column, $post_id) {
//     if ($column === 'assigned_posts') {
//         // Retrieve and display the assigned property
//         $assigned_posts = get_post_meta($post_id, 'assigned_posts', true); // Adjust the meta key as needed
//         echo $assigned_posts ? esc_html($assigned_posts) : __('None', 'text-domain');
//     } elseif ($column === 'current_status') {
//         // Show current review status
//         $status = get_post_status($post_id);
//         echo $status === 'publish' ? __('Approved', 'text-domain') : __('Unapproved', 'text-domain');
//     } elseif ($column === 'action') {
//         // Add "Looks Good" / "Make Private" toggle button
//         $state = get_post_meta($post_id, '_review_state', true);
//         $state = $state === 'private' ? 'private' : 'public';
//         $label = $state === 'public' ? __('Looks Good', 'text-domain') : __('Make Private', 'text-domain');
//         $color = $state === 'public' ? '#28a745' : '#dc3545';

//         echo '<a href="' . esc_url(admin_url('admin-post.php?action=toggle_review_state&review_id=' . $post_id . '&state=' . ($state === 'public' ? 'private' : 'public'))) . '" 
//                 class="button button-small" 
//                 style="background-color: ' . esc_attr($color) . '; color: white;">' . esc_html($label) . '</a>';
//     }
// }
// add_action('manage_site-review_posts_custom_column', 'custom_reviews_column_content', 10, 2);



// ---------

// function add_review_toggle_column($columns) {
//     $columns['toggle_action'] = __('Action', 'text-domain');
//     return $columns;
// }
// add_filter('manage_site-review_posts_columns', 'add_review_toggle_column');

// function render_review_toggle_column($column, $post_id) {
//     if ($column === 'toggle_action') {
//         // Retrieve the current state
//         $state = get_post_meta($post_id, '_review_state', true);
//         $state = $state === 'private' ? 'private' : 'public';

//         // Display the appropriate button
//         if ($state === 'public') {
//             echo '<a href="' . esc_url(admin_url('admin-post.php?action=toggle_review_state&review_id=' . $post_id . '&state=private')) . '" class="button button-small" style="background-color: #28a745; color: white !important;">' . __('Mark as Flagged', 'text-domain') . '</a>';
//         } elseif ($state === 'private') {
//             echo '<a href="' . esc_url(admin_url('admin-post.php?action=toggle_review_state&review_id=' . $post_id . '&state=public')) . '" class="button button-small" style="background-color: #dc3545; color: white !important;">' . __('Flagged', 'text-domain') . '</a>';
//         } else {
//             echo ''; // Explicitly output an empty string if no state
//         }
//     }
// }
// add_action('manage_site-review_posts_custom_column', 'render_review_toggle_column', 10, 2);

function render_review_toggle_column($column, $post_id) {
    if ($column === 'toggle_action') {
        // Retrieve the current state
        $state = get_post_meta($post_id, '_review_state', true);
        $state = $state === 'private' ? 'private' : 'public';

        // Check if the current user is an admin
        $is_admin = current_user_can('administrator');
        $disabled_attr = $is_admin ? 'style="pointer-events: none; opacity: 0.6;"' : '';
        $link_attr = $is_admin ? '#' : esc_url(admin_url('admin-post.php?action=toggle_review_state&review_id=' . $post_id . '&state=' . ($state === 'public' ? 'private' : 'public')));

        // Display the appropriate button
        if ($state === 'public') {
            echo '<a href="' . $link_attr . '" class="button button-small" style="background-color: #28a745; color: white !important;" ' . $disabled_attr . '>' . __('Mark as Flagged', 'text-domain') . '</a>';
        } else {
            echo '<a href="' . $link_attr . '" class="button button-small" style="background-color: #dc3545; color: white !important;" ' . $disabled_attr . '>' . __('Flagged', 'text-domain') . '</a>';
        }
    }
}
add_action('manage_site-review_posts_custom_column', 'render_review_toggle_column', 10, 2);


function handle_review_toggle_state() {
    if (!isset($_GET['review_id'], $_GET['state']) || !current_user_can('edit_posts')) {
        wp_die(__('You do not have sufficient permissions to access this page.', 'text-domain'));
    }

    $review_id = absint($_GET['review_id']);
    $new_state = sanitize_text_field($_GET['state']);

    // Update the meta field
    if ($new_state === 'private' || $new_state === 'public') {
        update_post_meta($review_id, '_review_state', $new_state);
    }

    // Redirect back to the reviews page
    wp_redirect(admin_url('edit.php?post_type=site-review'));
    exit;
}
add_action('admin_post_toggle_review_state', 'handle_review_toggle_state');

function add_toggle_button_styles() {
    echo '<style>
        .button-small {
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 3px;
        }
        .button-small[style*="#28a745"] {
            background-color: #28a745 !important; /* Green for "Looks Good" */
        }
        .button-small[style*="#dc3545"] {
            background-color: #dc3545 !important; /* Red for "Make Private" */
        }
    </style>';
}
add_action('admin_head', 'add_toggle_button_styles');


// Add a custom filter dropdown for 'Flagged' reviews
function add_make_private_filter($views) {
    $views['make_private'] = '<a href="' . esc_url(add_query_arg('review_state', 'private')) . '">' . __('Flagged', 'text-domain') . '</a>';
    
    return $views;
}
add_filter('views_edit-site-review', 'add_make_private_filter');

// Modify the review listing query to filter by 'Flagged' state
function filter_reviews_by_private_state($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }

    // Check if the 'review_state' filter is set to 'private'
    if (isset($_GET['review_state']) && $_GET['review_state'] === 'private') {
        $query->set('meta_key', '_review_state');
        $query->set('meta_value', 'private');
    }
}
add_action('pre_get_posts', 'filter_reviews_by_private_state');

// function filter_reviews_for_contributors($query) {
//     // Check if it's the Site Reviews query and the user is logged in
//     if (is_admin() && $query->is_main_query() && current_user_can('contributor') && !current_user_can('administrator')) {
        
//         // Get the current logged-in user ID
//         $user_id = get_current_user_id();

//         // Modify the query to only show reviews for properties linked to this user
//         $query->set('meta_query', array(
//             array(
//                 'key'     => 'property_user',  // Replace with your actual custom field or relationship
//                 'value'   => $user_id,
//                 'compare' => '='
//             )
//         ));
//     }
// }
// add_action('pre_get_posts', 'filter_reviews_for_contributors');



// add_action('wp_ajax_check_review_status', 'check_review_status');
// add_action('wp_ajax_nopriv_check_review_status', 'check_review_status');

// function check_review_status() {
//     if (!is_user_logged_in()) {
//         wp_send_json_error(['message' => 'You must be logged in to collect a gift voucher.']);
//     }

//     $current_user = wp_get_current_user();
    
//     // Query for 'site-review' post type reviews by the current user
//     $args = [
//         'post_type' => 'site-review',
//         'author' => $current_user->ID,
//         'posts_per_page' => -1, // Get all reviews by the user
//         'post_status' => 'publish', // Only published reviews
//     ];

//     $reviews = get_posts($args);

//     foreach ($reviews as $review) {
//         $review_content = wp_strip_all_tags($review->post_content);
//         $word_count = str_word_count($review_content);

//         if ($word_count >= 100) {
//             wp_send_json_success();
//         }
//     }

//     wp_send_json_error(['message' => 'Please submit a review with at least 100 words to avail a gift voucher.']);
// }


// add_action('wp_ajax_check_review_status', 'check_review_status');
// add_action('wp_ajax_nopriv_check_review_status', 'check_review_status');

// function check_review_status() {
//     if (!is_user_logged_in()) {
//         wp_send_json_error(['message' => 'You must be logged in to collect a gift voucher.']);
//     }

//     // Get the property ID from the AJAX request
//     $property_id = isset($_POST['property_id']) ? intval($_POST['property_id']) : 0;

//     // if (!$property_id || get_post_type($property_id) !== 'property') { // Replace 'property' with the actual post type
//     //     wp_send_json_error(['message' => 'This action is only allowed from a property page.']);
//     // }

//     $current_user = wp_get_current_user();

//     // Query for 'site-review' post type reviews by the current user for this property
//     $args = [
//         'post_type' => 'site-review',
//         'author' => $current_user->ID,
//         'posts_per_page' => 1, // Limit to one review per property
//         'post_status' => 'publish',
//         'meta_query' => [
//             [
//                 'key' => '_submitted',
//                 'compare' => 'EXISTS', // Ensure _submitted meta key exists
//             ],
//         ],
//     ];
    
//     $reviews = get_posts($args);
    
//     if (!empty($reviews)) {
//         $review = $reviews[0]; // Since only one review is possible, take the first
    
//         $submitted_meta = get_post_meta($review->ID, '_submitted', true);
    
//         if ($submitted_meta) {
//             // Unserialize the meta value
//             $submitted_data = maybe_unserialize($submitted_meta);
    
//             // Check if 'assigned_posts' matches the property ID
//             if (isset($submitted_data['assigned_posts']) && $submitted_data['assigned_posts'] == $property_id) {
//                 $review_content = wp_strip_all_tags($review->post_content);
//                 $word_count = str_word_count($review_content);
    
//                 if ($word_count >= 100) {
//                     wp_send_json_success(['message' => 'Valid review found.']);
//                 } else {
//                     wp_send_json_error(['message' => 'Your review must have at least 100 words to avail a gift voucher.']);
//                 }
//             }
//         }
//     }
    
//     // If no matching review is found
//     wp_send_json_error(['message' => 'No review found for this property.']);
    
// }

add_action('wp_ajax_check_review_status', 'check_review_status');
add_action('wp_ajax_nopriv_check_review_status', 'check_review_status');

function check_review_status() {
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'You must be logged in to collect a gift voucher.']);
    }

    global $wpdb;

    // Get the property ID from the AJAX request
    $property_id = isset($_POST['property_id']) ? intval($_POST['property_id']) : 0;

    if (!$property_id) {
        wp_send_json_error(['message' => 'Property ID is missing or invalid.']);
    }

    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;

    // Query for 'site-review' post type reviews by the current user for this property
    $args = [
        'post_type' => 'site-review',
        'author' => $user_id,
        'posts_per_page' => 1,
        'post_status' => 'publish',
        'meta_query' => [
            [
                'key' => '_submitted',
                'compare' => 'EXISTS',
            ],
        ],
    ];

    $reviews = get_posts($args);

    if (!empty($reviews)) {
        $review = $reviews[0]; // Since only one review is possible, take the first
        $submitted_meta = get_post_meta($review->ID, '_submitted', true);

        if ($submitted_meta) {
            // Unserialize the meta value
            $submitted_data = maybe_unserialize($submitted_meta);

            // Check if 'assigned_posts' matches the property ID
            if (isset($submitted_data['assigned_posts']) && $submitted_data['assigned_posts'] == $property_id) {
                $review_content = wp_strip_all_tags($review->post_content);
                $word_count = str_word_count($review_content);

                if ($word_count >= 100) {
                    // Check if the user has already collected a voucher for this property
                    $voucher_collected = $wpdb->get_var(
                        $wpdb->prepare(
                            "SELECT COUNT(*) FROM wp_gift_vouchers WHERE user_id = %d AND property_id = %d",
                            $user_id,
                            $property_id
                        )
                    );

                    if ($voucher_collected > 0) {
                        wp_send_json_error(['message' => 'You have already collected a gift voucher for this property.']);
                    } else {
                        wp_send_json_success(['message' => 'Valid review found.']);
                    }
                } else {
                    wp_send_json_error(['message' => 'Your review must have at least 100 words to avail a gift voucher.']);
                }
            }
        }
    }

    // If no matching review is found
    wp_send_json_error(['message' => 'No review found for this property.']);
}




// function check_review_status() {
//     if (!is_user_logged_in()) {
//         wp_send_json_error(['message' => 'You must be logged in to collect a gift voucher.']);
//     }

//     $current_user = wp_get_current_user();
//     $reviews = get_comments([
//         'user_id' => $current_user->ID,
//         'post_type' => 'site-review', // Site Reviews uses a custom post type for reviews
//         'status' => 'approve',
//     ]);

//     foreach ($reviews as $review) {
//         $review_content = wp_strip_all_tags($review->comment_content);
//         $word_count = str_word_count($review_content);

//         if ($word_count >= 100) {
//             wp_send_json_success();
//         }
//     }

//     wp_send_json_error(['message' => 'Please submit a review with at least 100 words to avail a gift voucher.']);
// }


// END - wp-admin - Site Reviews - Customization
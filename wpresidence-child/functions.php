<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if ( !function_exists( 'chld_thm_cfg_locale_css' ) ):
    function chld_thm_cfg_locale_css( $uri ){
        if ( empty( $uri ) && is_rtl() && file_exists( get_template_directory() . '/rtl.css' ) )
            $uri = get_template_directory_uri() . '/rtl.css';
        return $uri;
    }
endif;
add_filter( 'locale_stylesheet_uri', 'chld_thm_cfg_locale_css' );

if ( !function_exists( 'chld_thm_cfg_parent_css' ) ):
    function chld_thm_cfg_parent_css() {
        wp_enqueue_style( 'chld_thm_cfg_parent', trailingslashit( get_template_directory_uri() ) . 'style.css', array( 'bootstrap.min','bootstrap-theme.min' ) );
    }
endif;
add_action( 'wp_enqueue_scripts', 'chld_thm_cfg_parent_css', 10 );

function disable_plugin_updates( $value ) {
    if ( isset( $value->response['site-reviews/site-reviews.php'] ) ) {
        unset( $value->response['site-reviews/site-reviews.php'] );
    }
    if ( isset( $value->response['site-reviews-authors/site-reviews-authors.php'] ) ) {
        unset( $value->response['site-reviews-authors/site-reviews-authors.php'] );
    }
    if ( isset( $value->response['site-reviews-filters/site-reviews-filters.php'] ) ) {
        unset( $value->response['site-reviews-filters/site-reviews-filters.php'] );
    }
    if ( isset( $value->response['site-reviews-forms/site-reviews-forms.php'] ) ) {
        unset( $value->response['site-reviews-forms/site-reviews-forms.php'] );
    }
    if ( isset( $value->response['site-reviews-images/site-reviews-images.php'] ) ) {
        unset( $value->response['site-reviews-images/site-reviews-images.php'] );
    }
    if ( isset( $value->response['site-reviews-notifications/site-reviews-notifications.php'] ) ) {
        unset( $value->response['site-reviews-notifications/site-reviews-notifications.php'] );
    }
    if ( isset( $value->response['site-reviews-themes/site-reviews-themes.php'] ) ) {
        unset( $value->response['site-reviews-themes/site-reviews-themes.php'] );
    }
    return $value;
}
add_filter( 'site_transient_update_plugins', 'disable_plugin_updates' );



// Path to the includes directory
$includes_dir = get_stylesheet_directory() . '/functions-extra/';

// Array of files to include
$includes_files = [
    'functions-gift-voucher.php',
    'functions-site-review.php',
    'functions-wpadmin-builder-users-menu.php',
    'functions-discount-coupon.php',
    'functions-ads.php',
    'functions-wpadmin-builder-import-properties.php',
    'functions-wpadmin-dashboard-users.php',
    'functions-wpadmin-overall.php',
    'functions-wpadmin-menu-users.php',
    'functions-wpadmin-menu-properties.php',
    'functions-wpadmin-login.php'
];

// Loop through the files and include them
foreach ($includes_files as $file) {
    $file_path = $includes_dir . $file;
    if (file_exists($file_path)) {
        require_once $file_path;
    } else {
        error_log("File not found: " . $file_path); // Log if file is missing
    }
}





function wpresidence_child_theme_scripts() {
    wp_enqueue_script('child-custom-script', // Handle name for the script
        get_stylesheet_directory_uri() . '/js/custom-script.js', // Path to the script file
        array('jquery'), // Dependencies (jQuery in this case)
        null, // Version number (null to avoid caching issues)
        true // Load the script in the footer (true) or header (false)
    );
}
add_action('wp_enqueue_scripts', 'wpresidence_child_theme_scripts');



function enqueue_sweetalert() {
    wp_enqueue_script('sweetalert', 'https://cdn.jsdelivr.net/npm/sweetalert2@11', [], null, true);
}
add_action('wp_enqueue_scripts', 'enqueue_sweetalert');



// function enqueue_gift_voucher_script_child() {
//     // Enqueue the custom script
//     wp_enqueue_script(
//         'gift-voucher-js',
//         get_stylesheet_directory_uri() . '/js/custom-script-gift-voucher-modal.js',
//         array('jquery'),
//         null,
//         true
//     );

//     // Localize the script to pass ajaxurl
//     wp_localize_script(
//         'gift-voucher-js',
//         'ajax_object',
//         array(
//             'ajaxurl' => admin_url('admin-ajax.php') // AJAX URL
//         )
//     );
// }
// add_action('wp_enqueue_scripts', 'enqueue_gift_voucher_script_child');




function enqueue_gift_voucher_script_child() {
    wp_enqueue_script(
        'gift-voucher-js',
        get_stylesheet_directory_uri() . '/js/custom-script-gift-voucher-modal.js',
        array('jquery'),
        null,
        true
    );
}
add_action('wp_enqueue_scripts', 'enqueue_gift_voucher_script_child');

function enqueue_site_reviews_script_child() {
    wp_enqueue_script(
        'site-reviews-js',
        get_stylesheet_directory_uri() . '/js/custom-script-site-reviews.js',
        array('jquery'),
        null,
        true
    );
}
add_action('wp_enqueue_scripts', 'enqueue_site_reviews_script_child');

function enqueue_google_reviews_script_child() {
    wp_enqueue_script(
        'google-reviews-js',
        get_stylesheet_directory_uri() . '/js/custom-script-google-reviews.js',
        array('jquery'),
        null,
        true
    );
}
add_action('wp_enqueue_scripts', 'enqueue_google_reviews_script_child');


function enqueue_wp_admin_custom_script_property_script_child() {
    wp_enqueue_script(
        'wp-admin-custom-script-property-js',
        get_stylesheet_directory_uri() . '/js/wp-admin-custom-script-property.js',
        array('jquery'),
        null,
        true
    );
}
add_action('admin_enqueue_scripts', 'enqueue_wp_admin_custom_script_property_script_child');

function wp_admin_custom_style() {
    wp_enqueue_style('wp-admin-custom-style', get_stylesheet_directory_uri() . '/custom-css/wp-admin-custom-style.css');
}
add_action('admin_enqueue_scripts', 'wp_admin_custom_style');


// function wpresidence_child_theme_scripts2() {
//     wp_enqueue_script('child-custom-script', // Handle name for the script
//         get_stylesheet_directory_uri() . '/js/custom-script-gift-voucher-modal.js', // Path to the script file
//         array('jquery'), // Dependencies (jQuery in this case)
//         null, // Version number (null to avoid caching issues)
//         true // Load the script in the footer (true) or header (false)
//     );
// }
// add_action('wp_enqueue_scripts', 'wpresidence_child_theme_scripts2');

// wp_enqueue_script('gift-voucher-js', get_template_directory_uri() . '/js/custom-script-gift-voucher-modal.js', array('jquery'), null, true);




include_once get_stylesheet_directory() .'/templates/functions/dashboard_functions.php';
include_once get_stylesheet_directory() .'/templates/functions/help_functions.php';
// include_once get_stylesheet_directory() .'/templates/functions/searchfunctions.php';
include_once get_stylesheet_directory() .'/templates/functions/gallery_functions.php';
include_once get_stylesheet_directory() .'/templates/functions/property-card-functions.php';

// I want to access the $post variable in my custom file. So I added like below.
// Include file and hook to a WordPress action
add_action( 'wp', function () {
    // Check if we are on a single post or page
    if ( is_singular() ) {
        include_once get_stylesheet_directory() .'/custom-design/site_review_section.php';
        include_once get_stylesheet_directory() . '/custom-design/google_review_section.php';
    }
});



// include_once get_stylesheet_directory() .'/templates/functions/custom-comments.php';
include_once get_stylesheet_directory() .'/templates/functions/dashboard_actions_functions.php';


include_once  get_stylesheet_directory() .'/templates/get_discount_voucher_modal.php';
include_once  get_stylesheet_directory() .'/templates/extra-custom/gift-voucher-modal.php';


// function include_gift_voucher_modal() {
//     include get_template_directory() . '/templates/extra-custom/gift-voucher-modal.php';
// }
// add_action('wp_footer', 'include_gift_voucher_modal');



// Remove the logout link in comment form
add_filter( 'comment_form_logged_in', '__return_empty_string' );

function wpestate_comment($comment, $args, $depth) {
    $GLOBALS['comment'] = $comment;
    switch ($comment->comment_type) :
        case 'pingback' :
        case 'trackback' :
            ?>
            <li class="post pingback">
                <p><?php esc_html_e('Pingback:', 'wpresidence'); ?> <?php comment_author_link(); ?><?php //edit_comment_link(esc_html__('Edit', 'wpresidence'), '<span class="edit-link">', '</span>'); ?></p>
            <?php
            break;
            default :
            ?>

            <li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">

            <?php
            $avatar = wpestate_get_avatar_url(get_avatar($comment, 55));
            print '<div class="blog_author_image singlepage" style="background-image: url('.esc_url($avatar).');">';
            // comment_reply_link(array_merge($args, array('reply_text' => esc_html__('Reply', 'wpresidence'), 'depth' => $depth, 'max_depth' => $args['max_depth'])));
            print'</div>';
            ?>

            <div id="comment-<?php comment_ID(); ?>" class="comment">
                <?php //edit_comment_link(esc_html__('Edit', 'wpresidence'), '<span class="edit-link">', '</span>'); ?>
                <div class="comment-meta">
                    <div class="comment-author vcard">
                        <?php
                        print '<div class="comment_name">' . get_comment_author_link().'</div>';
                        print '<span class="comment_date">'.esc_html__(' on ','wpresidence').' '. get_comment_date() . '</span>';
                        ?>
                    </div><!-- .comment-author .vcard -->

                <?php if ($comment->comment_approved == '0') : ?>
                        <em class="comment-awaiting-moderation"><?php esc_html_e('Your comment is awaiting moderation.', 'wpresidence'); ?></em>
                        <br />
                <?php endif; ?>

                </div>

                <div class="comment-content"><?php comment_text(); ?></div>
            </div><!-- #comment-## -->
            <?php
            break;
    endswitch;
}

function wpestate_property_overview_v2_CUSTOM($postID,$is_tab='',$tab_active_class=''){
    $data       =   wpestate_return_all_labels_data('overview');
    $label      =   wpestate_property_page_prepare_label( $data['label_theme_option'],$data['label_default'] );


    global $post;
    
    if($is_tab=='yes'){
        ob_start();
        include(locate_template('templates/listing_templates/property-page-templates/single-overview-section-CUSTOM.php'));
        $content=ob_get_contents();
        ob_end_clean();
        return wpestate_property_page_create_tab_item($content,$label,$data['tab_id'],$tab_active_class);
    }else{
        include(locate_template('templates/listing_templates/property-page-templates/single-overview-section-CUSTOM.php'));
    }
  
}



// ADMIN SIDE CUSTOMIZATION

// function customize_admin_menu_for_subscriber() {
//     if (current_user_can('subscriber')) {
//         // Remove all menu items except "Comments"
//         remove_menu_page('index.php');                   // Dashboard
//         remove_menu_page('upload.php');                  // Media
//         remove_menu_page('edit.php');                    // Posts
//         remove_menu_page('edit.php?post_type=estate_property');                    // Posts
//         remove_menu_page('edit.php?post_type=wpestate_invoice');                    // Posts
//         remove_menu_page('edit.php?post_type=wpestate_search');                    // Posts
//         remove_menu_page('edit.php?post_type=membership_package');                    // Posts
//         remove_menu_page('edit.php?post_type=estate_agent');                    // Posts
//         remove_menu_page('edit.php?post_type=estate_agency');                    // Posts
//         remove_menu_page('edit.php?post_type=estate_developer');                    // Posts
//         remove_menu_page('edit.php?post_type=wpestate_crm_lead');                    // Posts
//         remove_menu_page('admin.php?page=grw');                    // Posts
//         remove_menu_page('edit.php?post_type=elementor_library&tabs_group=library');                    // Posts
//         remove_menu_page('edit.php?post_type=page');     // Pages
//         remove_menu_page('edit-comments.php');           // Comments
//         remove_menu_page('themes.php');                  // Appearance
//         remove_menu_page('plugins.php');                 // Plugins
//         remove_menu_page('users.php');                   // Users
//         remove_menu_page('tools.php');                   // Tools
//         remove_menu_page('options-general.php');         // Settings
//         remove_menu_page('profile.php');                 // Profile

//         remove_menu_page('admin.php');
//         remove_menu_page('admin.php?page=vc-welcome');                 // Profile


//         // Re-add the "Comments" menu
//         add_menu_page(
//             __('Comments'), // Menu title
//             __('Comments'), // Page title
//             'read',         // Capability
//             'edit-comments.php', // Menu slug
//             '',             // Function (not needed since we're using the default page)
//             'dashicons-admin-comments', // Icon
//             25             // Position in the menu
//         );
//     }
// }
// add_action('admin_menu', 'customize_admin_menu_for_subscriber', 999999);

// function remove_all_admin_menus_for_subscriber() {
//     // Check if the current user should have their menus removed
//     if (current_user_can('subscriber')) {
//         // Get the global $menu variable that holds all admin menu items
//         global $menu;

//         // Loop through the menu array and remove each item
//         foreach ($menu as $menu_item) {
//             remove_menu_page($menu_item[2]);
//         }

//         // Re-add the "Comments" menu
//         add_menu_page(
//             __('Customer Reviews'), // Menu title
//             __('Customer Reviews'), // Page title
//             'read',         // Capability
//             'edit-comments.php', // Menu slug
//             '',             // Function (not needed since we're using the default page)
//             'dashicons-admin-comments', // Icon
//             25             // Position in the menu
//         );
//     }
// }
// add_action('admin_menu', 'remove_all_admin_menus_for_subscriber', 999);

function remove_all_admin_menus_for_subscriber() {
    // Check if the current user should have their menus removed
    if (current_user_can('subscriber')) {
        // Get the global $menu variable that holds all admin menu items
        global $menu;

        // Loop through the menu array and remove each item
        foreach ($menu as $menu_item) {
            remove_menu_page($menu_item[2]);
        }

        // Re-add the "Comments" menu
        add_menu_page(
            __('Customer Reviews'), // Menu title
            __('Customer Reviews'), // Page title
            'read',         // Capability
            'edit.php?post_type=site-review', // Menu slug
            '',             // Function (not needed since we're using the default page)
            'dashicons-admin-comments', // Icon
            25             // Position in the menu
        );
    }
}
add_action('admin_menu', 'remove_all_admin_menus_for_subscriber', 999);

function hide_admin_bar_for_subscriber() {
    // Check if the current user should have the admin bar hidden
    if (current_user_can('subscriber')) {
        // Hide the admin bar for all other users
        echo '<style>
                #wp-admin-bar-root-default #wp-admin-bar-wp-logo,
                #wp-admin-bar-root-default #wp-admin-bar-comments,
                #wp-admin-bar-root-default #wp-admin-bar-new-content,
                #wp-admin-bar-root-default #wp-admin-bar-hostinger_admin_bar,
                #wpbody .wpestate_notices, .tablenav .actions, #wpbody-content .subsubsub, #screen-meta-links, #screen-meta, #footer-thankyou, #vue-app, #wpfooter {
                    display: none !important;
                }
            </style>';
    }
}
add_action('admin_head', 'hide_admin_bar_for_subscriber', 999);



// EDITOR START
function remove_all_admin_menus_for_editor_expect_this_list() {

    // Check if the current user has the 'editor' role
    if (current_user_can('editor')) {
        global $menu;

        // Specify the slugs of the menus you want to keep for the editor
        $allowed_menus = array(
            'index.php',              // Dashboard
            // 'edit.php',               // Posts
            'upload.php',             // Media
            'edit.php?post_type=page', // Pages
            // 'edit-comments.php',       // Comments
            'edit.php?post_type=site-review',
            // 'edit.php?post_type=estate_agent',
            'edit.php?post_type=estate_property',
            // 'edit.php?post_type=site-review',
            'users.php',
            'wp_pro-welcome',
            'users.php',
            'admin.php?page=discount-coupons'
        );

        // Loop through all registered menus
        foreach ($menu as $menu_item) {
            $menu_slug = $menu_item[2];

            // Remove the menu if it's not in the allowed list
            if (!in_array($menu_slug, $allowed_menus)) {
                remove_menu_page($menu_slug);
            }
        }
    }

    // Check if the current user should have their menus removed
    // if (current_user_can('editor')) {
    //     // Get the global $menu variable that holds all admin menu items
    //     global $menu;

    //     // Loop through the menu array and remove each item
    //     foreach ($menu as $menu_item) {
    //         remove_menu_page($menu_item[2]);
    //     }

    //     // Re-add the "Comments" menu
    //     add_menu_page(
    //         __('Customer Reviews'), // Menu title
    //         __('Customer Reviews'), // Page title
    //         'read',         // Capability
    //         'edit.php?post_type=site-review', // Menu slug
    //         '',             // Function (not needed since we're using the default page)
    //         'dashicons-admin-comments', // Icon
    //         25             // Position in the menu
    //     );
    // }
}
add_action('admin_menu', 'remove_all_admin_menus_for_editor_expect_this_list', 999);

function hide_admin_bar_for_editor() {
    // Check if the current user should have the admin bar hidden
    if (current_user_can('editor')) {
        // Hide the admin bar for all other users
        echo '<style>
                #wp-admin-bar-root-default #wp-admin-bar-wp-logo,
                #wp-admin-bar-root-default #wp-admin-bar-comments,
                #wp-admin-bar-root-default #wp-admin-bar-new-content,
                #wp-admin-bar-root-default #wp-admin-bar-hostinger_admin_bar,
                #wpbody .wpestate_notices,
                .tablenav .actions,
                #wpbody-content .subsubsub,
                #screen-meta-links,
                #screen-meta,
                #footer-thankyou,
                #vue-app,
                #wpfooter,
                #wp-admin-bar-wpforms-menu,
                #wp-admin-bar-monsterinsights_frontend_button,
                #wp-admin-bar-updates,
                #wp-admin-bar-aioseo-main {
                    display: none !important;
                }
            </style>';
    }
}
add_action('admin_head', 'hide_admin_bar_for_editor', 999);

// function hide_submenu_items_by_text_for_editor() {
//     if (current_user_can('editor')) {
//         ?-->
//         <script type="text/javascript">
//             jQuery(document).ready(function($) {
//                 // Reusable function to hide sub-menu items based on text value
//                 function hideSubMenuItems(menuSelector, itemsToHide) {
//                     $.each(itemsToHide, function(index, itemText) {
//                         $(menuSelector + ' ul.wp-submenu li a').filter(function() {
//                             return $(this).text() === itemText;
//                         }).parent().hide(); // Hide the <li> element
//                     });
//                 }

//                 // Array of sub-menu item text values to hide for 'Menu dashboard'
//                 var menuDashboard_SubmenuItemsToHide = [
//                     'Updates',
//                     'Site Reviews',
//                     'Insights',
//                     'SEO Statistics'
//                 ];
//                 $('#menu-dashboard ul.wp-submenu li a').filter(function() {
//                     return $(this).text().includes('Updates');
//                 }).parent().hide(); // Hide the <li> element

//                 // Array of sub-menu item text values to hide for 'estate_property'
//                 var property_SubmenuItemsToHide = [
//                     // 'Properties',
//                     'Add New Property',
//                     'Categories',
//                     'Type',
//                     'City',
//                     'Neighborhood',
//                     'County / State',
//                     'Features & Amenities',
//                     'Property Status'
//                 ];

//                 // Array of sub-menu item text values to hide for 'pages'
//                 var page_SubmenuItemsToHide = [
//                     // 'Pages',
//                     'Add New Page'
//                 ];

//                 // Array of sub-menu item text values to hide for 'Site Review'
//                 var siteReview_SubmenuItemsToHide = [
//                     'All Forms',
//                     'All Themes',
//                     'Add New Post',
//                     'Categories',
//                     'Settings',
//                     'Tools',
//                     'Addons',
//                     'Help & Support'
//                 ];

//                 // Array of sub-menu item text values to hide for 'Google Review'
//                 var googleReview_SubmenuItemsToHide = [
//                     'Review Funnels',
//                     'Review List',
//                     'Templates',
//                     'Badges',
//                     'Forms',
//                     'Floats',
//                     'Analytics',
//                     'Tools',
//                     'Help Forum',
//                     'Affiliation',
//                     'Account',
//                     'Contact Us'
//                 ];

//                 // Call the reusable function for different menus
//                 hideSubMenuItems('#menu-dashboard', menuDashboard_SubmenuItemsToHide);
//                 hideSubMenuItems('#menu-posts-estate_property', property_SubmenuItemsToHide);
//                 hideSubMenuItems('#menu-pages', page_SubmenuItemsToHide);
//                 hideSubMenuItems('#menu-posts-site-review', siteReview_SubmenuItemsToHide);
//                 hideSubMenuItems('#toplevel_page_wp_pro-welcome', googleReview_SubmenuItemsToHide);
//             });
//         </script>

//         <?php
//     }
// }
// add_action('admin_footer', 'hide_submenu_items_by_text_for_editor');
// EDITOR END

// function restrict_admin_pages_for_subscriber() {
//     if (current_user_can('subscriber')) {
//         global $pagenow;
        
//         // Allow access to the "Comments" page and related comment actions
//         $allowed_pages = array(
//             'edit-comments.php',        // Comments page
//             'comment.php',              // Edit single comment
//             'comment-reply.php',        // Reply to a comment (if used)
//             'edit.php?post_type=comment', // Custom post type comment management (if applicable)
//             'admin-ajax.php'
//         );

//         if (!in_array($pagenow, $allowed_pages)) {
//             // Allow query parameters to be checked as well
//             if (isset($_GET['action']) && $_GET['action'] === 'editcomment' && $pagenow === 'comment.php') {
//                 return;
//             }
//             wp_redirect(admin_url('edit-comments.php'));
//             exit;
//         }
//     }
// }
// add_action('admin_init', 'restrict_admin_pages_for_subscriber');



// function limit_comment_actions_for_subscriber($actions, $comment) {
//     if (current_user_can('subscriber')) {
//         // Allow only the "Reply" action
//         $allowed_actions = array('reply');
//         foreach ($actions as $action => $link) {
//             if (!in_array($action, $allowed_actions)) {
//                 unset($actions[$action]);
//             }
//         }
//     }
//     return $actions;
// }
// add_filter('comment_row_actions', 'limit_comment_actions_for_subscriber', 10, 2);

function limit_comment_actions_for_subscriber($actions, $comment) {
    if (current_user_can('subscriber')) {
        // Only allow "Reply" for top-level comments (comments with no parent)
        if ($comment->comment_parent == 0) {
            $allowed_actions = array('reply');
        } else {
            $allowed_actions = array('edit', 'trash');
        }

        // Remove all other actions
        foreach ($actions as $action => $link) {
            if (!in_array($action, $allowed_actions)) {
                unset($actions[$action]);
            }
        }
    }
    return $actions;
}
add_filter('comment_row_actions', 'limit_comment_actions_for_subscriber', 10, 2);


// function filter_comments_by_user($query) {
//     // if (is_admin() && $query->is_main_query() && current_user_can('edit_posts')) {
//     if (current_user_can('subscriber')) {
//         global $current_user;
//         wp_get_current_user();

//         // Replace 'meta_key' with the actual meta key associated with the user's page
//         // $page_id = get_user_meta($current_user->ID, 'meta_key', true);
//         $page_id = 139;

//         if ($page_id) {
//             $query->query_vars['post_id'] = 139;
//         } else {
//             $query->query_vars['post__in'] = [0]; // No comments if the user doesn't have a page
//         }
//     }
// }
// add_action('pre_get_comments', 'filter_comments_by_user');

function filter_comments_by_user($query) {
    if (current_user_can('subscriber')) {
        global $current_user;
        wp_get_current_user();

        // Step 1: Get all pages associated with the specific user
        $args = array(
            'post_type'   => 'estate_property', // Change this if you're using a custom post type
            'meta_key'    => 'property_user', // Assuming you store the user ID in a custom field 'user_id'
            'meta_value'  => $current_user->ID,
            'numberposts' => -1, // Get all pages
            'fields'      => 'ids', // We only need the IDs
        );
        $page_ids = get_posts($args);

        // Step 2: Filter comments based on page IDs
        if (!empty($page_ids)) {
            $query->query_vars['post__in'] = $page_ids; // Only include comments for these pages
        } else {
            $query->query_vars['post__in'] = [0]; // No comments if the user doesn't have a page
        }
    }
}
add_action('pre_get_comments', 'filter_comments_by_user');

// DONT DELETE - GOOGLE VERIFICATION - Function to add Google site verification meta tag
function add_google_site_verification() {
    echo '<meta name="google-site-verification" content="C1Z60P3tyubamTxQ2VGEnqvpDT3o0eioOv11GsL0Fcs" />' . "\n";
}

// Hook the function to wp_head action
add_action('wp_head', 'add_google_site_verification');


// Add application configuration variables to the header for JavaScript usage
function add_application_config() {
    // Get the current post ID; if not available, set it to null
    $post_id = get_the_ID() ? get_the_ID() : 'null';

    // Output the configuration as JavaScript variables
    ?>
    <script type="text/javascript">
        var isUserLoggedIn = <?php echo json_encode(is_user_logged_in()); ?>;
        var postId = <?php echo $post_id; ?>;
    </script>
    <?php
}

// Hook the function to wp_head action
add_action('wp_head', 'add_application_config');



// Mobile OTP - START

// Add the action for logged-in and non-logged-in users
add_action('wp_ajax_nopriv_send_otp', 'send_otp_function');
add_action('wp_ajax_send_otp', 'send_otp_function');

function send_otp_function() {
    // Your MSG91 API Key
    $apiKey = 'eMnc72mJyDagrdNSIuAzRQVt9bjpo3fkCq64liGFKU1BWZ0TY8EhlZc1m3jsxQUpJSPkdbwOB4utVYXn';
    // Get the mobile number from the AJAX request
    $mobile = sanitize_text_field($_POST['mobile']);
    // Generate a random OTP
    $otp = rand(100000, 999999);

    $fields = array(
        "variables_values" => $otp,
        "route" => "otp",
        "numbers" => $mobile,
    );
    
    $curl = curl_init();
    
    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://www.fast2sms.com/dev/bulkV2",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_SSL_VERIFYHOST => 0,
      CURLOPT_SSL_VERIFYPEER => 0,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => json_encode($fields),
      CURLOPT_HTTPHEADER => array(
        "authorization: " . $apiKey,
        "accept: */*",
        "cache-control: no-cache",
        "content-type: application/json"
      ),
    ));
    
    $response = curl_exec($curl);
    // $err = curl_error($curl);
    
    curl_close($curl);
    
    // if ($err) {
    //   echo "cURL Error #:" . $err;
    // } else {
    //   echo $response;
    // }

    $responseData = json_decode($response, true);

    // echo $responseData;

    // Check the response status
    if ($responseData['return'] == true) {
        // Store OTP temporarily in session (or database if preferred)
        $_SESSION['otp'] = $otp;
        wp_send_json_success('OTP sent successfully.');
    } else {
        // wp_send_json_error('Error sending OTP: ' . $responseData['message']);
        wp_send_json_error('OTP sent failed.');
    }

    wp_die(); // Always terminate AJAX functions with wp_die()
}


// Start the session if not already started
// if (session_status() === PHP_SESSION_NONE) {
//     session_start();
// }

// Add action for logged-in and non-logged-in users
add_action('wp_ajax_nopriv_verify_otp', 'verify_otp_function');
add_action('wp_ajax_verify_otp', 'verify_otp_function');

function verify_otp_function() {
    // Get the OTP entered by the user
    $entered_otp = sanitize_text_field($_POST['otp']);

    // echo '$entered_otp = ' . $entered_otp;

    // Get the OTP stored in the session
    $stored_otp = isset($_SESSION['otp']) ? $_SESSION['otp'] : null;

    // echo '$stored_otp = ' . $stored_otp;

    // Check if the entered OTP matches the stored OTP
    if ($entered_otp == $stored_otp) {
        // Clear OTP from session after successful verification
        unset($_SESSION['otp']);
        wp_send_json_success('OTP verified successfully.');
    } else {
        wp_send_json_error('Invalid OTP. Please try again.' . $_SESSION['otp']);
    }

    wp_die(); // Always end the AJAX function with wp_die()
}


// Mobile OTP - END


// ADMIN DASHBOARD START

function full_screen_welcome_message() {
    // Only show the welcome message on the main dashboard page
    $screen = get_current_screen();
    if ($screen->base === 'dashboard') {
        echo '
        <div class="custom-fullscreen-welcome">
            <div class="welcome-content">
                <h1>Welcome to Home Reviewz Admin Dashboard!</h1>
                <p>We are excited to have you here. Explore the admin panel for site management and updates.</p>
                <a href="#" class="custom-button">Start Exploring</a>
            </div>
        </div>';
    }
}

function full_screen_welcome_styles() {
    // Custom CSS for full-screen welcome message
    echo '
    <style>
        .custom-fullscreen-welcome {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background-color: #f1f1f1;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999; /* Ensure it is above everything else */
        }
        .welcome-content {
            text-align: center;
            background-color: #ffffff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0px 5px 15px rgba(0,0,0,0.2);
        }
        .welcome-content h1 {
            line-height: 1.2;
            font-size: 3em;
            color: #007cba;
            margin-bottom: 20px;
        }
        .welcome-content p {
            font-size: 1.5em;
            color: #333;
        }
        .welcome-content a, .welcome-content a:hover, .welcome-content a:active {
            color: #fff !important;
        }
        .custom-button {
            display: inline-block;
            padding: 15px 30px;
            margin-top: 25px;
            background-color: #007cba;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-size: 1.2em;
            transition: background-color 0.3s ease;
        }
    </style>';
}

// Hook to inject the custom welcome message and styles
add_action('in_admin_header', 'full_screen_welcome_message');
add_action('admin_head', 'full_screen_welcome_styles');


// ADMIN DASHBOARD END


// CUSTOM START RATE DESIGN - START



function custom_consolidated_star_rating_shortcode($atts) {
    // Default value (0), but will be updated by the JavaScript if available in the HTML
    $post = get_post();
    $ratingInfo = glsr_get_ratings([
        'assigned_posts' => $post->ID,
    ]);
    $siteReviewRating = isset($ratingInfo['average']) ? $ratingInfo['average'] : 0;
    ob_start();
    ?>
    <div id="custom-consolidated-star-rating" class="glsr glsr-default glsr-ltr" data-shortcode="site_reviews_summary" data-assigned_posts="139" data-class="show-only-rating-star" data-id="glsr_e9e2ab85" data-filters="true">
        <div class="glsr-summary-wrap">
            <div class="glsr-summary show-only-rating-star">
                <span class="custom-glsr-rating-title">Overall Rating</span>
                <div class="glsr-summary-stars">
                    <div class="glsr-star-rating glsr-stars" data-rating="0" data-reviews="0">
                        <span class="screen-reader-text">Rated <strong>0</strong> out of 5</span>
                        <span class="glsr-star glsr-star-full" aria-hidden="true"></span>
                        <span class="glsr-star glsr-star-full" aria-hidden="true"></span>
                        <span class="glsr-star glsr-star-full" aria-hidden="true"></span>
                        <span class="glsr-star glsr-star-half" aria-hidden="true"></span>
                        <span class="glsr-star glsr-star-empty" aria-hidden="true"></span>
                    </div>
                </div>
                <div class="glsr-summary-rating"><span class="glsr-tag-value">0</span></div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Get the rating value from the wprev_avgrevs element
        const siteReviewRating = <?php echo json_encode($siteReviewRating); ?>;
        const avgRatingElement = document.querySelector('.wprev_avgrevs');
        const googleRating = avgRatingElement ? parseFloat(avgRatingElement.textContent) : 0;

        var avgRating;
        if (siteReviewRating == 0 || googleRating == 0) {
            avgRating = siteReviewRating + googleRating;
        } else {
            avgRating = (siteReviewRating + googleRating) / 2;
        }
        // const avgRating = (siteReviewRating + googleRating) / 2;

        // Select the star rating container and update its data attribute
        const ratingContainer = document.querySelector('#custom-consolidated-star-rating .glsr-star-rating');
        ratingContainer.setAttribute('data-rating', avgRating);
        const stars = ratingContainer.querySelectorAll('.glsr-star');

        // Calculate full stars, half stars, and empty stars
        const fullStars = Math.floor(avgRating);
        const halfStar = avgRating % 1 >= 0.5 ? 1 : 0;

        // Apply classes based on rating
        stars.forEach((star, index) => {
            if (index < fullStars) {
                star.classList.add('glsr-star-full');
                star.classList.remove('glsr-star-half', 'glsr-star-empty');
            } else if (index === fullStars && halfStar) {
                star.classList.add('glsr-star-half');
                star.classList.remove('glsr-star-full', 'glsr-star-empty');
            } else {
                star.classList.add('glsr-star-empty');
                star.classList.remove('glsr-star-full', 'glsr-star-half');
            }
        });

        // Update the rating value text
        document.querySelector('#custom-consolidated-star-rating .glsr-summary-rating .glsr-tag-value').innerText = avgRating.toFixed(1);
    });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('custom_consolidated_star_rating', 'custom_consolidated_star_rating_shortcode');


function custom_home_review_star_rating_shortcode($atts) {
    // Default value (0), but will be updated by the JavaScript if available in the HTML
    $post = get_post();
    $ratingInfo = glsr_get_ratings([
        'assigned_posts' => $post->ID,
    ]);
    $siteReviewRating = isset($ratingInfo['average']) ? $ratingInfo['average'] : 0;
    ob_start();
    ?>
    <div id="custom-home-review-star-rating" class="glsr glsr-default glsr-ltr" data-shortcode="site_reviews_summary" data-assigned_posts="139" data-class="show-only-rating-star" data-id="glsr_e9e2ab85" data-filters="true">
        <div class="glsr-summary-wrap">
            <div class="glsr-summary show-only-rating-star">
            <!-- <span class="custom-glsr-rating-title">Home Reviewz Rating</span> -->
                <div class="glsr-summary-stars">
                    <div class="glsr-star-rating glsr-stars" data-rating="0" data-reviews="0">
                        <span class="screen-reader-text">Rated <strong>0</strong> out of 5</span>
                        <span class="glsr-star glsr-star-full" aria-hidden="true"></span>
                        <span class="glsr-star glsr-star-full" aria-hidden="true"></span>
                        <span class="glsr-star glsr-star-full" aria-hidden="true"></span>
                        <span class="glsr-star glsr-star-half" aria-hidden="true"></span>
                        <span class="glsr-star glsr-star-empty" aria-hidden="true"></span>
                    </div>
                </div>
                <div class="glsr-summary-rating"><span class="glsr-tag-value">0</span></div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Get the rating value from the wprev_avgrevs element
        const siteReviewRating = <?php echo json_encode($siteReviewRating); ?>;
        const avgRating = siteReviewRating;

        // Select the star rating container and update its data attribute
        const ratingContainer = document.querySelector('#custom-home-review-star-rating .glsr-star-rating');
        ratingContainer.setAttribute('data-rating', avgRating);
        const stars = ratingContainer.querySelectorAll('.glsr-star');

        // Calculate full stars, half stars, and empty stars
        const fullStars = Math.floor(avgRating);
        const halfStar = avgRating % 1 >= 0.5 ? 1 : 0;

        // Apply classes based on rating
        stars.forEach((star, index) => {
            if (index < fullStars) {
                star.classList.add('glsr-star-full');
                star.classList.remove('glsr-star-half', 'glsr-star-empty');
            } else if (index === fullStars && halfStar) {
                star.classList.add('glsr-star-half');
                star.classList.remove('glsr-star-full', 'glsr-star-empty');
            } else {
                star.classList.add('glsr-star-empty');
                star.classList.remove('glsr-star-full', 'glsr-star-half');
            }
        });

        // Update the rating value text
        document.querySelector('#custom-home-review-star-rating .glsr-summary-rating .glsr-tag-value').innerText = avgRating.toFixed(1);
    });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('custom_home_review_star_rating', 'custom_home_review_star_rating_shortcode');


function custom_google_review_star_rating_shortcode($atts) {
    // Default value (0), but will be updated by the JavaScript if available in the HTML
    $post = get_post();
    $ratingInfo = glsr_get_ratings([
        'assigned_posts' => $post->ID,
    ]);
    $siteReviewRating = isset($ratingInfo['average']) ? $ratingInfo['average'] : 0;
    ob_start();
    ?>
    <div id="custom-google-review-star-rating" class="glsr glsr-default glsr-ltr" data-shortcode="site_reviews_summary" data-assigned_posts="139" data-class="show-only-rating-star" data-id="glsr_e9e2ab85" data-filters="true">
        <div class="glsr-summary-wrap">
            <div class="glsr-summary show-only-rating-star">
                <!-- <span class="custom-glsr-rating-title">Overall Rating</span>  -->
                <div class="glsr-summary-stars">
                    <div class="glsr-star-rating glsr-stars" data-rating="0" data-reviews="0">
                        <span class="screen-reader-text">Rated <strong>0</strong> out of 5</span>
                        <span class="glsr-star glsr-star-full" aria-hidden="true"></span>
                        <span class="glsr-star glsr-star-full" aria-hidden="true"></span>
                        <span class="glsr-star glsr-star-full" aria-hidden="true"></span>
                        <span class="glsr-star glsr-star-half" aria-hidden="true"></span>
                        <span class="glsr-star glsr-star-empty" aria-hidden="true"></span>
                    </div>
                </div>
                <div class="glsr-summary-rating"><span class="glsr-tag-value">0</span></div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Get the rating value from the wprev_avgrevs element
        const avgRatingElement = document.querySelector('.wprev_avgrevs');
        const googleRating = avgRatingElement ? parseFloat(avgRatingElement.textContent) : 0;
        const avgRating = googleRating;

        // Select the star rating container and update its data attribute
        const ratingContainer = document.querySelector('#custom-google-review-star-rating .glsr-star-rating');
        ratingContainer.setAttribute('data-rating', avgRating);
        const stars = ratingContainer.querySelectorAll('.glsr-star');

        // Calculate full stars, half stars, and empty stars
        const fullStars = Math.floor(avgRating);
        const halfStar = avgRating % 1 >= 0.5 ? 1 : 0;

        // Apply classes based on rating
        stars.forEach((star, index) => {
            if (index < fullStars) {
                star.classList.add('glsr-star-full');
                star.classList.remove('glsr-star-half', 'glsr-star-empty');
            } else if (index === fullStars && halfStar) {
                star.classList.add('glsr-star-half');
                star.classList.remove('glsr-star-full', 'glsr-star-empty');
            } else {
                star.classList.add('glsr-star-empty');
                star.classList.remove('glsr-star-full', 'glsr-star-half');
            }
        });

        // Update the rating value text
        document.querySelector('#custom-google-review-star-rating .glsr-summary-rating .glsr-tag-value').innerText = avgRating.toFixed(1);
    });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('custom_google_review_star_rating', 'custom_google_review_star_rating_shortcode');

// CUSTOM START RATE DESIGN - END

// CUSTOM TOP RATED PROPERTY SECTION IN HOME PAGE - START

// add_filter('wpresidence_property_filter_options', function($filter_options) {
//     $filter_options['top_rated'] = [
//         'label' => 'Top Rated', // Label for the filter
//         'type' => 'meta',       // Specify that it’s a meta field
//         'meta_key' => 'top-rated', // Custom field name
//         'meta_value' => 'true', // The value to filter by
//     ];

//     return $filter_options;
// });


// add_shortcode('custom_wp_residence_items_list', function($atts) {
//     // Set default parameters
//     $atts = shortcode_atts([
//         'posts_per_page' => 6, // Default number of properties per page
//         'meta_key' => 'top-rated', // Custom field to filter properties
//         'meta_value' => 'true', // Value to match for filtering (properties marked as "top-rated")
//     ], $atts);

//     // Query to get properties based on custom field "top-rated"
//     $args = [
//         'post_type'      => 'estate_property', // Assuming "estate_property" is the post type
//         'posts_per_page' => $atts['posts_per_page'],
//         'meta_key'       => $atts['meta_key'], // Filter by custom field key
//         'meta_value'     => $atts['meta_value'], // Filter by custom field value
//     ];

//     $query = new WP_Query($args);

//     ob_start();
//     if ($query->have_posts()) {
//         while ($query->have_posts()) {
//             $query->the_post();
//             // Output the property list item (use the same template part as in "WpResidence Items List")
//             get_template_part('templates/property_unit');
//         }
//     } else {
//         echo '<p>No top-rated properties found.</p>';
//     }

//     wp_reset_postdata();
//     return ob_get_clean();
// });


add_shortcode('estate_property_list', function ($atts) {
    $atts = shortcode_atts([
        'meta_key'   => '',
        'meta_value' => '',
    ], $atts);

    $args = [
        'post_type'      => 'estate_property',
        'meta_query'     => [
            [
                'key'     => $atts['meta_key'],
                'value'   => $atts['meta_value'],
                'compare' => '='
            ]
        ],
        'posts_per_page' => 6, // Limit to 2 rows x 3 columns
    ];

    $query = new WP_Query($args);

    ob_start();
    if ($query->have_posts()) {
        echo '<div class="property-grid-wrapper" style="display: flex; flex-wrap: wrap;">';

        // Define the number of columns
        $wpestate_no_listins_per_row = 4;

        // Pass variables to the template
        set_query_var('wpestate_no_listins_per_row', $wpestate_no_listins_per_row);
        set_query_var('wpestate_uset_unit', '3');


        while ($query->have_posts()) {
            $query->the_post();

            // Each property unit wrapped in a column
            // echo '<div class="property-column" style="flex: 1 1 calc(33.33% - 10px); margin: 5px;">';

            // Include the template part
            get_template_part('templates/property_unit'); // Adjust path if needed

            // echo '</div>';
        }

        echo '</div>'; // Close the grid wrapper
    } else {
        echo '<p>No properties found.</p>';
    }
    wp_reset_postdata();

    return ob_get_clean();
});

// CUSTOM TOP RATED PROPERTY SECTION IN HOME PAGE - END







// $summary = glsr_get_summary([
//     'assigned_posts' => get_the_ID(), // For example, get_the_ID() if in a single property template
// ]);
// echo $summary;
// $overall_rating = $summary['rating'];
// echo $overall_rating;

add_action('plugins_loaded', 'my_rating_function');

function my_rating_function() {
    echo "=======================================";
    echo "=======================================";
    echo "=======================================";
    echo "=======================================";
    echo "=======================================";
    echo "=======================================";

    $summary = glsr_get_summary([
    'assigned_posts' => get_the_ID(), // For example, get_the_ID() if in a single property template
]);

echo "=======================================";
echo "=======================================";
echo "=======================================";
echo "=======================================";
echo "=======================================";
echo "=======================================";
highlight_string("<?php\n" . var_export($summary, true) . ";\n?>");

    if (function_exists('glsr_get_summary')) {
        $property_id = get_the_ID(); // If this is a single property template, or retrieve the ID as needed
        $summary = glsr_get_summary(['assigned_posts' => $property_id]);
        $overall_rating = $summary['rating'];
        // Do something with $overall_rating, such as storing it in a global, printing it, etc.
    }
}




// END ENQUEUE PARENT ACTION

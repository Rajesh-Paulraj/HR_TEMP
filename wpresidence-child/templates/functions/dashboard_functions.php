<?php

/*
 * Generate Dasboard menu
 *
 */
function wpestate_generate_user_menu($place = '') {
    $current_user           = wp_get_current_user();
    $userID                 = $current_user->ID;
    $user_login             = $current_user->user_login;
    $user_agent_id          = intval(get_user_meta($userID, 'user_agent_id', true));
    $home_url               = esc_url(home_url('/'));
    $no_unread              = intval(get_user_meta($userID, 'unread_mess', true));
  
    global $post;
    $current_page_template='';
    if(isset($post->ID)){
        $current_page_template = get_post_meta( $post->ID, '_wp_page_template', true );
    }

    // $values_dropdown_property_status = array(
    //     0 => array(
    //         'label' => esc_html__('All', 'wpresidence'),
    //         'value' => 0
    //     ),
    //     1 => array(
    //         'label' => esc_html__('Published', 'wpresidence'),
    //         'value' => 1
    //     ),
    //     2 => array(
    //         'label' => esc_html__('Disabled', 'wpresidence'),
    //         'value' => 2
    //     ),
    //     3 => array(
    //         'label' => esc_html__('Expired', 'wpresidence'),
    //         'value' => 3
    //     ),
    //     4 => array(
    //         'label' => esc_html__('Draft', 'wpresidence'),
    //         'value' => 4
    //     ),
    //     5 => array(
    //         'label' => esc_html__('Waiting for approval', 'wpresidence'),
    //         'value' => 5
    //     ),
    // );


    $values_crm_dropdown = array(
        0 => array(
            'label' => esc_html__('Leads', 'wpresidence'),
            'value' => 0,
            'link' => 'wpestate-crm-dashboard_leads.php'
        ),
        1 => array(
            'label' => esc_html__('Contacts', 'wpresidence'),
            'value' => 1,
            'link' => 'wpestate-crm-dashboard_contacts.php'
        ),
    );
  
    // 0, 1 - Consider Normal user. 2, 3 - Not Used. 4 - Builder user. 5 - Normal Admin user
      $dashboard_pages = array(
        // 'user_dashboard_main.php' => array(
        //     'icon' => 'dashboard.svg',
        //     'label' => esc_html__('Dashboard', 'wpresidence')
        // ),
        'user_dashboard_profile.php' => array(
            'icon' => 'profile.svg',
            'label' => esc_html__('My Profile', 'wpresidence')
        ),
        // 'user_dashboard.php' => array(
        //     'icon' => 'listings.svg',
        //     'label' => esc_html__('My Properties List', 'wpresidence')
        // ),
        // 'user_dashboard_add.php' => array(
        //     'icon' => 'plus.svg',
        //     'label' => esc_html__('Add New Property', 'wpresidence')
        // ),
        // 'user_dashboard_favorite.php' => array(
        //     'icon' => 'heart.svg',
        //     'label' => esc_html__('Favorites', 'wpresidence')
        // ),
        // 'user_dashboard_searches.php' => array(
        //     'icon' => 'search.svg',
        //     'label' => esc_html__('Saved Searches', 'wpresidence')
        // ),
        // 'user_dashboard_invoices.php' => array(
        //     'icon' => 'invoices.svg',
        //     'label' => esc_html__('My Invoices', 'wpresidence')
        // ),
        // 'user_dashboard_add_agent.php' => array(
        //     'icon' => 'addagent.svg',
        //     'label' => esc_html__('Add New Agent', 'wpresidence'),
        // ),
        // 'user_dashboard_agent_list.php' => array(
        //     'icon' => 'agents.svg',
        //     'label' => esc_html__('Agent List', 'wpresidence'),
        // ),
        // 'user_dashboard_inbox.php' => array(
        //     'icon' => 'message.svg',
        //     'label' => esc_html__('Inbox', 'wpresidence') . '<div class="unread_mess">' . intval($no_unread) . '</div>',
        // ),
        // 'user_dashboard_showing.php' => array(
        //     'icon' => 'dashboard.svg',
        //     'label' => esc_html__('Dashboard', 'wpresidence')
        // ),
        // 'wpestate-crm-dashboard.php' => array(
        //     'icon' => 'crm.svg',
        //     'label' => esc_html__('CRM', 'wpresidence')
        // ),
    );


    if(!function_exists('wpestate_crm_top_level_menu')){
        unset($dashboard_pages['wpestate-crm-dashboard.php']);
    }


    $no_unread = intval(get_user_meta($userID, 'unread_mess', true));
    $user_role = intval(get_user_meta($userID, 'user_estate_role', true));

    if ($user_role == 0 || $user_role == 1) {
        $dashboard_pages['user_dashboard_favorite.php'] = array(
            'icon' => 'heart.svg',
            'label' => esc_html__('Favorites', 'wpresidence')
        );
    }

    if ($user_role == 4) {
        // unset($dashboard_pages['user_dashboard_profile.php']);
        $dashboard_pages['user_dashboard_customer_comments.php'] = array(
            'icon' => 'plus.svg',
            'label' => esc_html__('Customer Reviews', 'wpresidence')
        );
        $dashboard_pages['user_dashboard.php'] = array(
            'icon' => 'listings.svg',
            'label' => esc_html__('Brand Listing', 'wpresidence')
        );
    }

    if ($user_role == 5) {
        // unset($dashboard_pages['user_dashboard_profile.php']);
        $dashboard_pages['user_dashboard.php'] = array(
            'icon' => 'listings.svg',
            'label' => esc_html__('My Properties List', 'wpresidence')
        );
        $dashboard_pages['user_dashboard_add.php'] = array(
            'icon' => 'plus.svg',
            'label' => esc_html__('Add New Property', 'wpresidence')
        );
    }

    // if ($user_role != 3 && $user_role != 4) {
    //     unset($dashboard_pages['user_dashboard_agent_list.php']);
    //     unset($dashboard_pages['user_dashboard_add_agent.php']);
    // }



    foreach ($dashboard_pages as $page => $details):
        $active_class = '';
        $template_link = wpestate_get_template_link($page);
        if ( $current_page_template == $page ) {
            $active_class = 'user_tab_active';
        }
        if ($page == 'wpestate-crm-dashboard.php' && 
            ( $current_page_template == 'wpestate-crm-dashboard_contacts.php' || $current_page_template == 'wpestate-crm-dashboard_leads.php')) {
            $active_class = 'user_tab_active';
        }



        if ($template_link != $home_url && $template_link != '' && wpestate_check_user_permission_on_dashboard(str_replace('.php', '', $page))) {
            if (wpestate_check_user_agent_id($user_agent_id)) {
                if (wpestate_check_user_role_menu($user_role, $user_agent_id)) {
                    if ($place == 'top' && $details['label'] === esc_html__('Logout', 'wpresidence')) {
                        print '<li role="presentation" class="divider"></li>';
                    }
                    ?>


                    <li role="presentation" class="<?php print esc_attr($active_class) . '_list';
                    print ' ' . esc_attr(str_replace('.php', '', $page)) . ' ' . 'user_role_' . esc_attr($user_role); ?>">
                        <a href="<?php print esc_url($template_link); ?>" class="<?php print esc_attr($active_class); ?>" >
                        <?php
                        include(locate_template('templates/dashboard-templates/dashboard-icons/' . $details['icon']));
                        print trim($details['label']);
                        ?>
                        </a>

                        <?php
                        if ($page == 'user_dashboard.php') {

                            $status = '';
                            if (isset($_GET['status'])) {
                                $status = intval($_GET['status']);
                            }
                            ?>
                            <!-- <ul class="secondary_menu_sidebar ">
                                <--?php
                                foreach ($values_dropdown_property_status as $key => $item) {
                                    $selected_class = '';
                                    if ($status == $item['value']) {
                                        $selected_class = " secondary_select ";
                                    }
                                    ?>
                                    <li>
                                        <a class="dashboad-tooltip <--?php echo esc_attr($selected_class); ?>" href="<--?php print esc_url_raw(add_query_arg('status', $item['value'], '')); ?>">
                                <--?php echo ' - ' . esc_html($item['label']); ?>
                                        </a>
                                    </li>
                            <--?php } ?>
                            </ul> -->

                        <?php
                        } else if ( $page == 'wpestate-crm-dashboard.php' || 
                                    $current_page_template == 'wpestate-crm-dashboard_contacts.php' || 
                                    $current_page_template == 'wpestate-crm-dashboard_leads.php') {


                            $status = '';
                            if (isset($_GET['actions'])) {
                                $status = intval($_GET['actions']);
                            }
                            ?>

                            <ul class="secondary_menu_sidebar ">
                                <?php
                                foreach ($values_crm_dropdown as $key => $item) {
                                    $selected_class = '';
                                    if ($status == $item['value']) {
                                        $selected_class = " secondary_select ";
                                    }
                                    $base_crm = wpestate_get_template_link('wpestate-crm-dashboard.php');
                                    ?>
                                    <li>
                                        <a class="dashboad-tooltip <?php echo esc_attr($selected_class); ?>" href="<?php print esc_url_raw(add_query_arg('actions', $item['value'], $base_crm)); ?>">
                                <?php echo ' - ' . esc_html($item['label']); ?>
                                        </a>
                                    </li>
                        <?php } ?>
                            </ul>

                    <?php } ?>

                    </li>
                    <?php
                }
            }
        }
    endforeach;
    ?>

    <li role="presentation">
        <a href="<?php print esc_url(wp_logout_url(esc_url($home_url))); ?>" class="" >
        <?php
        include(locate_template('templates/dashboard-templates/dashboard-icons/logout.svg'));
        esc_html_e('Logout', 'wpresidence');
        ?>
        </a>
        <?php
    }





/*
 * Show Dashboard title
 *
 */
function wpestate_show_dashboard_title($title = '', $second_title = '', $description = '') {
    global $post;
    $current_user = wp_get_current_user();
    $userID = $current_user->ID;
    $user_data = get_userdata($userID);
    $name = $user_data->first_name . ' ' . $user_data->last_name;
    $message = esc_html__('Welcome', 'wpresidence');
    if (trim($name) != '') {
        $message .= ', ' . $name;
    }
    $user_role = get_user_meta($userID, 'user_estate_role', true);
    if ($user_role == 3 || $user_role == 4) {
        $developer_id = get_the_author_meta('user_agent_id', $userID);
        $message .= ', ' . get_the_title($developer_id);
    }
    print '<div class="dashboard_hello_section">';
    // print '<div class="dashboard_hi_text">' . $message . '</div>';
    // print '<h2>' . esc_html($title) . '</h2>';
    print '<div class="dashboard_hi_text">' . esc_html($title) . '</div>';

    // $no_unread = intval(get_user_meta($userID, 'unread_mess', true));
    // print '<div class="wpestate_bell_note"><a href="' . esc_url(wpestate_get_template_link('user_dashboard_inbox.php')) . '">';
    // include(locate_template('templates/dashboard-templates/dashboard-icons/bell.svg'));
    // print '<div class="wpestate_bell_note_unread">' . intval($no_unread) . '</div></a>';
    // print '</div>';
    print '</div>';
}






/*
 * Check if the page loaded belongs to dashboard
 *
 */
function wpestate_is_user_dashboard() {
    global $post;
    $page_template='';
    if(isset($post->ID)){
       $page_template = get_post_meta( $post->ID, '_wp_page_template', true );
    }
   
    $dashboard_pages=array(
        'user_dashboard_main.php',
        'user_dashboard.php' ,
        'user_dashboard_add.php',
        'user_dashboard_profile.php',
        'user_dashboard_favorite.php',
        'user_dashboard_analytics.php',
        'user_dashboard_searches.php',
        'user_dashboard_search_result.php',
        'user_dashboard_invoices.php', 
        'user_dashboard_add_agent.php',
        'user_dashboard_agent_list.php',
        'user_dashboard_inbox.php',
        'wpestate-crm-dashboard.php',
        'wpestate-crm-dashboard_contacts.php',
        'wpestate-crm-dashboard_leads.php',

        // Rajesh
        'user_dashboard_customer_comments.php'
    );

    if (in_array($page_template, $dashboard_pages) ) { 
        return true;
    } else { 
        return false;
    }
         
}







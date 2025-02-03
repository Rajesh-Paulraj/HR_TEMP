<?php
/**
 * Plugin Name: Homereviewz - Users KPI
 * Description: A sample code to add a "Users KPI" menu under Dashboard with charts, stats, and export functionality.
 * Version: 1.0
 * Author: Your Name
 */

/**
 * 1) Track user login times in user meta.
 */
add_action('wp_login', 'homereviewz_update_last_login', 10, 2);
function homereviewz_update_last_login($user_login, $user) {
    update_user_meta($user->ID, 'last_login', current_time('timestamp'));
}

/**
 * 2) Enqueue Chart.js in admin.
 */
add_action('admin_enqueue_scripts', 'homereviewz_enqueue_chartjs');
function homereviewz_enqueue_chartjs() {
    // Only load on our specific page to avoid overhead site-wide
    $screen = get_current_screen();
    if ( isset($screen->id) && $screen->id === 'dashboard_page_users-dashboard' ) {
        wp_enqueue_script('chartjs', 'https://cdn.jsdelivr.net/npm/chart.js', [], null, true);
    }
}

/**
 * 3) Create a new top-level Admin Menu under 'Dashboard' for "Users KPI"
 */
// add_action('admin_menu', 'homereviewz_add_users_kpi_menu');
// function homereviewz_add_users_kpi_menu() {
//     // Add top-level menu under Dashboard
//     add_menu_page(
//         'Users Dashboard',            // Page Title
//         'Users Dashboard',            // Menu Title
//         'manage_options',       // Capability
//         'users-dashboard',        // Menu Slug
//         'homereviewz_render_users_kpi_page', // Callback
//         'dashicons-chart-pie',  // Icon
//         3                       // Position under Dashboard
//     );
// }

add_action('admin_menu', 'homereviewz_add_users_kpi_submenu');
function homereviewz_add_users_kpi_submenu() {
    // Add submenu under Dashboard
    add_submenu_page(
        'index.php',                    // Parent slug (Dashboard menu slug)
        'Users Dashboard',              // Page title
        'Users Dashboard',              // Menu title
        'manage_options',               // Capability
        'users-dashboard',              // Menu slug
        'homereviewz_render_users_kpi_page' // Callback function
    );
}

/**
 * 4) Render the "Users KPI" page
 */
function homereviewz_render_users_kpi_page() {
    // Security check: only allow admins
    if ( !current_user_can('manage_options') ) {
        return;
    }

    global $wpdb;

    // ====================
    // A) Data Gathering
    // ====================

    // 1) Total users & roles breakdown
    $count_data = count_users();
    $total_users = $count_data['total_users'];
    $role_counts = $count_data['avail_roles']; // e.g. $role_counts['administrator'], $role_counts['subscriber'], etc.

    // 2) Monthly Growth
    //    We'll check new registrations for current month vs previous month
    $current_month_start = date('Y-m-01 00:00:00');
    $current_month_end   = date('Y-m-t 23:59:59');

    $current_month_count = $wpdb->get_var($wpdb->prepare("
        SELECT COUNT(*)
        FROM $wpdb->users
        WHERE user_registered BETWEEN %s AND %s
    ", $current_month_start, $current_month_end));

    $previous_month_start = date('Y-m-01 00:00:00', strtotime('-1 month'));
    $previous_month_end   = date('Y-m-t 23:59:59', strtotime('-1 month'));

    $previous_month_count = $wpdb->get_var($wpdb->prepare("
        SELECT COUNT(*)
        FROM $wpdb->users
        WHERE user_registered BETWEEN %s AND %s
    ", $previous_month_start, $previous_month_end));

    if ($previous_month_count > 0) {
        $monthly_growth = round((($current_month_count - $previous_month_count) / $previous_month_count) * 100, 2);
    } else {
        $monthly_growth = 0;
    }

    // 3) Active Users (logged in last 7 days)
    $seven_days_ago = strtotime('-7 days');
    $args_active = array(
        'meta_key'     => 'last_login',
        'meta_value'   => $seven_days_ago,
        'meta_compare' => '>=',
        'fields'       => 'ID'
    );
    $recent_logins = get_users($args_active);
    $active_users_count = count($recent_logins);

    // 4) User Engagement (example: total comments / total users)
    //    If your "reviews" are stored as comments, you can also filter by comment_type or post_type if needed
    $total_comments = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = '1'");
    $average_engagement = ($total_users > 0) ? round($total_comments / $total_users, 2) : 0;

    // 5) Data for Monthly Registrations (Line Chart)
    //    We'll fetch the count of registrations per month for the last 6 months
    $months_array = array();
    $registrations_array = array();
    for ($i = 5; $i >= 0; $i--) {
        $start_date = date('Y-m-01 00:00:00', strtotime("-$i month"));
        $end_date   = date('Y-m-t 23:59:59', strtotime("-$i month"));
        $label_month = date('M Y', strtotime("-$i month"));
        $count_month = $wpdb->get_var($wpdb->prepare("
            SELECT COUNT(*) 
            FROM $wpdb->users 
            WHERE user_registered BETWEEN %s AND %s
        ", $start_date, $end_date));

        $months_array[] = $label_month;
        $registrations_array[] = (int) $count_month;
    }

    // 6) Bar Chart: Top 5 users based on engagement (e.g., most comments)
    //    This is a simple example if you store each user's comments in wp_comments table
    //    For advanced "reviews" logic, adapt accordingly.
    $top_users_query = "
        SELECT user_id, COUNT(comment_ID) as comment_count
        FROM $wpdb->comments
        WHERE comment_approved = '1' AND user_id != 0
        GROUP BY user_id
        ORDER BY comment_count DESC
        LIMIT 5
    ";
    $top_users = $wpdb->get_results($top_users_query, ARRAY_A);

    // We'll prepare arrays for Chart.js
    $bar_labels = array();
    $bar_data   = array();
    if (!empty($top_users)) {
        foreach ($top_users as $user_data) {
            $u_info = get_user_by('id', $user_data['user_id']);
            $bar_labels[] = $u_info ? $u_info->user_nicename : 'Unknown';
            $bar_data[]   = (int) $user_data['comment_count'];
        }
    }

    // ===========================
    // B) Handle Search/Filter
    // ===========================
    $filter_role = isset($_GET['filter_role']) ? sanitize_text_field($_GET['filter_role']) : '';
    $filter_date = isset($_GET['filter_date']) ? sanitize_text_field($_GET['filter_date']) : '';

    $filter_args = array(
        'orderby' => 'user_registered',
        'order'   => 'DESC',
        'number'  => 20, // just show 20 in UI
    );
    if ($filter_role) {
        $filter_args['role'] = $filter_role;
    }
    // If we also want to filter by registration date after X:
    // Note: WordPress doesn't have a direct 'date_registered' arg in get_users(), so we might do a meta_query or direct SQL
    // For simplicity, let's just handle date with a direct SQL if provided:
    $filtered_users = array();
    if ($filter_date) {
        // We'll manually query users who registered after $filter_date
        $filtered_users = $wpdb->get_results($wpdb->prepare("
            SELECT ID 
            FROM $wpdb->users
            WHERE user_registered >= %s
            ORDER BY user_registered DESC
            LIMIT 50
        ", $filter_date));
    } else {
        $filtered_users = get_users($filter_args);
    }

    // ===========================
    // C) Export to CSV
    // ===========================
    if (isset($_GET['export_users']) && $_GET['export_users'] === '1') {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=users_export.csv');
        $output = fopen('php://output', 'w');
        // Add CSV column headers
        fputcsv($output, array('User ID', 'Username', 'Email', 'Role', 'Registered On'));

        // For demo, we’ll export all users (or you could export filtered)
        $all_users = get_users(array('fields' => array('ID')));
        foreach ($all_users as $user_obj) {
            $user_info = get_userdata($user_obj->ID);
            fputcsv($output, array(
                $user_info->ID,
                $user_info->user_login,
                $user_info->user_email,
                implode(', ', $user_info->roles),
                $user_info->user_registered,
            ));
        }

        fclose($output);
        exit;
    }

    // ===========================
    // D) Recent Registrations
    // ===========================
    $args_recent = array(
        'orderby' => 'user_registered',
        'order'   => 'DESC',
        'number'  => 5,
    );
    $recent_users = get_users($args_recent);

    // ===========================
    // E) Display the Admin Page
    // ===========================
    ?>
    <div class="wrap">
        <h1>Users Dashboard</h1>

        <!-- KPI Cards -->
        <div style="display: flex; flex-wrap: wrap; gap: 20px; margin-bottom: 20px;">
            <div style="background: #fff; border: 1px solid #ccc; padding: 15px; width: 200px;">
                <h2><?php echo esc_html($total_users); ?></h2>
                <p>Total Users</p>
            </div>
            <div style="background: #fff; border: 1px solid #ccc; padding: 15px; width: 200px;">
                <h2><?php echo esc_html($monthly_growth); ?>%</h2>
                <p>Monthly Growth</p>
            </div>
            <div style="background: #fff; border: 1px solid #ccc; padding: 15px; width: 200px;">
                <h2><?php echo esc_html($active_users_count); ?></h2>
                <p>Active Users (Last 7 Days)</p>
            </div>
            <div style="background: #fff; border: 1px solid #ccc; padding: 15px; width: 200px;">
                <h2><?php echo esc_html($average_engagement); ?></h2>
                <p>Avg Engagement (Comments/User)</p>
            </div>
        </div>

        <!-- Charts -->
        <div style="display: flex; flex-wrap: wrap; gap: 30px;">
            <!-- Pie Chart: Role Distribution -->
            <div style="flex: 1; min-width: 300px;">
                <h3>Role Distribution</h3>
                <canvas id="rolePieChart"></canvas>
            </div>
            <!-- Line Chart: Monthly Registrations -->
            <div style="flex: 1; min-width: 300px;">
                <h3>Monthly Registrations (Last 6 Months)</h3>
                <canvas id="monthlyLineChart"></canvas>
            </div>
            <!-- Bar Chart: Top 5 Engaged Users -->
            <div style="flex: 1; min-width: 300px;">
                <h3>Top 5 Engaged Users</h3>
                <canvas id="topUsersBarChart"></canvas>
            </div>
        </div>


        <!-- Recent Registrations -->
        <hr />
        <h2>Recent Registrations</h2>
        <table class="widefat striped">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Role</th>
                    <th>Registered On</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recent_users as $user): ?>
                    <tr>
                        <td><?php echo esc_html($user->user_login); ?></td>
                        <td><?php echo implode(', ', $user->roles); ?></td>
                        <td><?php echo esc_html($user->user_registered); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>


        <!-- Search / Filter -->
        <!-- <hr />
        <h2>Search / Filter</h2>
        <form method="GET" style="margin-bottom: 20px;">
            <input type="hidden" name="page" value="users-dashboard" />
            <label for="filter_role">Role: </label>
            <select name="filter_role" id="filter_role">
                <option value="">All</option>
                <--?php
                // Show roles from $role_counts array
                foreach ($role_counts as $role_name => $role_count) {
                    echo '<option value="' . esc_attr($role_name) . '"' . selected($role_name, $filter_role, false) . '>' . esc_html(ucfirst($role_name)) . '</option>';
                }
                ?>
            </select>
            <label for="filter_date">Registered After: </label>
            <input type="date" name="filter_date" id="filter_date" value="<?php echo esc_attr($filter_date); ?>" />
            <input type="submit" class="button button-primary" value="Filter" />
        </form> -->

        <!-- Filtered Users Table -->
        <!-- <table class="widefat striped">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Role</th>
                    <th>Registered</th>
                </tr>
            </thead>
            <tbody>
            <--?php
            if (!empty($filtered_users)) {
                foreach ($filtered_users as $f_user) {
                    // If we used direct query above, $f_user might be an stdClass with ID property
                    $user_id = is_object($f_user) ? $f_user->ID : $f_user->ID;
                    $u = get_user_by('id', $user_id);
                    if (!$u) continue;
                    ?>
                    <tr>
                        <td><--?php echo esc_html($u->user_login); ?></td>
                        <td><--?php echo implode(', ', $u->roles); ?></td>
                        <td><--?php echo esc_html($u->user_registered); ?></td>
                    </tr>
                    <--?php
                }
            } else {
                echo '<tr><td colspan="3">No users found.</td></tr>';
            }
            ?>
            </tbody>
        </table> -->

        <!-- Export Button -->
        <!-- <hr />
        <a href="?page=users-dashboard&export_users=1" class="button button-primary">Export CSV</a> -->

    </div><!-- end .wrap -->

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1) Pie Chart: Role Distribution
        var roleCtx = document.getElementById('rolePieChart').getContext('2d');
        var roleData = {
            labels: [
                'Administrator',
                'Subscriber',
                'Contributor'
                // Add more roles as needed
            ],
            datasets: [{
                data: [
                    <?php echo isset($role_counts['administrator']) ? $role_counts['administrator'] : 0; ?>,
                    <?php echo isset($role_counts['subscriber']) ? $role_counts['subscriber'] : 0; ?>,
                    <?php echo isset($role_counts['contributor']) ? $role_counts['contributor'] : 0; ?>
                ],
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56']
            }]
        };
        new Chart(roleCtx, {
            type: 'pie',
            data: roleData,
            options: {
                responsive: true,
                // maintainAspectRatio: false
            }
        });

        // 2) Line Chart: Monthly Registrations
        var monthlyCtx = document.getElementById('monthlyLineChart').getContext('2d');
        var monthlyData = {
            labels: <?php echo json_encode($months_array); ?>,
            datasets: [{
                label: 'Registrations',
                data: <?php echo json_encode($registrations_array); ?>,
                fill: false,
                borderColor: '#36A2EB',
                tension: 0.1
            }]
        };
        new Chart(monthlyCtx, {
            type: 'line',
            data: monthlyData,
            options: {
                responsive: true,
                // maintainAspectRatio: false
            }
        });

        // 3) Bar Chart: Top 5 Engaged Users (by comment count)
        var barCtx = document.getElementById('topUsersBarChart').getContext('2d');
        var barLabels = <?php echo json_encode($bar_labels); ?>;
        var barCounts = <?php echo json_encode($bar_data); ?>;

        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: barLabels,
                datasets: [{
                    label: 'Comments',
                    data: barCounts,
                    backgroundColor: '#FF6384'
                }]
            },
            options: {
                responsive: true,
                // maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        precision: 0
                    }
                }
            }
        });
    });
    </script>

    <?php
} // end function homereviewz_render_users_kpi_page
?>

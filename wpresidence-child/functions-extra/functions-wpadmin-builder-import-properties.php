<?php
// Include necessary WordPress libraries
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

defined('IMPORT_PROPERTIES_FROM_EXCEL') or define('IMPORT_PROPERTIES_FROM_EXCEL', true);

/**
 * Convert a "price range" string (e.g. "59 Lakhs +", "1.1 Crores +") to a numeric value.
 * Example:
 *  "59 Lakhs +"    -> "5900000"
 *  "83.94 Lakhs +" -> "8394000"
 *  "1.1 Crores +"  -> "11000000"
 *
 * Returns the numeric amount as a string. If it doesn't match, returns the original string.
 */
function convert_price_range_to_number($price_str) {
    // Normalize
    $lower = strtolower($price_str);
    // Remove any trailing plus sign
    $lower = str_replace('+', '', $lower);
    $lower = trim($lower);

    // Regex to get the decimal / numeric portion
    preg_match('/([\d\.]+)/', $lower, $matches);
    $number_part = !empty($matches[1]) ? floatval($matches[1]) : 0;

    if (strpos($lower, 'lakh') !== false) {
        // 1 Lakh = 100,000
        return (string) ($number_part * 100000);
    } elseif (strpos($lower, 'crore') !== false) {
        // 1 Crore = 10,000,000
        return (string) ($number_part * 10000000);
    }

    // If we don't find "lakh" or "crore", return the raw string or handle differently.
    return $price_str;
}

/**
 * Property Type Mapping: e.g., "APARTMENTS" => "Apartments"
 */
function transform_property_type($value) {
    $map = [
        'APARTMENTS' => 'Apartments',
        'Rowhouses'  => 'Row Houses'
        // Add more if needed
    ];

    $upper = strtoupper($value);
    if (isset($map[$upper])) {
        return $map[$upper];
    }
    return $value;
}

/**
 * If the Excel "Launch date" is in format "Feb-25", convert to "2025-02-01" (YYYY-mm-dd).
 * If the Excel "Handover date" is in format "Mar-25", convert to "2025-03-31" (last day).
 * Ignores other formats.
 */
function parse_short_month_year($value, $use_last_day = false) {
    if (preg_match('/^([A-Za-z]{3})-(\d{2})$/', $value, $matches)) {
        $month_abbrev = $matches[1];
        $year_2digit  = (int) $matches[2];
        $year_full    = 2000 + $year_2digit;

        // Convert the month abbrev to a number (01-12)
        $month_num = date('m', strtotime("$month_abbrev 1"));

        if ($use_last_day) {
            $days_in_month = cal_days_in_month(CAL_GREGORIAN, (int)$month_num, $year_full);
            return sprintf('%04d-%02d-%02d', $year_full, $month_num, $days_in_month);
        }
        return sprintf('%04d-%02d-01', $year_full, $month_num);
    }

    // If not matching "MMM-YY", just return the original
    return $value;
}

/**
 * Imports properties from an Excel file with explicit mapping to WPResidence custom fields.
 *
 * - If PROJECT NAME does not exist, create property.
 * - If PROJECT NAME exists, UPDATE that property (instead of skipping).
 * - If PRICE RANGE is "Price On Request", set label accordingly. Otherwise do Lakhs/Crores conversion.
 * - "APARTMENTS" => "Apartments" transform for property type.
 * - LAUNCH DATE => "MMM-YY" => "YYYY-mm-01"
 * - HANDOVER DATE => "MMM-YY" => "YYYY-mm-(last day)"
 * - Also: Assign the property_type term in wp_terms ignoring case.
 */
function import_properties_from_excel($file_path) {

    if (!file_exists($file_path)) {
        wp_die(__('Excel file not found.', 'textdomain'));
    }

    // Optional file extension check
    $extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
    if (!in_array($extension, ['xlsx', 'xls'], true)) {
        return __('Invalid file format. Please upload an XLSX or XLS file.', 'textdomain');
    }

    // Load PHPSpreadsheet
    require_once get_stylesheet_directory() . '/libs/PhpSpreadsheet/autoload.php';

    // Attempt to read the spreadsheet
    try {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file_path);
    } catch (\Exception $e) {
        return sprintf(__('Error loading Excel file: %s', 'textdomain'), $e->getMessage());
    }

    $worksheet = $spreadsheet->getActiveSheet();
    $columns   = $worksheet->toArray(null, true, true, true);

    // Basic validation
    if (empty($columns) || count($columns) < 2) {
        return __('Invalid or empty Excel file.', 'textdomain');
    }

    /**
     * MAP your Excel field names to the WPResidence meta keys.
     */
    $meta_key_map = [
        'PROPERTY TYPE'    => 'property-type',
        'CODE'             => 'custom-property-code',
        'LOCATION'         => 'custom-property-address',
        'PRICE RANGE'      => 'property_price',
        'BHK'              => 'bhk',
        'SIZE RANGE'       => 'size-range',
        'LAUNCH DATE'      => 'launch-date',
        'HANDOVER DATE'    => 'handover-date',
        'RERA/DTCP NUMBER' => 'rera-dtcp-number',
        // 'GOOGLE RATING'    => 'google-rating',
        'PROJECT WEBSITE'  => 'property-website',
        'PROJECT IMAGES'   => 'project-images-drive-link',
        'VIDEO REVIEW LINK'=> 'video-review-link',
    ];

    /**
     * BHK Map
     */
    $bhk_map = [
        'Not Available' => 'Not Available',
        '1 BHK'         => '1',
        '2 BHK'         => '2',
        '3 BHK'         => '3',
        '4 BHK'         => '4',
        '5 BHK'         => '5',
        '1 & 2 BHK'     => '1 & 2',
        '2 & 2.5 BHK'   => '2 & 2.5',
        '2 & 3 BHK'     => '2 & 3',
        '3 & 4 BHK'     => '3 & 4',
        '4 & 5 BHK'     => '4 & 5',
        '1, 2 & 3 BHK'  => '1 & 2 & 3',
        '2, 3 & 4 BHK'  => '2 & 3 & 4',
        ''             => 'Not Available',
    ];

    // Track how many are imported and which properties are skipped
    $skipped_properties = [];
    $imported_properties = 0;

    // STEP 1: Extract field names from Column A
    $field_names = [];
    foreach ($columns as $row_index => $row) {
        $field_name = $row['A'] ?? null;
        if (!empty($field_name)) {
            $field_names[$row_index] = sanitize_text_field($field_name);
        }
    }

    // STEP 2: Each property is in columns B, C, D, etc.
    $property_columns = array_keys($columns[1] ?? []);
    array_shift($property_columns); // Removes 'A'

    foreach ($property_columns as $column_key) {
        $property_data = [];

        // Gather row data, keyed by field name
        foreach ($columns as $row_index => $row) {
            $field_label = $field_names[$row_index] ?? null;
            $value       = $row[$column_key] ?? null;
            if (!empty($field_label)) {
                $property_data[$field_label] = sanitize_text_field($value);
            }
        }

        // Must have PROJECT NAME
        if (empty($property_data['PROJECT NAME'])) {
            // Collect info about the skip
            // If there's no project name, we can't print it, but let's note which column is skipped
            $skipped_properties[] = "Column $column_key (missing PROJECT NAME)";
            error_log('Skipping column with missing PROJECT NAME.');
            continue;
        }

        // 1) If property exists, update. Otherwise create.
        $existing_property = get_page_by_title(
            $property_data['PROJECT NAME'],
            OBJECT,
            'estate_property'
        );

        if ($existing_property) {
            $property_id = $existing_property->ID;
            wp_update_post([
                'ID'          => $property_id,
                'post_title'  => $property_data['PROJECT NAME'],
                'post_status' => 'publish',
                'post_type'   => 'estate_property',
            ]);
        } else {
            $property_id = wp_insert_post([
                'post_title'   => $property_data['PROJECT NAME'],
                'post_status'  => 'publish',
                'post_type'    => 'estate_property',
            ]);
            if (is_wp_error($property_id)) {
                error_log(
                    'Error creating property: ' .
                    $property_data['PROJECT NAME'] . ' - ' .
                    $property_id->get_error_message()
                );
                continue;
            }
        }

        // 2) Create/Get Agent
        $agent_id = null;
        if (!empty($property_data['BUILDER NAME'])) {
            $builder_name = $property_data['BUILDER NAME'];

            $agent_query = new WP_Query([
                'post_type'      => 'estate_agent',
                'title'          => $builder_name,
                'fields'         => 'ids',
                'post_status'    => 'any',
                'posts_per_page' => 1,
            ]);

            if ($agent_query->have_posts()) {
                $agent_id = current($agent_query->posts);
            } else {
                $agent_id = wp_insert_post([
                    'post_title'  => $builder_name,
                    'post_type'   => 'estate_agent',
                    'post_status' => 'publish',
                ]);
                if (is_wp_error($agent_id)) {
                    error_log('Error creating agent: ' . $builder_name . ' - ' . $agent_id->get_error_message());
                    $agent_id = null;
                }
            }
        }

        // 3) Create / Get Builder User
        $user_id = null;
        if (!empty($property_data['BUILDER NAME'])) {
            $sanitized_builder_name = preg_replace('/\s+/', '_', strtolower($property_data['BUILDER NAME']));
            $user_name             = $sanitized_builder_name . '_admin';
            $user_email            = $sanitized_builder_name . '@example.com';

            $existing_user = get_user_by('login', $user_name);
            if (!$existing_user) {
                $existing_user = get_user_by('email', $user_email);
            }

            if (!$existing_user) {
                $random_password = wp_generate_password(12, false);
                $user_id = wp_insert_user([
                    'user_login' => $user_name,
                    'user_pass'  => $random_password,
                    'user_email' => $user_email,
                    'role'       => 'subscriber',
                    'first_name' => $property_data['BUILDER NAME'],
                ]);

                // Set expire date
                update_user_meta($user_id, '_expire_user_date', strtotime('2025-04-30 12:00:00'));

                if (is_wp_error($user_id)) {
                    error_log('Error creating user: ' . $user_name . ' - ' . $user_id->get_error_message());
                    $user_id = null;
                } else {
                    // Add contributor
                    $user_object = new WP_User($user_id);
                    $user_object->add_role('contributor');
                    // Link user <-> agent
                    if ($agent_id) {
                        update_post_meta($agent_id, 'agent_user', $user_id);
                    }
                }
            } else {
                $user_id = $existing_user->ID;
                // Link user & agent
                if ($agent_id) {
                    update_post_meta($agent_id, 'agent_user', $user_id);
                }
                // Also set expire date
                update_user_meta($user_id, '_expire_user_date', strtotime('2025-04-30 12:00:00'));
            }
        }

        // 4) Link property to agent & user
        if ($agent_id) {
            update_post_meta($property_id, 'property_agent', $agent_id);
        }
        if ($user_id) {
            update_post_meta($property_id, 'property_user', $user_id);
            // Also update post_author
            wp_update_post([
                'ID'         => $property_id,
                'post_author'=> $user_id,
            ]);
        }

        // 5) Always set "Select header type" = "1" (none)
        update_post_meta($property_id, 'header_type', '1');

        // 6) Save & Update Custom Fields
        foreach ($property_data as $excel_field => $value) {
            if (empty($value)) {
                continue;
            }
            if (in_array($excel_field, ['PROJECT NAME', 'BUILDER NAME'], true)) {
                continue;
            }

            // (a) BHK transform
            if ($excel_field === 'BHK') {
                $value = isset($bhk_map[$value]) ? $bhk_map[$value] : 'Not Available';
            }

            // (b) PROPERTY TYPE => transform, then link term ignoring case
            if ($excel_field === 'PROPERTY TYPE') {
                $value = transform_property_type($value);

                global $wpdb;
                $found_term = $wpdb->get_row($wpdb->prepare("
                    SELECT t.term_id, tt.term_taxonomy_id
                    FROM {$wpdb->terms} t
                    INNER JOIN {$wpdb->term_taxonomy} tt ON t.term_id = tt.term_id
                    WHERE LOWER(t.name) = LOWER(%s)
                      AND tt.taxonomy = 'property_category'
                    LIMIT 1
                ", $value));
                if ($found_term) {
                    wp_set_object_terms($property_id, (int)$found_term->term_id, 'property_category', false);
                }
            }

            // (c) LAUNCH DATE => "Feb-25" => "2025-02-01"
            if ($excel_field === 'LAUNCH DATE') {
                $value = parse_short_month_year($value, false);
            }

            // (d) HANDOVER DATE => "Mar-25" => "2025-03-31"
            if ($excel_field === 'HANDOVER DATE') {
                $value = parse_short_month_year($value, true);
            }

            // (e) PRICE RANGE => "Price On Request" or else Lakhs/Crores
            if ($excel_field === 'PRICE RANGE') {
                if (strtolower($value) === 'price on request') {
                    update_post_meta($property_id, 'property_label', '₹ Price On Request');
                } else {
                    $value = convert_price_range_to_number($value);
                    update_post_meta($property_id, 'property_label', 'onwards');
                }
            }

            // (f) Map to meta key
            if (isset($meta_key_map[$excel_field])) {
                $mapped_key = $meta_key_map[$excel_field];
                update_post_meta($property_id, $mapped_key, $value);
            } else {
                $fallback_key = strtolower(str_replace([' ', '/'], ['_', '_'], $excel_field));
                update_post_meta($property_id, $fallback_key, $value);
            }
        }

        $imported_properties++;
    }

    // Build final message
    if ($imported_properties === 0) {
        // No properties imported
        $message = __('No properties were imported. Please check your Excel data.', 'textdomain');
    } else {
        // Some were imported
        $message = sprintf(__('Properties imported/updated successfully: %d', 'textdomain'), $imported_properties);
    }

    // If any were skipped, append their info
    if (!empty($skipped_properties)) {
        $message .= '<br/><br/>' . __('Skipped entries:', 'textdomain') . ' ' . implode(', ', $skipped_properties);
    }

    return $message;
}

// ========== The rest of your existing code remains unchanged ==========

add_action('admin_post_import_properties', function () {
    if (!current_user_can('manage_options')) {
        wp_die(__('Unauthorized.', 'textdomain'));
    }

    if (!isset($_FILES['properties_excel']) || empty($_FILES['properties_excel']['tmp_name'])) {
        wp_die(__('No file uploaded.', 'textdomain'));
    }

    $file = wp_upload_bits(
        $_FILES['properties_excel']['name'],
        null,
        file_get_contents($_FILES['properties_excel']['tmp_name'])
    );

    if (!empty($file['error'])) {
        wp_die(__('Error uploading file: ', 'textdomain') . $file['error']);
    }

    $result = import_properties_from_excel($file['file']);
    echo $result;
    exit;
});

add_action('admin_menu', function () {
    add_menu_page(
        __('Import Properties', 'textdomain'),
        __('Import Properties', 'textdomain'),
        'manage_options',
        'import-properties',
        'render_import_properties_page',
        'dashicons-upload',
        20
    );
});

function render_import_properties_page() {
    ?>
    <div class="wrap">
        <h1><?php _e('Import Properties', 'textdomain'); ?></h1>
        <p>
            <?php _e('Upload an Excel file to import or update multiple properties. Make sure the file format and column mapping are correct.', 'textdomain'); ?>
        </p>

        <form
            id="import-properties-form"
            method="post"
            action="<?php echo esc_url(admin_url('admin-post.php?action=import_properties')); ?>"
            enctype="multipart/form-data"
            style="background: #f9f9f9; padding: 20px; border-radius: 8px;"
        >
            <div style="margin-bottom: 15px;">
                <label for="properties_excel" style="font-weight: bold; margin-bottom: 5px; display: block;">
                    <?php _e('Upload Excel File:', 'textdomain'); ?>
                </label>
                <input
                    type="file"
                    name="properties_excel"
                    id="properties_excel"
                    accept=".xlsx, .xls"
                    required
                    style="padding: 5px; border: 1px solid #ccc; border-radius: 4px; width: 100%;"
                />
            </div>

            <button
                type="submit"
                class="button button-primary"
                style="background-color: #007cba; border-color: #006ba1;"
            >
                <?php _e('Upload and Import', 'textdomain'); ?>
            </button>
        </form>

        <div style="margin-top: 20px;">
            <h2><?php _e('Instructions & Mapping', 'textdomain'); ?></h2>
            <p>
                <?php _e('• If "PROPERTY TYPE" matches a term (ignoring case), link it in wp_term_relationships.', 'textdomain'); ?><br/>
                <?php _e('• "Feb-25" (Launch) => "2025-02-01", "Mar-25" (Handover) => "2025-03-31".', 'textdomain'); ?><br/>
                <?php _e('• Skipped entries will be listed at the end of the import result for manual checking.', 'textdomain'); ?><br/>
                <?php _e('• All other existing logic remains unchanged.', 'textdomain'); ?>
            </p>
        </div>
    </div>
    <?php
}

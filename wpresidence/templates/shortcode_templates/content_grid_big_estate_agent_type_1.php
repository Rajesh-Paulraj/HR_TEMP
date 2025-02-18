<?php
$main_image     =   wp_get_attachment_image_src(get_post_thumbnail_id($itemID), 'listing_full_slider');
$main_image_url =   isset($main_image[0]) ? $main_image[0] : wpresidence_get_option('wp_estate_prop_list_slider_image_palceholder', 'url');
$title          =   get_the_title($itemID); 
$link           =   esc_url(get_permalink($itemID));
$excerpt        =   wpestate_strip_excerpt_by_char(get_the_excerpt($itemID),115,$itemID,'...');
$new_page_option=   wpresidence_get_option('wp_estate_unit_card_new_page', '');
$target         =   $new_page_option === '_self' ? '' : 'target="' . esc_attr($new_page_option) . '"';
$allowed_html = [
    'br' => [],
    'em' => [],
    'strong' => [],
    'b' => []
];

$agent_posit        = esc_html( get_post_meta($itemID, 'agent_position', true) );
?>

<div class="property_unit_type5_content_wrapper property_listing" data-link="<?php echo $link; ?>">

    <div class="property_unit_type5_content" style="background-image:url('<?php echo $main_image_url; ?>')"></div>
    <div class="featured_gradient"></div>

    <div class="property_unit_content_grid_big_details">
        <div class="blog_unit_meta">
            <?php echo trim($agent_posit); ?>
        </div>
        <h4>
            <a href="<?php echo $link; ?>" <?php echo $target; ?>>
                <?php echo wp_kses($title, $allowed_html); ?>
            </a>
        </h4>
        <div class="property_unit_content_grid_big_details_location">
            <?php echo trim($excerpt); ?>
        </div>
    </div>
</div>

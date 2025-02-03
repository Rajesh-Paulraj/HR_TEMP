<?php
// functions-discount-coupon.php

// Function to handle the custom shortcode
function dynamic_ad_placement_shortcode( $atts ) {
    // Get the current property ID
    $property_id = get_the_ID();

    // Retrieve the 'ads-group-id' from custom fields
    $ads_group_id = get_post_meta( $property_id, 'ads-group-id', true );
    if (empty( $ads_group_id)) {
        // This is the default ads group for all property
        $ads_group_id = 'placement-test-2';
    }

    // Check if ads_group_id exists
    if ( ! empty( $ads_group_id ) ) {
        // Construct the original shortcode with dynamic ID
        $shortcode = '[the_ad_placement id="' . esc_attr( $ads_group_id ) . '"]';

        // Execute and return the original shortcode
        return do_shortcode( $shortcode );
    }

    return ''; // Return nothing if no ads_group_id is found
}
add_shortcode( 'dynamic_ad_placement', 'dynamic_ad_placement_shortcode' );


?>
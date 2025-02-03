<?php


// GOOGLE REVIEW - CUSTOM SHORT CODE
function google_review_section(){
    
    global $post;
    $google_review_id     =   esc_html( get_post_meta($post->ID, 'google-review-id', true) );
    echo do_shortcode('[wprevpro_usetemplate tid="' . esc_attr($google_review_id) . '"]');

}

add_shortcode('google_review_section', 'google_review_section');
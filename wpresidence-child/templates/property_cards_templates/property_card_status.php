<div class="status-wrapper">
    <?php

    $featured               =   intval  ( get_post_meta($post->ID, 'prop_featured', true) );
    if($featured==1 && wpresidence_get_option('property_card_agent_show_featured', '')=='yes' ){
        print '<div class="featured_div">'.esc_html__('Featured','wpresidence').'</div>';
    }

    $property_action            =   get_the_terms($post->ID, 'property_action_category');  
    if(isset($property_action[0])){
        $property_action_term = $property_action[0]->name;
        print '<div class="action_tag_wrapper '.esc_attr($property_action_term).' ">'.wp_kses_post($property_action_term).'</div>';
    }                      
    print wpestate_return_property_status($post->ID,'unit');
    
    // Rajesh - Customized code
    // Check if the "Top Rated" custom field is Yes
    $top_rated = get_post_meta($post->ID, 'top-rated', true);
    if ($top_rated === 'Yes') {
        print '<div class="ribbon-inside top-rated">Top Rated</div>';
    }
    
    ?> 
</div>
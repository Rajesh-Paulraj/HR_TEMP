<?php
$featured               =   intval  ( get_post_meta($post->ID, 'prop_featured', true) );?>
<div class="tag-wrapper">
    <!-- <-?php
        if($featured==1 && wpresidence_get_option('property_card_agent_show_featured', '')=='yes' ){
            print '<div class="featured_div">'.esc_html__('Featured','wpresidence').'</div>';
        }
    ?>       -->

    <?php

        $ratingInfo = glsr_get_ratings([
            'assigned_posts' => $post->ID,
        ]);
        $overall_rating = isset($ratingInfo['average']) ? $ratingInfo['average'] : 0;

        // Round to one decimal place for display purposes
        $sr_rating = number_format($overall_rating, 1);
        
        // Full stars
        $full_stars = floor($overall_rating);
        
        // Determine if a half star is needed
        $decimal_part = $overall_rating - $full_stars; 
        $half_stars_count = ($decimal_part >= 0.5) ? 1 : 0;
        
        // Empty stars
        $empty_stars = 5 - $full_stars - $half_stars_count;
        
        ?>
        <div style="top: 20px; display: inline-flex; width: fit-content;" class="glsr-summary show-only-rating-star">
            <div class="glsr-summary-stars">
                <div class="glsr-star-rating glsr-stars" data-rating="<?php echo esc_attr($sr_rating); ?>" data-reviews="0">
                    <?php
                    // Print full stars
                    for ($i = 0; $i < $full_stars; $i++) {
                        echo '<span class="glsr-star glsr-star-full" aria-hidden="true"></span>';
                    }
        
                    // Print half star if needed
                    if ($half_stars_count > 0) {
                        echo '<span class="glsr-star glsr-star-half" aria-hidden="true"></span>';
                    }
        
                    // Print empty stars
                    for ($i = 0; $i < $empty_stars; $i++) {
                        echo '<span class="glsr-star glsr-star-empty" aria-hidden="true"></span>';
                    }
                    ?>
                </div>
            </div>
            <!-- <div class="glsr-summary-rating" style="color: #fff;"><span class="glsr-tag-value"><-?php echo esc_html($sr_rating); ?></span></div> -->
        </div>
        
    <?php 
    if(wpresidence_get_option('property_card_agent_show_status', '')=='yes'){
        get_template_part('templates/property_cards_templates/property_card_status');
    } 
    ?>
</div>


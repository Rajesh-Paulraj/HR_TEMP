<?php

/*
*
* Header masonary type 2
*
*
*/

function wpestate_header_masonry_gallery_type2($prop_id,$main_image_masonry='listing_full_slider',$second_image_masonry='listing_full_slider',$is_shortcode=""){
    print'<div class="gallery_wrapper"><div class=" col-md-8">';

    $post_attachments   =   wpestate_return_property_images($prop_id);
    $count              =   0;
    $total_pictures     =   count ($post_attachments);
    if($count == 0 ){
        $full_prty          = wp_get_attachment_image_src(get_post_thumbnail_id($prop_id), $main_image_masonry);

        $full_prty_src='';
        if(isset($full_prty[0])){
            $full_prty_src=$full_prty[0];
        }
        print wpestate_return_property_status($prop_id,'horizontalstatus');
        print '<div class="col-md-8 image_gallery lightbox_trigger special_border" data-slider-no="1" style="background-image:url('.esc_attr($full_prty_src).')  ">   <div class="img_listings_overlay" ></div></div>';
    }


        foreach ($post_attachments as $attachment) {
            $count++;
            $special_border='  ';
            if($count==0){
                $special_border=' special_border ';
            }

            if($count==1){
                $special_border=' special_border_top ';
            }

            if($count==3){
                $special_border=' special_border_left ';
            }

            if($count <= 4 && $count !=0){
                $full_prty          = wp_get_attachment_image_src($attachment->ID, $second_image_masonry);
                print '<div class="col-md-4 image_gallery lightbox_trigger '.esc_attr($special_border).' " data-slider-no="'.esc_attr($count+1).'" style="background-image:url('.esc_attr($full_prty[0]).')"> <div class="img_listings_overlay" ></div> </div>';
            }

            if($count ==5 ){
                $full_prty          = wp_get_attachment_image_src($attachment->ID, $second_image_masonry);
                print '<div class="col-md-4 image_gallery last_gallery_item lightbox_trigger" data-slider-no="'.esc_attr($count+1).'" style="background-image:url('.esc_attr($full_prty[0]).')  ">
                    <div class="img_listings_overlay img_listings_overlay_last" ></div>
                    <span class="img_listings_mes">'.esc_html__( 'See all','wpresidence').' '.esc_html($total_pictures).' '.esc_html__( 'photos','wpresidence').'</span></div>';
            }
        }

        // <h1 class="entry-title entry-prop"><?php the_title(); ?--></h1>

        // $file_path = get_theme_file_path("templates/extra-custom/design_property_details_to_show_right_side_of_image.php");

        echo '</div><div class="col-md-4">';
        // include(locate_template('templates/extra-custom/design_property_details_to_show_right_side_of_image.php'));
        wpestate_property_overview_v2_CUSTOM($post->ID);
        // include($file_path);
        echo '</div></div>';

    // print '</div>';
}

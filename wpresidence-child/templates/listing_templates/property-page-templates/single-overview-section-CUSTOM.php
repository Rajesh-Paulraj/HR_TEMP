<?php
$wp_estate_property_overview_order = wpresidence_get_option('wp_estate_property_overview_order', '');

if($is_tab!='yes'){ 
    ?>
    <div class="single-overview-section panel-group property-panel" id="single-overview-section">
        <h4 class="panel-title" id=""><?php print   esc_html($label);  ?></h4>
    <?php 
    } 

print '<div class="property-page-overview-details-wrapper" style="gap: 25px">';




    $current_user       =   wp_get_current_user();
    $wpestate_prop_all_details = get_post_custom($post->ID);
    $post_id = $post->ID;

    $propid                     =   $post->ID;
    $wpestate_options           =   wpestate_page_details($post->ID);
    $gmap_lat                   =   esc_html( get_post_meta($post->ID, 'property_latitude', true));
    $gmap_long                  =   esc_html( get_post_meta($post->ID, 'property_longitude', true));
    $unit                       =   esc_html( wpresidence_get_option('wp_estate_measure_sys', '') );
    $wpestate_currency          =   esc_html( wpresidence_get_option('wp_estate_currency_symbol', '') );
    $use_floor_plans            =   intval( get_post_meta($post->ID, 'use_floor_plans', true) );


    $wpestate_currency  =   esc_html( wpresidence_get_option('wp_estate_currency_symbol', '') );
    $where_currency     =   esc_html( wpresidence_get_option('wp_estate_where_currency_symbol', '') );
    $measure_sys        =   esc_html ( wpresidence_get_option('wp_estate_measure_sys','') );
    $property_size      =   wpestate_get_converted_measure( $post_id, 'property_size',$wpestate_prop_all_details );
    $property_lot_size  =   wpestate_get_converted_measure( $post_id, 'property_lot_size',$wpestate_prop_all_details );

    $colmd=wpestat_get_content_comuns($columns,'details');


    if($wpestate_prop_all_details==''){
        $property_rooms     = floatval ( get_post_meta($post_id, 'property_rooms', true) );
        $property_bedrooms  = floatval ( get_post_meta($post_id, 'property_bedrooms', true) );
        $property_bathrooms = floatval ( get_post_meta($post_id, 'property_bathrooms', true) );
        $price              = floatval ( get_post_meta($post_id, 'property_price', true) );
        $property_second_price = floatval ( get_post_meta($post_id, 'property_second_price', true) );
        $energy_index       = get_post_meta($post_id, 'energy_index', true) ;
        $energy_class       = get_post_meta($post_id, 'energy_class', true);
    }else{
        $property_rooms     = floatval (  wpestate_return_custom_field( $wpestate_prop_all_details,'property_rooms') );
        $property_bedrooms  = floatval (  wpestate_return_custom_field( $wpestate_prop_all_details,'property_bedrooms'));
        $property_bathrooms = floatval (  wpestate_return_custom_field( $wpestate_prop_all_details,'property_bathrooms') );
        $price              = floatval (  wpestate_return_custom_field( $wpestate_prop_all_details,'property_price') );
        $property_second_price  = floatval (  wpestate_return_custom_field( $wpestate_prop_all_details,'property_second_price') );
        $energy_index       =  wpestate_return_custom_field( $wpestate_prop_all_details,'energy_index') ;
        $energy_class       =  wpestate_return_custom_field( $wpestate_prop_all_details,'energy_class');
    }



    if ($price != 0) {
        $price =wpestate_show_price_from_all_details($post_id,$wpestate_currency,$where_currency,1,$wpestate_prop_all_details);
    }else{
        $price='';
    }


    if ($property_second_price != 0) {
        $property_second_price =wpestate_show_price_from_all_details($post_id,$wpestate_currency,$where_currency,1,$wpestate_prop_all_details,"yes");
    }else{
        $property_second_price='';
    }





    $hidden_address =    esc_html( get_post_meta($post->ID, 'hidden_address', true) );


?>
<h1 class="entry-title entry-prop" style="font-size: 25px; max-width: 100%;"><?php the_title(); ?></h1>

<div class="property_categs">
    <i class="fas fa-map-marker-alt"></i>
    <?php print wp_kses_post($hidden_address); ?>
</div>


<div class="price_area">
<?php print wp_kses_post($price); ?>
</div>

<div>
<!-- <div class="first_overview_date"><?php print get_the_modified_date(); ?></div> -->
                

</div>                
                
                
                
                
                
                
                
                
                
                
                <?php





if( is_array($wp_estate_property_overview_order['enabled']) ):
    foreach ($wp_estate_property_overview_order['enabled'] as $key=>$value):
        switch ($key) {


            case 'updated_on':
                ?>
                <ul class="overview_element">
                    <li class="first_overview first_overview_left">
                        <?php esc_html_e('Updated On:','wpresidence'); ?>
                    </li>
                    <li class="first_overview_date"><?php print get_the_modified_date(); ?></li>
                </ul>
                <?php
                break;


            case 'bedrooms':
                $property_bedrooms      =   get_post_meta($post->ID,'property_bedrooms',true);
                if($property_bedrooms!='' && $property_bedrooms!=0) { 
                    print wpestate_display_overview_item('bedrooms',$property_bedrooms);
                 } 
                break;


            case 'bathrooms':
                $property_bathrooms     =   get_post_meta($post->ID,'property_bathrooms',true);
                if($property_bathrooms!='' && $property_bathrooms!=0) { 
                    print wpestate_display_overview_item('bathrooms',$property_bathrooms);   
                }
                break;


                
            case 'rooms':
                $property_rooms         =   get_post_meta($post->ID,'property_rooms',true);
                if($property_rooms!='' && $property_rooms!=0) {
                    print wpestate_display_overview_item('rooms',$property_rooms);   
                } 
                break;



            case 'garages':
                $property_garage        =   get_post_meta($post->ID,'property-garage',true);
                if($property_garage!='' && $property_garage!=0) { 
                    print wpestate_display_overview_item('garages',$property_garage);   
                }
                break;


            case 'size':
                $property_size          =   wpestate_get_converted_measure( $post->ID, 'property_size' );
                if($property_size!='' &&   strval($property_size)!='0' ) { 
                    print wpestate_display_overview_item('size',$property_size);   
                }
                break;
            case 'lot_size':
                $property_lot_size         =    wpestate_get_converted_measure( $post->ID, 'property_lot_size' ) ;  
                if($property_lot_size!='' &&  strval($property_lot_size)!='0' ) {                
                    print wpestate_display_overview_item('lot_size',$property_lot_size);   
                } 
                break;

            case 'year_built':
                $property_year          =   get_post_meta($post->ID,'property-year',true);
                if($property_year!='' ) { 
                    print wpestate_display_overview_item('year_built',$property_year);   
                } 
                break;

                
            case 'property_category':
                $property_card_type_string = get_the_term_list($post->ID, 'property_category', '', ', ', '');;
                print wpestate_display_overview_item('property_category',$property_card_type_string);   
                break;

            case 'property_id':
                print wpestate_display_overview_item('property_id',$post->ID);   
                break;

            case 'map':
                $property_latitude         =    get_post_meta( $post->ID, 'property_latitude',true ) ;  
                $property_longitude        =    get_post_meta( $post->ID, 'property_longitude' ,true) ;  
                $marker_image              =    get_theme_file_uri('/css/css-images/idxpin.png');
                $what_map                  =    intval( wpresidence_get_option('wp_estate_kind_of_map') );
                $overview_map_width        =    intval( wpresidence_get_option('wpestate_overview_map_width') );
                $overview_map_height       =    intval( wpresidence_get_option('wpestate_overview_map_height') );

                if($property_latitude!=='' and $property_longitude!=''){
                    if($what_map==1){
                        $api_key                   =    wpresidence_get_option(  'wp_estate_api_key' ) ;  
                        $map_url = "https://maps.googleapis.com/maps/api/staticmap?center={$property_latitude},{$property_longitude}&zoom=11&size={$overview_map_width}x{$overview_map_height}&scale=1&format=jpg&style=feature:administrative.land_parcel|visibility:off&style=feature:landscape.man_made|visibility:off&style=feature:transit.station|hue:0xffa200&markers=icon:{$marker_image}%7C{$property_latitude},{$property_longitude}&key={$api_key}";
                        echo '<img id="overview_map" class="overview_map" src="' . $map_url . '" alt="map-entry" style="width:'.intval($overview_map_width).'px;height:'.intval($overview_map_height).'px;" height="100%" width="100%">';
                    }else{
                        $encoded_marker_image_url = urlencode($marker_image);
                        $mapbox_access_token =  wpresidence_get_option(  'wp_estate_mapbox_api_key' ) ;  
                        $map_url = "https://api.mapbox.com/styles/v1/mapbox/streets-v11/static/url-{$encoded_marker_image_url}({$property_longitude},{$property_latitude})/{$property_longitude},{$property_latitude},11/{$overview_map_width}x{$overview_map_height}?access_token={$mapbox_access_token}";
                        echo '<img id="overview_map"  class="overview_map" src="' . $map_url . '" alt="map-entry" height="100%" width="100%">';                
                    }
                }
                print wpestate_overview_map_modal($post->ID);
                


                break;
        }
        
    endforeach;
endif;
print '</div>';

if($is_tab!='yes'){ ?>
    </div>
<?php 
} 
?>

<button type="button" data-toggle="modal" data-target="#get-discount-voucher-modal" data-backdrop="static" data-keyboard="false" class="wpresidence_button get-discount-voucher-btn" id="wp-child-get-discount-voucher-btn">Get Discount Coupon</button>
<button type="button" data-toggle="modal" data-target="#giftVoucherModal" data-backdrop="static" data-keyboard="false" class="wpresidence_button get-discount-voucher-btn" id="wp-child-get-discount-voucher-btn">Gift Coupon</button>

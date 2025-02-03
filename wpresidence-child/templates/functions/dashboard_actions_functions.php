<?php

/*
* 
* $agent_list is a list of possible author - the real author id + the agency and developer that owns that user
*/
function wpestate_dashboard_customer_reviews($agent_list,$order_by='0',$status_value='0'){
    $prop_no      =   intval( wpresidence_get_option('wp_estate_prop_no', '') );
    $paged        =   (get_query_var('paged')) ? get_query_var('paged') : 1;
    $autofill     =   '';
    $order_by     =   intval($order_by);
    $order_data   =   wpestate_set_order_parameter_property($order_by);
    $agent_list   =   array_filter($agent_list);
    $args = array(
            'post_type'         =>  'estate_property',
            'author__in'        =>  $agent_list,
            'paged'             =>  $paged,
            'posts_per_page'    =>  $prop_no,
            'post_status'       =>  wpestate_set_status_parameter_property($status_value)

            );


        $args ['meta_key']        =   $order_data ['meta_key'];
        $args ['orderby']         =   $order_data ['orderby'] ;
        $args ['order']           =   $order_data ['order'] ;


        if(isset($_POST['prop_name'])){
            $prop_name = esc_html( $_POST['prop_name'] );
            $args['s']  = $prop_name ;
        }



        $prop_selection = new WP_Query($args);

        if($order_by!=0) {
        $prop_selection = new WP_Query($args);
        }else{
        $prop_selection = wpestate_return_filtered_by_order($args);
        }


        $comments = get_comments(array('post_id' => 139));
        




    print '<div class="wpestate_dashboard_list_header">';
            get_template_part('templates/dashboard-templates/dashboard-list-filter-actions');
            get_template_part('templates/dashboard-templates/dashboard-list-filter-categories');
            print '<form action="" id="search_dashboard_auto" method="POST">
                    <input type="text" id="prop_name" name="prop_name" value="" placeholder="'.esc_html__('Search a listing','wpresidence').'">
                    <input type="submit" class="wpresidence_button" id="search_form_submit_1" value="'.esc_html__('Search','wpresidence').'">';
                    wp_nonce_field( 'dashboard_searches', 'dashboard_searches_nonce');
            print'</form> ';
    print '</div>';


    print '<div class="wpestate_dashboard_table_list_header row">';



        $paid_submission_status         =   esc_html ( wpresidence_get_option('wp_estate_paid_submission','') );
        if ($paid_submission_status=='per listing'){
            print '<div class="col-md-3">'.esc_html__('Property','wpresidence').'</div>';
            print '<div class="col-md-2">'.esc_html__('Category','wpresidence').'</div>';
            print '<div class="col-md-2">'.esc_html__('Status','wpresidence').'</div>';
            print '<div class="col-md-2">'.esc_html__('Pay Status','wpresidence').'</div>';
            print '<div class="col-md-1">'.esc_html__('Price','wpresidence').'</div>';
        }else{
            print '<div class="col-md-5">'.esc_html__('Comments','wpresidence').'</div>';
            print '<div class="col-md-2">'.esc_html__('Stars','wpresidence').'</div>';
            print '<div class="col-md-3">'.esc_html__('Property','wpresidence').'</div>';
            print '<div class="col-md-2">'.esc_html__('Status','wpresidence').'</div>';
            // print '<div class="col-md-2">'.esc_html__('Reply','wpresidence').'</div>';
        }


        // print '<div class="col-md-2">'.esc_html__('Flag','wpresidence').'</div>';
    print'</div>';
    

    if( !$prop_selection->have_posts() ){
        print '<h4 style="margin-top:30px">'.esc_html__('You don\'t have any properties!','wpresidence').'</h4>';
    }else{

        while ($prop_selection->have_posts()): $prop_selection->the_post();
                include( locate_template('templates/dashboard-templates/dashboard_customer_reviews_view_single_review.php'));
        endwhile;


        $args2= array(
                'post_type'                 =>  'estate_property',
                'author__in'                =>  $agent_list,
                'posts_per_page'            => '-1' ,
                'post_status'               =>  array( 'any' ),
                'cache_results'             =>  false,
                'update_post_meta_cache'    =>  false,
                'update_post_term_cache'    =>  false,

                );
        $prop_selection2 = new WP_Query($args2);
        while ($prop_selection2->have_posts()): $prop_selection2->the_post();
                $autofill.= '"'.get_the_title().'",';
        endwhile;

        print '<script type="text/javascript">
            //<![CDATA[
                    jQuery(document).ready(function(){
                        var autofill=['.$autofill.']
                        jQuery( "#prop_name" ).autocomplete({
                        source: autofill
                    });
            });
            //]]>
            </script>';
        wpestate_pagination($prop_selection->max_num_pages, $range =2);

        
    }

}
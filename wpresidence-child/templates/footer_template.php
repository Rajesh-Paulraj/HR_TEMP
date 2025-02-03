<?php
$show_sticky_footer_select  =   wpresidence_get_option('wp_estate_show_sticky_footer','');
$footer_background          =   wpresidence_get_option('wp_estate_footer_background','url');
$repeat_footer_back_status  =   wpresidence_get_option('wp_estate_repeat_footer_back','');
$logo_header_type           =   wpresidence_get_option('wp_estate_logo_header_type','');
$footer_style               =   '';
$footer_back_class          =   '';

if ($footer_background!=''){
    $footer_style='style=" background-image: url('.esc_url($footer_background).') "';
}

if( $repeat_footer_back_status=='repeat' ){
    $footer_back_class = ' footer_back_repeat ';
}else if( $repeat_footer_back_status=='repeat x' ){
    $footer_back_class = ' footer_back_repeat_x ';
}else if( $repeat_footer_back_status=='repeat y' ){
    $footer_back_class = ' footer_back_repeat_y ';
}else if( $repeat_footer_back_status=='no repeat' ){
    $footer_back_class = ' footer_back_repeat_no ';
}

if($show_sticky_footer_select=='yes'){
    $footer_back_class.=' sticky_footer ';
}

if($logo_header_type=='type4'){
    $footer_back_class.= ' footer_header4 ';
}

$show_foot          =   wpresidence_get_option('wp_estate_show_footer','');
$wide_footer        =   wpresidence_get_option('wp_estate_wide_footer','');
$wide_footer_class  =   '';

if($show_foot==''){
    $show_foot='yes';
}

$post_id='';
if( isset($post->ID) ){
   $post_id =$post->ID;
}


  $wide_status     =   esc_html(wpresidence_get_option('wp_estate_wide_status',''));
  if($wide_status==''){
      $wide_status=1;
  }
  if($wide_status==2 || $wide_status==''){
      $footer_back_class.=" boxed_footer ";
  }


 ?>
 <footer id="colophon" <?php print wp_kses_post($footer_style); ?> class=" <?php print esc_attr($footer_back_class);?> ">
     <?php
     if($wide_footer=='yes'){
         $wide_footer_class=" wide_footer ";
     }
     ?>

     <div id="footer-widget-area" class="row <?php print esc_attr($wide_footer_class);?>">
        <!-- <--?php get_sidebar('footer');?> -->
        <div id="first" class="widget-area col-md-5 ">
                <ul class="xoxo">
                    <li id="text-15" class="widget-container widget_text">
                        <h4 class="widget-title-footer">ABOUT</h4>
                        <div class="textwidget">
                            <p>WpResidence is committed to delivering a high level of expertise, customer service, and attention to detail to the marketing and sales of luxury real estate, and rental properties.</p>
                            <p>Since 2013, we have developed powerful and fast real estate themes for&nbsp; businesses who need a reliable and extremely versatile product.</p>
                        </div>
                    </li>
                    <li id="social_widget-4" class="widget-container social_sidebar">
                    <div class="social_sidebar_internal">
                        <a href="#" target="_blank" aria-label="facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" target="_blank" aria-label="twitter"><i class="fa-brands fa-x-twitter"></i></a>
                        <!-- <a href="#" target="_blank" aria-label="pinterest"><i class="fab fa-pinterest-p  fa-fw"></i></a> -->
                        <a href="#" target="_blank" aria-label="youtube"><i class="fab fa-youtube  fa-fw"></i></a>
                        <!-- <a href="#" target="_blank" aria-label="vimeo"><i class="fab fa-vimeo-v  fa-fw"></i></a> -->
                        <a href="#" target="_blank" aria-label="instagram"><i class="fab fa-instagram  fa-fw"></i></a>
                    </div>
                    </li>
                </ul>
            </div>
            <div id="second" class="widget-area col-md-4">
                <ul class="xoxo">
                    <li id="contact_widget-2" class="widget-container contact_sidebar">
                        <h4 class="widget-title-footer">Contact</h4>
                        <div class="contact_sidebar_wrap">
                            <p class="widget_contact_addr"><i class="fas fa-building"></i>3C, 3rd Floor, <br>No. 22, Rainbow Arcade, <br>Thyagaraya Road, T-Nagar, <br>Chennai – 600017</p>
                            <p class="widget_contact_phone"><i class="fas fa-phone"></i><a href="tel:+919940040228">(+91) 994 004 0228</a></p>
                            <!-- <p class="widget_contact_fax"><i class="fas fa-print"></i>(+91) 790 499 7959</p> -->
                            <p class="widget_contact_email"><i class="far fa-envelope"></i><a href="mailto:write2us@homereviewz.in">write2us@homereviewz.in</a></p>
                            <!-- <p class="widget_contact_skype"><i class="fab fa-skype"></i>yourskypeid</p> -->
                            <!-- <p class="widget_contact_url"><i class="fas fa-desktop"></i><a href="SITE NAME">https://yoururl.com</a></p> -->
                        </div>
                    </li>
                </ul>
            </div>
            <div id="third" class="widget-area col-md-3">
                <ul class="xoxo">
                    <li id="property_categories-1" class="widget-container property_categories">
                        <h4 class="widget-title-footer">Menu</h4>
                        <div class="category_list_widget">
                            <ul>
                                <li><a href="/">Home</a></li>
                                <li><a href="all-properties">All Properties</a></li>
                                <li><a href="contact-us">Contact Us</a></li>
                                <li><a href="about-us">About Us</a></li>
                            </ul>
                        </div>
                    </li>
                </ul>
            </div>
        </div>


     <?php
     $show_show_footer_copy_select  =   wpresidence_get_option('wp_estate_show_footer_copy','');
     if($show_show_footer_copy_select=='yes'){
     ?>
         <div class="sub_footer">
             <div class="sub_footer_content <?php print esc_attr($wide_footer_class);?>">
                 <span class="copyright">
                     <?php
                     $message = stripslashes( esc_html (wpresidence_get_option('wp_estate_copyright_message', '')) );
                     if (function_exists('icl_translate') ){
                         $property_copy_text      =   icl_translate('wpestate','wp_estate_copyright_message', $message );
                         print esc_html($property_copy_text);
                     }else{
                         print esc_html($message);
                     }
                     ?>
                 </span>

                 <div class="subfooter_menu">
                     <?php
                         show_support_link();
                         wp_nav_menu( array(
                             'theme_location'    => 'footer_menu',
                         ));
                     ?>
                 </div>
             </div>
         </div>
     <?php
     }// end show subfooter
     ?>


 </footer><!-- #colophon -->

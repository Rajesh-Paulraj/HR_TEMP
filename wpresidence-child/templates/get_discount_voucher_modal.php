<?php
add_action( 'wp_footer', 'wpresidence_child_add_get_discount_voucher_modal_footer' );

function wpresidence_child_add_get_discount_voucher_modal_footer(){
    global $post;
    $front_end_register     =   esc_html( wpresidence_get_option('wp_estate_front_end_register','') );
    $front_end_login        =   esc_html( wpresidence_get_option('wp_estate_front_end_login ','') );
    $facebook_status        =   esc_html( wpresidence_get_option('wp_estate_facebook_login','') );
    $google_status          =   esc_html( wpresidence_get_option('wp_estate_google_login','') );
    $twiter_status           =   esc_html( wpresidence_get_option('wp_estate_twiter_login','') );
    $mess                   =   '';
    $security_nonce         =   wp_nonce_field( 'forgot_ajax_nonce-topbar', 'security-forgot-topbar',true,false );
    $background_modal       =   wpresidence_get_option('wp_estate_login_modal_image','url');

    if( $background_modal =='' ){
    $background_modal=  get_theme_file_uri('/img/defaults/modalback.jpg');
    }
    $recaptha_class="";
    if(wpresidence_get_option('wp_estate_use_captcha','')=='yes'){
      $recaptha_class="  wpestare_recaptcha_extra_class  ";
    }


    $custom_height=520;

    if( $facebook_status=='yes' || $google_status=='yes' || $twiter_status=='yes' ){
        $custom_height=550;
    }



    ?>

<?php
global $post;
$wpestate_options   =   wpestate_page_details($post->ID);
$current_user       =   wp_get_current_user();

$user_meta = get_user_meta( $current_user->ID );

?>

<!-- Modal -->
<div class="modal fade" id="get-discount-voucher-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Get Your Discount Coupon</h4>
			</div>
			<div class="modal-body">
                <div id="form-container" class="container">
                    <div class="row">	
                        <div class="col-md-12">
                            <div class="wpestate_dashboard_content_wrapper">
                                <div id="profile_message"></div>
                                <div class="add-estate profile-page profile-onprofile row">
                                    <!-- <div class="wpestate_dashboard_section_title">Contact Information</div> -->
                                    <div class="col-md-6 firstname_wrapper "><label for="firstname">First Name</label><input type="text" id="firstname" class="form-control" value="<?php echo esc_attr($user_meta['first_name'][0]); ?>" name="firstname"></div>
                                    <div class="col-md-6 secondname_wrapper "><label for="secondname">Last Name</label><input type="text" id="secondname" class="form-control" value="<?php echo esc_attr($user_meta['last_name'][0]); ?>" name="secondname"></div>
                                    <div class="col-md-6 userphone_wrapper "><label for="userphone">Phone</label><input type="text" id="userphone" class="form-control" value="<?php echo esc_attr($user_meta['phone'][0]); ?>" name="userphone"></div>
                                    <div class="col-md-6 useremail_wrapper "><label for="useremail">Email</label><input type="text" id="useremail" class="form-control" value="<?php echo esc_attr($current_user->user_email); ?>" name="useremail"></div>
                                    <!-- <div class="col-md-6 occupation_wrapper "><label for="occupation">Occupation</label><input type="text" id="occupation" class="form-control" value="" name="occupation"></div> -->
                                    <!-- <div class="col-md-6 budget_wrapper "><label for="budget">Budget (in Lakhs)</label><input type="text" id="budget" class="form-control" value="" name="budget"></div> -->
                                    <input type="hidden" id="security-profile" name="security-profile" value="e421660350"><input type="hidden" name="_wp_http_referer" value="/dashboard-profile-page/">          
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="coupon-container" class="container" style="display: none">
                    <div class="row">
                        <div class="login-alert login-alert-success">Copy of your Coupon sent to your email.</div>
                    </div>
                    <div class="row">
                        <!-- <img src="https://homereviewz.in/wp-content/uploads/2024/07/Voucher-1.png" alt="" width="100%" height="auto"> -->
                    </div>
                </div>
			</div>
			<div class="modal-footer">
				<!-- <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> -->
                <input type="text" id="enter-otp-input" name="" value="" placeholder="Enter OTP">

                <button type="button" onclick="sendOTP()" data-toggle="modal" data-target="" data-backdrop="static" data-keyboard="false" class="wpresidence_button get-discount-voucher-btn" id="get-otp-btn" style="width: auto">Get OTP</button>
                <button type="button" onclick="verifyOTP()" data-toggle="modal" data-target="" data-backdrop="static" data-keyboard="false" class="wpresidence_button get-discount-voucher-btn" id="verify-otp-btn" style="width: auto; display: none;">Verify OTP</button>

				<button id="get-discount-voucher-detail-submit-btn" type="button" class="wpresidence_button">Get Discount Coupon</button>
                <button id="get-discount-voucher-download-btn" type="button" class="wpresidence_button"  onclick='forceDownload(this)' data-href='https://homereviewz.in/wp-content/uploads/2024/11/Sample-Discount-Voucher-PDF.pdf' download="Coupon.pdf" style="display: none">Download Coupon</button>
			</div>
		</div>
	</div>
</div>

   
<?php
    }
?>

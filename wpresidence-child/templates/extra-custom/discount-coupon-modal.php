<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

function my_discount_coupon_modal() {
    if (!defined('DOING_AJAX') || !DOING_AJAX) {
        global $post;
        $property_id = isset($post->ID) ? $post->ID : 0;
        $property_name = get_the_title($property_id);
        $property_code = get_post_meta($property_id, 'property-code', true);
        $agent_id = get_post_meta($property_id, 'property_agent', true );
        if ( $agent_id ) {
            $builder_name = get_the_title( $agent_id );
        } else {
            $builder_name = '-';
        }
        $property_location = get_post_meta($property_id, 'property-address', true);
        $discount_percentage = get_post_meta($property_id, 'discount-percentage', true);
        if(empty($discount_percentage)) {
            $discount_percentage = "0"; // fallback if not set
        }

        ?>

        <!-- Discount Coupon Modal -->
        <div id="discount-coupon-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="discountCouponModalLabel" aria-hidden="true" data-property-id="<?php echo esc_attr($property_id); ?>">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="border-bottom:none;">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff;font-size:24px;opacity:1;">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title text-center" id="discountCouponModalLabel" style="width:100%;color:#fff;font-weight:bold;">Discount Coupon Details</h4>
                    </div>
                    <div class="modal-body" style="background:rgba(255,255,255,0.9); padding:20px;">
                        <!-- Property Summary -->
                        <div class="card mb-4 shadow-sm">
                            <div class="card-body p-3">
                                <ul class="list-unstyled mb-0">
                                    <li><strong>Property Name:</strong> <span id="property-name"><?php echo esc_html($property_name); ?></span></li>
                                    <li><strong>Property Code:</strong> <span id="property-code"><?php echo esc_html($property_code); ?></span></li>
                                    <li><strong>Builder Name:</strong> <span id="builder-name"><?php echo esc_html($builder_name); ?></span></li>
                                    <li><strong>Location:</strong> <span id="property-location"><?php echo esc_html($property_location); ?></span></li>
                                    <li><strong>Discount Percentage:</strong> <span id="discount-percentage"><?php echo esc_html($discount_percentage); ?></span>%</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Form -->
                        <div class="card mb-3 shadow-sm">
                            <div class="card-body">
                                <form id="discount-coupon-form">
                                    <input type="hidden" id="property-id" name="property_id" value="<?php echo esc_attr($property_id); ?>">
                                    <div class="form-group">
                                        <label for="name">Full Name <span style="color:red">*</span></label>
                                        <input type="text" class="form-control" id="name" name="name" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="mobile">Mobile Number <span style="color:red">*</span></label>
                                        <input type="text" class="form-control" id="mobile" name="mobile" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Email Address <span style="color:red">*</span></label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="occupation">Occupation <span style="color:red">*</span></label>
                                        <input type="text" class="form-control" id="occupation" name="occupation" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="bhk-size">BHK/Size <span style="color:red">*</span></label>
                                        <input type="text" class="form-control" id="bhk-size" name="bhk_size" required>
                                    </div>
                                    <div class="form-group form-check">
                                        <input type="checkbox" class="form-check-input" id="request-appointment">
                                        <label class="form-check-label" for="request-appointment">Request Appointment with Builder</label>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- OTP Section -->
                        <div class="card mb-3 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center flex-wrap mb-3">
                                    <div id="send-otp-to-mobile-div" class="form-check form-check-inline mr-2">
                                        <input class="form-check-input" type="radio" name="otp_method" id="send-otp-mobile" value="mobile" checked>
                                        <label class="form-check-label" for="send-otp-mobile">Send OTP to Mobile</label>
                                    </div>
                                    <div id="send-otp-to-email-div" class="form-check form-check-inline mr-2">
                                        <input class="form-check-input" type="radio" name="otp_method" id="send-otp-email" value="email">
                                        <label class="form-check-label" for="send-otp-email">Send OTP to Email</label>
                                    </div>
                                    <button type="button" id="send-otp-btn" class="btn btn-sm ml-auto" style="background-color:#f47920;color:#fff;border:none;">SEND OTP</button>
                                </div>

                                <div class="d-flex align-items-center flex-wrap">
                                    <input type="text" class="form-control mr-2" id="enter-otp" placeholder="Enter OTP" style="max-width:150px;">
                                    <button type="button" id="verify-otp-btn" class="btn btn-sm mr-2" style="background-color:#f47920;color:#fff;border:none;">VERIFY OTP</button>
                                </div>

                                <div class="d-flex align-items-center flex-wrap text-align-center mt-2">
                                    <button type="button" id="download-coupon" class="btn btn-warning btn-sm" disabled>Download Discount Coupon</button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}


add_action('wp_footer', 'my_discount_coupon_modal');
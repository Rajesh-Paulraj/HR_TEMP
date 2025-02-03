<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

add_action('wp_footer', 'wpresidence_child_add_discount_coupon_modal_footer');

function wpresidence_child_add_discount_coupon_modal_footer(){
    $background_modal = wpresidence_get_option('wp_estate_login_modal_image', 'url');
    if ($background_modal == '') {
        $background_modal = get_theme_file_uri('/img/defaults/modalback.jpg');
    }
?>
<!-- Modal -->
<div class="modal fade" id="giftVoucherModal" tabindex="-1" role="dialog" aria-labelledby="giftVoucherModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title text-center" id="giftVoucherModalLabel">Collect Your Gift Voucher</h4>
            </div>
            <div class="modal-body">
                <!-- Loading Section -->
                <div class="loading-spinner text-center" style="display: none;">
                    <div class="spinner">
                        <div></div>
                        <div></div>
                    </div>
                    <p>Checking your review status...</p>
                </div>

                <!-- Messages -->
                <div class="review-notice text-center alert alert-info" style="display: none;">
                    <p>Thank you for your interest in our gift voucher program! To avail a voucher, please leave a review with at least <strong>100 words</strong>.</p>
                </div>
                <div class="insufficient-words-notice text-center alert alert-danger" style="display: none;">
                    <p>Your review must have at least 100 words to avail a gift voucher.</p>
                </div>
                <div class="already-collected-notice text-center alert alert-warning" style="display: none;">
                    <p>You have already collected a gift voucher for this property.</p>
                </div>

                <!-- Voucher Options Section -->
                <div class="voucher-options d-flex flex-column align-items-center" style="display: none;">
                    <button class="voucher-option btn btn-outline-primary w-75 my-2" data-type="lifestyle">
                        <img src="https://homereviewz.in/wp-content/uploads/2024/11/lifestyle-gift-card.png" alt="lifestyle" class="img-fluid">
                    </button>
                    <button class="voucher-option btn btn-outline-primary w-75 my-2" data-type="croma">
                        <img src="https://homereviewz.in/wp-content/uploads/2024/11/croma-gift-card.png" alt="croma" class="img-fluid">
                    </button>
                    <button class="voucher-option btn btn-outline-primary w-75 my-2" data-type="amazon">
                        <img src="https://homereviewz.in/wp-content/uploads/2024/11/amazon-gift-card.png" alt="amazon" class="img-fluid">
                    </button>
                </div>

                <!-- Thanks You Message Section -->
                <div class="alert alert-success text-center thank-you-section">
                    <div style="padding-bottom: 20px;">Thank you!</div>
                    <div>Your voucher request has been sent. Please check your email for further instructions.</div>
                </div>
                    
            </div>
            <div class="modal-footer">
                <button id="proceedVoucherBtn" type="button" class="wpresidence_button" disabled>Proceed</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php
}
?>

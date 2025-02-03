<?php

// SITE REVIEW - CUSTOM SHORT CODE
function site_review_section(){

    // ***************************************************
    // ***************************************************
    // ***************************************************
    // These code should not be here. This code for eDiscountCoupon button enable/disable
    ?>
    <script type="text/javascript">
        var enableDiscountCoupon = "<?php echo trim(esc_html(get_post_meta(get_the_ID(), 'enable-discount-coupon', true))); ?>";
        var discountPercentage = "<?php echo trim(esc_html(get_post_meta(get_the_ID(), 'discount-percentage', true))); ?>";
    </script>
    <?php
    // ***************************************************
    // ***************************************************
    // ***************************************************

    echo '<button type="button" id="write-your-review-button" class="wpresidence_button get-discount-voucher-btn">Write Your Review</button>';
    // Check if enable_gift_voucher is not "No"
    echo '<button type="button" id="collect-gift-voucher-button" class="wpresidence_button get-discount-voucher-btn" style="margin: 10px;" data-toggle="modal" data-target="#giftVoucherModal" data-backdrop="static" data-keyboard="false">Collect Gift Voucher</button>';
//     echo '<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#discountCouponModal">
//   GET DISCOUNT COUPON
// </button>

// <button type="button" class="wpresidence_button " style="margin: 10px;" data-toggle="modal" data-target="#discount-coupon-modal" data-backdrop="static" data-keyboard="false">GET DISCOUNT COUPON</button>
// ';
    echo '<div id="signin-review-form-container" style="display:none;">';
    echo do_shortcode('[site_reviews_form form="19883" assigned_posts="post_id" assigned_users="user_id"]');
    echo '</div>';

    if ( is_user_logged_in() ) {
        if (current_user_can('contributor')) {
            ?>
            <script type="text/javascript">
                jQuery('#write-your-review-button').hide();
                jQuery('#collect-gift-voucher-button').hide();
            </script>
            <?php
        } else {
            ?>
            <script type="text/javascript">
                jQuery('.glsr-button.wp-block-button__link').addClass('wpresidence_button');
                jQuery(document).ready(function($) {
                    // $('#write-your-review-button').on('click', function() {
                    //     $('#signin-review-form-container').show(); // Show the review form
                    //     // $(this).hide(); // Hide the button
                    //     // jQuery('#collect-gift-voucher-button').hide();
                    // });
                    $('#write-your-review-button').on('click', function() {
                        $('#signin-review-form-container').toggle(); // Toggle the visibility of the review form
                    });
                });
            </script>
            <?php
        }
    } else {
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function($) {

                // Change the button text to "Sign in to Submit your review"
            $('#signin-review-form-container').find('button[type="submit"]').text('Sign in to Submit your review');

            // Optionally, disable the button to prevent form submission
            // $('#signin-review-form-container').find('button[type="submit"]').attr('disabled', true);

            // Add a click event to redirect to the sign-in page or show a sign-in modal
            $('#signin-review-form-container').find('button[type="submit"]').on('click', function(event) {
                event.preventDefault(); // Prevent the default action
                // Show the login modal
                jQuery("#modal_login_wrapper").show();
            });

            jQuery('.glsr-button.wp-block-button__link').addClass('wpresidence_button');
            // $('#write-your-review-button').on('click', function() {
            //     $('#signin-review-form-container').show(); // Show the review form
            //     // $(this).hide(); // Hide the button
            // });
            $('#write-your-review-button').on('click', function() {
                $('#signin-review-form-container').toggle(); // Toggle the visibility of the review form
            });

            });
        </script>
        <?php
    }

    // if ( is_user_logged_in() ) {
    //     // User is logged in, show the review form
    //     echo do_shortcode('[site_reviews_form form="19883" assigned_posts="' . $post_id . '"]');
    // } else {
    //     // User is not logged in, show the "Write Your Review" button
    //     echo '<button id="write-your-review-button">Write Your Review</button>';
        
    //     // Include a hidden div with the review form, initially hidden
    //     // echo '<div id="review-form-container" style="display:none;">';
    //     // echo do_shortcode('[site_reviews_form form="19883" assigned_posts="' . $post_id . '"]');
    //     // echo '</div>';

    //     // User is not logged in, show the "Sign in to Submit your review" button
    //     echo '<div id="signin-review-form-container" style="display:none;">';
    //     echo do_shortcode('[site_reviews_form form="19883" assigned_posts="' . $post_id . '"]');
    //     echo '</div>';
    
    //     // Add jQuery to handle the button click and display the form
    //     ?->
    //     <script type="text/javascript">
    //         jQuery(document).ready(function($) {

    //             // Change the button text to "Sign in to Submit your review"
    //         $('#signin-review-form-container').find('button[type="submit"]').text('Sign in to Submit your review');

    //         // Optionally, disable the button to prevent form submission
    //         // $('#signin-review-form-container').find('button[type="submit"]').attr('disabled', true);

    //         // Add a click event to redirect to the sign-in page or show a sign-in modal
    //         $('#signin-review-form-container').find('button[type="submit"]').on('click', function(event) {
    //             event.preventDefault(); // Prevent the default action
    //             // Show the login modal
    //             jQuery("#modal_login_wrapper").show();
    //         });

    //         $('#write-your-review-button').on('click', function() {
    //             $('#signin-review-form-container').show(); // Show the review form
    //             $(this).hide(); // Hide the button
    //         });
    //         });
    //     </script>
    //     <?php
    // }

    echo '<div style="margin-top: 30px;"></div>';
    echo do_shortcode('[site_reviews_summary filters="true" labels="5 star,4 star,3 star,2 star,1 star" assigned_posts="post_id" class="show-only-rating-star"]');
    echo do_shortcode('[site_reviews_summary filters="false" labels="5 star,4 star,3 star,2 star,1 star" assigned_posts="post_id" rating_field="location_rating" class="location_rating show-only-rating-star"]');
    echo do_shortcode('[site_reviews_summary filters="false" labels="5 star,4 star,3 star,2 star,1 star" assigned_posts="post_id" rating_field="property_rating" class="property_rating show-only-rating-star"]');
    echo do_shortcode('[site_reviews_summary filters="false" labels="5 star,4 star,3 star,2 star,1 star" assigned_posts="post_id" rating_field="community_rating" class="community_rating show-only-rating-star"]');
    echo do_shortcode('[site_reviews_summary filters="false" labels="5 star,4 star,3 star,2 star,1 star" assigned_posts="post_id" rating_field="price_rating" class="price_rating show-only-rating-star"]');
    echo do_shortcode('[site_reviews_summary filters="true" labels="5 star,4 star,3 star,2 star,1 star" assigned_posts="post_id" class="show-only-rating-5bar"]');
    
    echo '<div style="margin-top: 30px;"></div>';
    echo do_shortcode('[site_reviews_images assigned_posts="post_id"]');
    // echo do_shortcode('[site_reviews_filter pagination="ajax" class="my-reviews full-width"]');
    echo '<div style="margin-top: 30px;"></div>';
    echo do_shortcode('[site_reviews filters="true" pagination="ajax" theme="19884" form="19883" assigned_posts="post_id" display="20" fallback="No reviews yet! Be the first to share your thoughts and help others by writing a review."]');
    // echo do_shortcode('[site_reviews filters="true" pagination="ajax" assigned_posts="' . $post_id . '"]');
    // echo do_shortcode('[site_reviews_images assigned_posts="' . $post_id . '"]');
    
}

add_shortcode('site_review_section', 'site_review_section');
jQuery(document).ready(function ($) {
    let otpVerified = false;
    let otpTimer; // variable to hold the interval
    let otpCountdown = 30; // 30 seconds countdown

    // Handle form submission
    $('#discount-coupon-form').on('submit', function (e) {alert("I think not trigger");
        e.preventDefault();
        submitForm();
    });

    function submitForm() {
        const formData = {
            action: 'handle_coupon_submission',
            name: $('#name').val(),
            mobile: $('#mobile').val(),
            email: $('#email').val(),
            occupation: $('#occupation').val(),
            bhk_size: $('#bhk-size').val(),
            property_id: $('#discount-coupon-modal').data('property-id'),
            appointment: $('#request-appointment').is(':checked')
        };

        $.post(discountCouponData.ajax_url, formData, function (response) {
            if (response.success) {
                alert(response.data.message);
                if (response.data.pdf_url) {
                    window.open(response.data.pdf_url, '_blank');
                }
                $('#discount-coupon-modal').modal('hide');
            } else {
                alert('Failed to submit the form. Please try again.');
            }
        });
    }

    // Validate required fields
    function allFieldsValid() {
        if (!$('#name').val().trim() || !$('#mobile').val().trim() || !$('#email').val().trim() || !$('#occupation').val().trim() || !$('#bhk-size').val().trim()) {
            return false;
        }
        return true;
    }

    // Handle Send OTP
    jQuery('#send-otp-btn').on('click', function () {
        if (!allFieldsValid()) {
            alert('Please fill all mandatory fields before sending OTP.');
            return;
        }

        const otpMethod = jQuery('input[name="otp_method"]:checked').val();
        const contact = otpMethod === 'mobile' ? jQuery('#mobile').val() : jQuery('#email').val();

        if (!contact) {
            alert('Please provide a valid contact.');
            return;
        }

        jQuery.ajax({
            url: discountCouponData.ajax_url,
            method: 'POST',
            data: {
                action: 'send_otp',
                contact: contact,
                type: otpMethod
            },
            success(response) {
                if (response.success) {
                    alert('OTP sent successfully!');
                    startOtpTimer();
                } else {
                    alert('Failed to send OTP. Please try again.');
                }
            }
        });
    });

    // Start the 30 seconds timer for OTP button
    function startOtpTimer() {
        const $otpBtn = $('#send-otp-btn');
        $otpBtn.prop('disabled', true);
        otpCountdown = 30; 
        $otpBtn.text('Wait ' + otpCountdown + 's');

        otpTimer = setInterval(function() {
            otpCountdown--;
            $otpBtn.text('Wait ' + otpCountdown + 's');

            if (otpCountdown <= 0) {
                clearInterval(otpTimer);
                $otpBtn.text('SEND OTP');
                $otpBtn.prop('disabled', false);
            }
        }, 1000);
    }

    // Handle Verify OTP
    jQuery('#verify-otp-btn').on('click', function () {
        const otp = jQuery('#enter-otp').val();
        const contact = jQuery('#mobile').val() || jQuery('#email').val();

        if (!otp) {
            alert('Please enter OTP.');
            return;
        }

        jQuery.ajax({
            url: discountCouponData.ajax_url,
            method: 'POST',
            data: {
                action: 'verify_otp',
                contact: contact,
                otp: otp
            },
            success(response) {
                if (response.success) {
                    alert('OTP verified successfully!');
                    jQuery('#download-coupon').prop('disabled', false);
                    otpVerified = true;
                    disableFormFields();
                } else {
                    alert('Invalid OTP. Please try again.');
                }
            }
        });
    });

    // Disable form fields after OTP verification
    function disableFormFields() {
        $('#discount-coupon-form input, #discount-coupon-form textarea, #discount-coupon-form select').prop('disabled', true);
        $('#request-appointment').prop('disabled', true);
        $('input[name="otp_method"]').prop('disabled', true);
    }

    // Handle Download Coupon
    jQuery('#download-coupon').on('click', function () {
        if (!allFieldsValid()) {
            alert('Please fill all mandatory fields before downloading.');
            return;
        }

        if (!otpVerified) {
            alert('Please verify OTP first.');
            return;
        }

        const data = {
            name: jQuery('#name').val(),
            mobile: jQuery('#mobile').val(),
            email: jQuery('#email').val(),
            occupation: $('#occupation').val(),
            bhk_size: $('#bhk-size').val(),
            property_id: jQuery('#property-id').val(),
            action: 'handle_coupon_submission',
            appointment: jQuery('#request-appointment').is(':checked')
        };

        jQuery.ajax({
            url: discountCouponData.ajax_url,
            method: 'POST',
            data: data,
            success(response) {
                if (response.success) {
                    alert('Coupon generated successfully!');
                    window.open(response.data.pdf_url, '_blank');
                } else {
                    alert('Failed to generate coupon. Please try again.');
                }
            }
        });
    });

    // Hide or show button based on enableDiscountCoupon
    if ((typeof enableDiscountCoupon !== 'undefined' && enableDiscountCoupon === "No") || !discountPercentage|| discountPercentage == 0) {
        $('#wp-child-get-discount-voucher-btn').hide();
    } else {
        $('#wp-child-get-discount-voucher-btn').show();
    }
});

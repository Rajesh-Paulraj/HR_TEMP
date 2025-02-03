$ = jQuery;

jQuery(document).ready(function ($) {
    if ($('textarea[name="site-reviews[content]"]').length) {
        $('textarea[name="site-reviews[content]"]').attr('placeholder', 'Kindly write your review with a minimum of 100 words to avail a gift voucher');
    }

    $('#collect-gift-voucher-button').on('click', function () {
        var ajaxurl = ajaxcalls_vars.admin_url + 'admin-ajax.php';
        var currentPropertyId = $('input[name="site-reviews[_post_id]"]').val();

        if (!currentPropertyId) {
            alert('Property ID is missing.');
            return;
        }

        $('#giftVoucherModal .review-notice, #giftVoucherModal .voucher-options, #giftVoucherModal .insufficient-words-notice, #giftVoucherModal .already-collected-notice').hide();
        $('#giftVoucherModal .loading-spinner').show();
        $('#giftVoucherModal .modal-header').show();
        $('#proceedVoucherBtn').hide();
        $('#giftVoucherModal .thank-you-section').hide();
        

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'check_review_status',
                property_id: currentPropertyId,
            },
            success: function (response) {
                $('#giftVoucherModal .loading-spinner').hide();

                if (response.success) {
                    $('#giftVoucherModal .voucher-options').show();
                    $('#proceedVoucherBtn').show();
                } else if (response.data.message === 'Your review must have at least 100 words to avail a gift voucher.') {
                    $('#giftVoucherModal .insufficient-words-notice').show();
                } else if (response.data.message === 'You have already collected a gift voucher for this property.') {
                    $('#giftVoucherModal .already-collected-notice').show();
                    $('#giftVoucherModal .modal-header').hide();
                    $('#proceedVoucherBtn').hide();
                } else {
                    $('#giftVoucherModal .review-notice').show();
                }
            },
            error: function () {
                $('#giftVoucherModal .loading-spinner').hide();
                $('#giftVoucherModal .modal-body').append('<div class="alert alert-danger text-center">An error occurred. Please try again.</div>');
            },
        });
    });

    $('.voucher-option').on('click', function () {
        let selectedVoucher = $(this).data('type');
        $('#proceedVoucherBtn').prop('disabled', false);
        $('.voucher-option').removeClass('selected');
        $(this).addClass('selected');

        var currentPropertyId = $('input[name="site-reviews[_post_id]"]').val();

        $('#proceedVoucherBtn').off('click').on('click', function () {
            var ajaxurl = ajaxcalls_vars.admin_url + 'admin-ajax.php';
            if (!selectedVoucher) return;

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'process_gift_voucher',
                    voucher_type: selectedVoucher,
                    property_id: currentPropertyId,
                },
                beforeSend: function () {
                    $('#proceedVoucherBtn').text('Processing...').prop('disabled', true);
                },
                success: function (response) {
                    $('.voucher-options').hide();
                    $('#giftVoucherModal .thank-you-section').show();
                    $('#proceedVoucherBtn').hide();
                },
                error: function () {
                    $('.modal-body').append(
                        `<div class="alert alert-danger text-center">
                            Oops! Something went wrong. Please try again.
                         </div>`
                    );
                    $('#proceedVoucherBtn').text('Proceed').prop('disabled', false);
                },
            });
        });
    });
});

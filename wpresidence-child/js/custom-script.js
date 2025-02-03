
$ = jQuery;

jQuery(document).ready(function ($) {
    // Check if the URL contains the action=loginpopup parameter
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('action') === 'loginpopup') {
        // Trigger the click event on the `.submit_action` element inside `#user_menu_u.user_not_loged`
        const submitAction = $('#user_menu_u.user_not_loged .submit_action');
        if (submitAction.length) {
            jQuery('.login-links').show();
            jQuery('#modal_login_wrapper').show();
            jQuery('#modal_login_wrapper').find('[autofocus]').focus();
        }
    }
});



jQuery('#wp-child-get-discount-voucher-btn').on( 'click', function(event) {

    // Delete start
    $('#form-container').show();
    $('#coupon-container').hide();

    $('#enter-otp-input').text("");
    $('#enter-otp-input').hide();

    jQuery('#get-otp-btn').text("Get OTP");
    $('#get-otp-btn').prop('disabled', false);
    $('#get-otp-btn').show();

    $('#get-discount-voucher-detail-submit-btn').show();
    $('#get-discount-voucher-detail-submit-btn').prop('disabled', true);
    $('#get-discount-voucher-download-btn').hide();
    // Delete end

    // jQuery('.login-links').show();
    jQuery('#get-discount-voucher_modal_wrapper').show();
    jQuery('#modal_login_wrapper').find('[autofocus]').focus();
});

jQuery('#get-otp-btn').on( 'click', function(event) {
    jQuery('#get-otp-btn').hide();
    jQuery('#verify-otp-btn').show();
    console.log(jQuery('#get-otp-btn').text());
    if (jQuery('#get-otp-btn').text() == "Get OTP") {
        $('#enter-otp-input').show();
        jQuery('#get-otp-btn').text("Verify Phone");
    } else if (jQuery('#get-otp-btn').text() == "Verify Phone") {
        jQuery('#get-otp-btn').text("Phone Number Verified");
        $('#enter-otp-input').hide();
        $('#get-otp-btn').prop('disabled', true);
        $('#get-discount-voucher-detail-submit-btn').prop('disabled', false);
    } else {
    }
});


// DELETE START
$('#get-discount-voucher-detail-submit-btn').on('click', function () {
    $('#form-container').hide();
    $('#coupon-container').show();
    $('#enter-otp-input').hide();
    $('#get-otp-btn').hide();
    $('#get-discount-voucher-detail-submit-btn').hide();
    $('#get-discount-voucher-download-btn').show();
});

function forceDownload(link){
    var url = link.getAttribute("data-href");
    var fileName = link.getAttribute("download");
    link.innerText = "Working...";
    var xhr = new XMLHttpRequest();
    xhr.open("GET", url, true);
    xhr.responseType = "blob";
    xhr.onload = function(){
        var urlCreator = window.URL || window.webkitURL;
        var imageUrl = urlCreator.createObjectURL(this.response);
        var tag = document.createElement('a');
        tag.href = imageUrl;
        tag.download = fileName;
        document.body.appendChild(tag);
        tag.click();
        document.body.removeChild(tag);
        link.innerText="Download Voucher";
    }
    xhr.send();
}
// DELETE END

// $('#wp-login-but-topbar').on('click', function () {
//     wpestate_login_topbar2();
// });

// $('#login_pwd_topbar, #login_user_topbar').keydown(function (e) {
//     if (e.keyCode === 13) {
//         e.preventDefault();
//         wpestate_login_topbar2();
//     }
// });


// function wpestate_login_topbar2() {
//     "use strict";
//     var login_user, login_pwd, ispop, ajaxurl, security, ispop;

//     login_user = jQuery('#login_user_topbar').val();
//     login_pwd = jQuery('#login_pwd_topbar').val();
//     security = jQuery('#security-login-topbar').val();
//     ajaxurl = ajaxcalls_vars.admin_url + 'admin-ajax.php';
//     ispop = jQuery('#loginpop').val();

//     if (jQuery('#loginpop_submit').val() === '3') {
//         ispop = 3;
//     }




//     jQuery('#login_message_area_topbar').empty().append('<div class="login-alert">' + ajaxcalls_vars.login_loading + '</div>');
//     var nonce = jQuery('#wpestate_ajax_log_reg').val();
//     jQuery.ajax({
//         type: 'POST',
//         dataType: 'json',
//         url: ajaxurl,
//         data: {
//             'action': 'wpestate_ajax_loginx_form_topbar',
//             'login_user': login_user,
//             'login_pwd': login_pwd,
//             'ispop': ispop,
//             'security': nonce
//         },

//         success: function (data) {

//             var extra_class='';
//             if (data.loggedin === true) {
//                 extra_class='wpestate_succes';
//             }
//             jQuery('#login_message_area_topbar').empty().append('<div class="login-alert '+extra_class+' ">' + data.message + '<div>');
            
//             if (data.loggedin === true) {
//                 if (parseInt(data.ispop, 10) === 1) {

//                     ajaxcalls_vars.userid = data.newuser;

//                     jQuery('#user_menu_u.user_not_loged').unbind('click');
//                     jQuery('#user_menu_u').removeClass('user_not_loged').addClass('user_loged');
//                     jQuery('#modal_login_wrapper').hide();
//                     wp_estate_update_menu_bar(data.newuser);
//                     wpestate_open_menu();
//                 } else if (parseInt(data.ispop, 10) === 2) {
//                     location.reload();
//                 } else if (parseInt(data.ispop, 10) === 3) {
//                     ajaxcalls_vars.userid = data.newuser;
//                     jQuery('#user_menu_u.user_not_loged').unbind('click');
//                     jQuery('#user_menu_u').removeClass('user_not_loged').addClass('user_loged');
//                     jQuery('#modal_login_wrapper').hide();
//                     wp_estate_update_menu_bar(data.newuser);
//                     wpestate_open_menu();
//                 } else {
//                     document.location.href = ajaxcalls_vars.login_redirect;
//                 }


//             } else {
//                 jQuery('#login_user').val('');
//                 jQuery('#login_pwd').val('');
//             }
//         },
//         error: function (errorThrown) {

//         }
//     });
// }

// Site review customization scripts - START

$('.glsr-bar-filter').on('click', function(event) {
    event.preventDefault(); // Prevent the default action of the anchor tag
     // Stop the event from bubbling up
     event.stopPropagation();
    
    // Get the rating level from the data-level attribute
    var rating = $(this).data('level');

    // Set the selected option in the dropdown
    $('#filter_by_rating').val(rating);

    // Trigger a click on the submit button
    $('.wp-block-search__button.glsr-button').click();
});

// Site review customization scripts - END

// Google review customization scripts - START
$(".wprev_banner_top_source").contents().filter(function() {
    return this.nodeType === 3; // Node type 3 indicates a text node
}).remove();

// $(".wppro_banner_icon").removeAttr("src");
// $(".wprev_banner_top").off("click");
// $(".wprev_banner_top_source").off("click");
// $(".wppro_banner_icon").off("click");

$(".wprev_banner_top span:first").css("display", "none");

$(".glsr-custom-youtubeurl").each(function() {
    var linkElement = $(this).find("a");
    var url = linkElement.attr("href");
    
    if (isYouTubeUrl(url)) {
        var videoId = getYouTubeVideoId(url);
        if (videoId) {
            var embedUrl = "https://www.youtube.com/embed/" + videoId;
            var iframe = $("<iframe>")
                // .attr("width", "560")
                .attr("height", "200")
                .attr("src", embedUrl)
                .attr("frameborder", "0")
                .attr("allow", "accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture")
                .attr("allowfullscreen", true);
            linkElement.replaceWith(iframe);
        }
    } else {
        $(this).parent().hide(); // Hide the entire div if the URL is not a YouTube URL
    }
});

function isYouTubeUrl(url) {
    var youtubeRegex = /^(https?:\/\/)?(www\.)?(youtube\.com\/watch\?v=|youtu\.be\/)[\w-]{11}/;
    return youtubeRegex.test(url);
}

function getYouTubeVideoId(url) {
    var videoId = null;
    var urlParts = url.split('v=');
    if (urlParts.length > 1) {
        videoId = urlParts[1].split('&')[0];
    } else {
        var shortUrlParts = url.split('youtu.be/');
        if (shortUrlParts.length > 1) {
            videoId = shortUrlParts[1].split('?')[0];
        }
    }
    return videoId;
}

// 
document.addEventListener("DOMContentLoaded", function() {
    // Retrieve the saved values from sessionStorage
    // const savedRating = sessionStorage.getItem("savedRating");
    const savedInput = sessionStorage.getItem("savedInput");
    const savedTextarea = sessionStorage.getItem("savedTextarea");
    const savedForPostId = sessionStorage.getItem("savedForPostId");

    if (savedForPostId && (savedForPostId == postId)) {
        // if (savedRating) {
        //     // Set the data-rating attribute to the saved value
        //     const starRatingElement = document.querySelector(".glsr-star-rating--stars");
        //     starRatingElement.setAttribute("data-rating", savedRating);
    
        //     // Update the star classes based on the saved rating
        //     const stars = starRatingElement.querySelectorAll("span");
        //     stars.forEach((star, index) => {
        //         if (index < savedRating) {
        //             star.classList.add("gl-active");
        //             if (index == savedRating - 1) {
        //                 star.classList.add("gl-selected");
        //             }
        //         } else {
        //             star.classList.remove("gl-active", "gl-selected");
        //         }
        //     });
        // }
    
        if (savedInput) {
            // Set the input value to the saved value using the name attribute
            const inputElement = document.querySelector("input[name='site-reviews[title]']");
            inputElement.value = savedInput;
        }
    
        if (savedTextarea) {
            // Set the textarea value to the saved value
            const textareaElement = document.querySelector(".glsr-textarea");
            textareaElement.value = savedTextarea;
        }
    }

    // Save the current values before the page refresh
    window.addEventListener("beforeunload", function() {
        if (!isUserLoggedIn) {
            // const currentRating = document.querySelector(".glsr-star-rating--stars").getAttribute("data-rating");
            const currentInput = document.querySelector("input[name='site-reviews[title]']").value;
            const currentTextarea = document.querySelector(".glsr-textarea").value;
    
            // sessionStorage.setItem("savedRating", currentRating);
            sessionStorage.setItem("savedInput", currentInput);
            sessionStorage.setItem("savedTextarea", currentTextarea);
            sessionStorage.setItem("savedForPostId", postId);
        } else {
            // sessionStorage.removeItem("savedRating");
            sessionStorage.removeItem("savedInput");
            sessionStorage.removeItem("savedTextarea");
            sessionStorage.removeItem("savedForPostId");
        }
    });

    // Add event listeners to update rating on click
    // const starElements = document.querySelectorAll(".glsr-star-rating--stars span");
    // starElements.forEach((star, index) => {
    //     star.addEventListener("click", function() {
    //         const rating = index + 1;
    //         const starRatingElement = document.querySelector(".glsr-star-rating--stars");
    //         starRatingElement.setAttribute("data-rating", rating);

    //         // Update the stars visually
    //         starElements.forEach((s, i) => {
    //             s.classList.toggle("gl-active", i < rating);
    //             s.classList.toggle("gl-selected", i === index);
    //         });
    //     });
    // });
});


// Mobile OTP - START

// Function to send OTP
function sendOTP() {
    const mobile = document.getElementById('userphone').value;

    if (!mobile || !mobile.trim()) {
        alert('Please enter mobile number.');
        return;
    }

    // AJAX request to send OTP via WordPress AJAX
    fetch('/wp-admin/admin-ajax.php?action=send_otp', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ mobile })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // alert('OTP sent to your mobile number.');
            document.getElementById('otpSection').style.display = 'block';
        } else {
            // alert('Error sending OTP: ' + data.data);
            // alert('OTP sent failed. Please try after some time.');
        }
    });
}

// Function to verify OTP
function verifyOTP() {
    const otp = document.getElementById('enter-otp-input').value; // Assuming you have an input field with id="otpInput"

    if (!otp || !otp.trim()) {
        alert('Please enter OTP.');
        return;
    }

    jQuery.ajax({
        type: 'POST',
        url: '/wp-admin/admin-ajax.php?action=verify_otp',
        dataType: 'json',
        data: {
            'otp'                    :   otp
        },
        success: function (data) {     
            if (data.success) {
                // alert('OTP verified successfully!');
                jQuery('#verify-otp-btn').text("Phone Number Verified");
                $('#enter-otp-input').hide();
                $('#verify-otp-btn').prop('disabled', true);
                $('#get-discount-voucher-detail-submit-btn').prop('disabled', false);
                // Proceed with the next step after verification, like form submission
            } else {
                // alert('Error verifying OTP: ' + data.data);
                alert('Invalid OTP. Please try again.');
            }
        },
        error: function (errorThrown) { 
            alert('Error. Please try again.');
        }
    });//end ajax   

    // jQuery.ajax({
    //     type: 'POST',
    //     url: '/wp-admin/admin-ajax.php?action=verify_otp',
    //     data: {
    //         'otp'        :   otp
    //     },
    //     success: function (data) {
    //         if (data.success) {
    //             // alert('OTP verified successfully!');
    //             jQuery('#verify-otp-btn').text("Phone Number Verified");
    //             $('#enter-otp-input').hide();
    //             $('#verify-otp-btn').prop('disabled', true);
    //             $('#get-discount-voucher-detail-submit-btn').prop('disabled', false);
    //             // Proceed with the next step after verification, like form submission
    //         } else {
    //             // alert('Error verifying OTP: ' + data.data);
    //             alert('Invalid OTP. Please try again.');
    //         }
    //     },
    //     error: function (errorThrown) {
    //         alert('Invalid OTP. Please try again.');
    //     }
    // });//end ajax

    // AJAX request to verify OTP via WordPress AJAX
    // fetch('/wp-admin/admin-ajax.php?action=verify_otp', {
    //     method: 'POST',
    //     headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    //     body: new URLSearchParams({ otp })
    // })
    // // .then(response => response.json())
    // .then(data => {
    //     if (data.ok && data.success) {
    //         // alert('OTP verified successfully!');
    //         jQuery('#verify-otp-btn').text("Phone Number Verified");
    //         $('#enter-otp-input').hide();
    //         $('#verify-otp-btn').prop('disabled', true);
    //         $('#get-discount-voucher-detail-submit-btn').prop('disabled', false);
    //         // Proceed with the next step after verification, like form submission
    //     } else {
    //         // alert('Error verifying OTP: ' + data.data);
    //         // alert('Invalid OTP. Please try again.');
    //     }
    // });
}

// Mobile OTP - END








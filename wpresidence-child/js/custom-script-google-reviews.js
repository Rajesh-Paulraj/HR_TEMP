
$ = jQuery;


// Bug Fix - After page load in Googe review input box get auto populated. But it should not.
jQuery(document).ready(function($) {
    setTimeout(function() {
        $('#wprevpro_header_search_input').val('');
    }, 10);
    setTimeout(function() {
        $('#wprevpro_header_search_input').val('');
    }, 100);
    setTimeout(function() {
        $('#wprevpro_header_search_input').val('');
    }, 500);
    setTimeout(function() {
        $('#wprevpro_header_search_input').val('');
    }, 1000);
    setTimeout(function() {
        $('#wprevpro_header_search_input').val('');
    }, 2000);
});


function scrollToSiteAndGoogleReviewSection() {
    // Find the target element by ID
    const targetElement = document.getElementById("site-and-google-review-section");

    if (targetElement) {
        // Smooth scroll to the target element
        targetElement.scrollIntoView({ behavior: "smooth" });
    }
}

// When click on the Google Review Rating in Overview section, its should move to respective respective section
document.getElementById("custom-google-review-star-rating").addEventListener("click", function() {
    scrollToSiteAndGoogleReviewSection();
});

// When click on the Site Review Rating in Overview section, its should move to respective respective section
document.getElementById("custom-home-review-star-rating").addEventListener("click", function() {
    scrollToSiteAndGoogleReviewSection();
});

// When click on the Site Review Rating in Overview section, its should move to respective respective section
document.getElementById("custom-consolidated-star-rating").addEventListener("click", function() {
    scrollToSiteAndGoogleReviewSection();
});



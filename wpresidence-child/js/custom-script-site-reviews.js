
$ = jQuery;


// When click on the "Submit Review" button shown the warning popup if the review words less than 100 words
(function () {
    const originalOpen = XMLHttpRequest.prototype.open;
    XMLHttpRequest.prototype.open = function (method, url, async, user, password) {
        this._url = url;
        originalOpen.call(this, method, url, async, user, password);
    };

    const originalSend = XMLHttpRequest.prototype.send;
    XMLHttpRequest.prototype.send = function (body) {
        // Check if the request is the review submission
        if (this._url.includes('admin-ajax.php') && body) {
            let isReviewSubmission = false;
            let reviewContent = '';

            // Handle body depending on its type
            if (body instanceof FormData) {
                for (let pair of body.entries()) {
                    if (pair[0] === 'site-reviews[_action]' && pair[1] === 'submit-review') {
                        isReviewSubmission = true;
                    }
                    if (pair[0] === 'site-reviews[content]') {
                        reviewContent = pair[1].trim();
                    }
                }
            } else if (typeof body === 'string') {
                const formData = new URLSearchParams(body);
                if (formData.get('site-reviews[_action]') === 'submit-review') {
                    isReviewSubmission = true;
                    reviewContent = formData.get('site-reviews[content]').trim();
                }
            }

            // Validate review word count if it's a review submission
            if (isReviewSubmission) {
                const wordCount = reviewContent.split(/\s+/).filter(word => word.length > 0).length;

                if (wordCount < 100) {
                    Swal.fire({
                        title: 'Warning',
                        html: 'Your review contains fewer than 100 words. To qualify for the gift coupon, we encourage you to expand your review to at least 100 words.<br><br>Do you still wish to proceed with the submission?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, Submit',
                        cancelButtonText: 'No, Go Back',
                        allowOutsideClick: false // Prevent closing the popup when clicking outside
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Allow the request to proceed if user confirms
                            originalSend.call(this, body);
                        } else {
                            // If user clicks "No," reset the button to its original state
                            const submitButton = document.querySelector('button.glsr-button[aria-busy="true"]');
                            if (submitButton) {
                                submitButton.setAttribute('aria-busy', 'false');
                                submitButton.removeAttribute('disabled');
                                submitButton.innerHTML = 'Submit Your Review';
                            }
                        }
                    });

                    return; // Stop the request if word count is insufficient
                }
            }
        }

        // Proceed with the request if not intercepted
        originalSend.call(this, body);
    };
})();


// Refresh Page after user submit review - START
jQuery(document).ready(function($) {
    // Select the target div where the class change happens
    var targetNode = document.querySelector('.glsr-form-message');

    // Check if the target div exists on the page
    if (targetNode) {
        // Options for the observer (we watch for attribute changes)
        var config = { attributes: true, childList: false, subtree: false };

        // Callback function to execute when mutations are observed
        var callback = function(mutationsList, observer) {
            mutationsList.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                    // Check if the class 'glsr-form-success' was added
                    if (mutation.target.classList.contains('glsr-form-success')) {
                        // Refresh the page when the success message appears
                        // Show the Thank You popup
                        Swal.fire({
                            title: 'Thank You!',
                            text: 'Your review has been submitted successfully.',
                            icon: 'success',
                            showCancelButton: false,
                            confirmButtonText: 'Close',
                            allowOutsideClick: false // Prevent closing the popup when clicking outside
                        }).then((result) => {
                            // Reload the page to show updated comments
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
                    }
                }
            });
        };

        // Create an observer instance linked to the callback function
        var observer = new MutationObserver(callback);

        // Start observing the target node for configured mutations
        observer.observe(targetNode, config);
    }
});

// Refresh Page after user submit review - END


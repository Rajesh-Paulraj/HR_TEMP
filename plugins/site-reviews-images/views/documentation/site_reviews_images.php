<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="shortcode-site_reviews_images">
            <span class="title">Display the review images</span>
            <span class="badge code">[site_reviews_images]</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="shortcode-site_reviews_images" class="inside">
        <h3>This shortcode displays your review images.</h3>
        <div class="glsr-notice-inline components-notice is-info">
            <p class="components-notice__content">Each example below demonstrates a different shortcode option. If you need to use multiple options, simply combine the options together (separated with a space) in the same shortcode.</p>
        </div>
        <?php
            $options = [
                trailingslashit(__DIR__).'site_reviews_images/assigned_posts.php',
                trailingslashit(__DIR__).'site_reviews_images/assigned_terms.php',
                trailingslashit(__DIR__).'site_reviews_images/assigned_users.php',
                trailingslashit(__DIR__).'site_reviews_images/class.php',
                trailingslashit(__DIR__).'site_reviews_images/display.php',
                trailingslashit(__DIR__).'site_reviews_images/id.php',
                trailingslashit(__DIR__).'site_reviews_images/rating.php',
            ];
            $filename = pathinfo(__FILE__, PATHINFO_FILENAME);
            $options = glsr()->filterArrayUnique('documentation/shortcodes/'.$filename, $options);
            foreach ($options as $option) {
                include $option;
            }
        ?>
    </div>
</div>

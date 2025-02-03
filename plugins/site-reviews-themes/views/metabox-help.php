<?php defined('WPINC') || die; ?>

<p><?= _x('Display your reviews with this theme:', 'admin-text', 'site-reviews-themes'); ?></p>
<div data-tippy-content="Use the <code>theme</code> option to display your reviews using this theme." data-tippy-allowhtml="1" data-tippy-delay="[150,null]"></span>
    <?= $site_reviews; ?>
</div>

<p><?= _x('Display the rating summary using your chosen rating image and colours:', 'admin-text', 'site-reviews-themes'); ?></p>
<div data-tippy-content="Use the <code>theme</code> option with the summary shortcode to use the theme's Rating Image and Rating Colours." data-tippy-allowhtml="1" data-tippy-delay="[150,null]"></span>
    <?= $site_reviews_summary; ?>
</div>

<p><?= _x('Display the review form using your chosen rating image and colours:', 'admin-text', 'site-reviews-themes'); ?></p>
<div data-tippy-content="Use the <code>theme</code> option with the form shortcode to use the theme's Rating Image and Rating Colours." data-tippy-allowhtml="1" data-tippy-delay="[150,null]"></span>
    <?= $site_reviews_form; ?>
</div>


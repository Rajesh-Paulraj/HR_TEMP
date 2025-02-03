<?php 

// @todo fix this dirty hack!
// $display = array_keys(glsr('Addon\Filters\Shortcodes\SiteReviewsFilterShortcode')->getDisplayOptions());
$display = array_keys(glsr('Addon\Filters\Application')->config('forms/filters-form')); 
sort($display);
$display = implode(',', $display);

?>
<p class="glsr-heading">filters</p>
<div class="components-notice is-warning">
    <p class="components-notice__content">The <a href="<?= glsr_admin_url('addons'); ?>">Review Filters</a> add-on is required to use this shortcode option.</p>
</div>
<p>Include the "filters" option to display the review filters and search bar above the reviews. If you want to display all of the filters, you can also just enter <code>true</code> as the value instead of typing them all in.</p>
<p>The default filters value is: <code>""</code></p>
<div class="shortcode-example">
    <input type="text" readonly class="code" value='[site_reviews filters="<?= $display; ?>"]'>
    <pre><code class="syntax-shortcode"><span class="tag">[site_reviews</span> <span class="attr-name">filters</span>=<span class="attr-value">"<?= $display; ?>"</span><span class="tag">]</span></code></pre>
</div>

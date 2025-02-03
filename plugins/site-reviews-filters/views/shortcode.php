<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="shortcode-site_reviews_filter">
            <span class="title">Filter the reviews</span>
            <span class="badge code">[site_reviews_filter]</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="shortcode-site_reviews_filter" class="inside">
        <h3>This shortcode filters your reviews.</h3>

        <div class="components-notice is-info">
            <p class="components-notice__content">Each example below demonstrates a different shortcode option. If you need to use multiple options, simply combine the options together (separated with a space) in the same shortcode.</p>
        </div>

        <p class="glsr-heading">class</p>
        <p>Include the "class" option to add custom CSS classes to the shortcode.</p>
        <p>The default class value is: <code>""</code></p>
        <div class="shortcode-example">
            <input type="text" readonly class="code" value='[site_reviews_filter class="my-reviews full-width"]'>
            <pre><code class="syntax-shortcode"><span class="tag">[site_reviews_filter</span> <span class="attr-name">class</span>=<span class="attr-value">"my-reviews full-width"</span><span class="tag">]</span></code></pre>
        </div>

        <p class="glsr-heading">hide</p>
        <p>Include the "hide" option to hide any specific fields you don't want to show. If all fields are hidden, the shortcode will not be displayed.</p>
        <p>The default hide value is: <code>""</code></p>
        <?php $hide = array_keys(glsr('Addon\Filters\Application')->config('forms/filters-form')); sort($hide); ?>
        <div class="shortcode-example">
            <input type="text" readonly class="code" value='[site_reviews_filter hide="<?= implode(',', $hide); ?>"]'>
            <pre><code class="syntax-shortcode"><span class="tag">[site_reviews_filter</span> <span class="attr-name">hide</span>=<span class="attr-value">"<?= implode(',', $hide); ?>"</span><span class="tag">]</span></code></pre>
        </div>

        <p class="glsr-heading">id</p>
        <p>Include the "id" option to add a custom HTML id attribute to the shortcode. This may be useful when using pagination with the ajax option.</p>
        <p>The default id value is: <code>""</code></p>
        <div class="shortcode-example">
            <input type="text" readonly class="code" value='[site_reviews_filter id="type-some-random-text-here"]'>
            <pre><code class="syntax-shortcode"><span class="tag">[site_reviews_filter</span> <span class="attr-name">id</span>=<span class="attr-value">"type-some-random-text-here"</span><span class="tag">]</span></code></pre>
        </div>

        <p class="glsr-heading">reviews_id</p>
        <p>Include the "reviews_id" option to link the filters shortcode to a specific [site_reviews] shortcode which uses that id option value. Using this option will also enable AJAX filtering.</p>
        <p>The default reviews_id value is: <code>""</code></p>
        <div class="shortcode-example">
            <input type="text" readonly class="code" value='[site_reviews_filters reviews_id="filtered-reviews"]'>
            <pre><code class="syntax-shortcode"><span class="tag">[site_reviews_filters</span> <span class="attr-name">reviews_id</span>=<span class="attr-value">"filtered-reviews"</span><span class="tag">]</span></code></pre>
        </div>
        <div class="shortcode-example">
            <input type="text" readonly class="code" value='[site_reviews id="filtered-reviews"]'>
            <pre><code class="syntax-shortcode"><span class="tag">[site_reviews</span> <span class="attr-name">id</span>=<span class="attr-value">"filtered-reviews"</span><span class="tag">]</span></code></pre>
        </div>
    </div>
</div>

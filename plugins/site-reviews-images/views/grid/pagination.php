<?php defined('WPINC') || die; ?>

<div class="glsr-modal-nav">
    <div class="wp-block-button">
        <button data-prev type="button" class="glsr-button btn btn-primary button et_pb_button wp-block-button__link" aria-label="<?= __('Previous Page', 'site-reviews-images'); ?>" data-page="<?= $page > 1 ? $page - 1 : 1; ?>"<?php if ($page === 1) { echo ' disabled'; } ?>>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                <path fill="currentColor" d="M7.828 11H20v2H7.828l5.364 5.364-1.414 1.414L4 12l7.778-7.778 1.414 1.414z"/>
            </svg>
        </button>
    </div>
    <div class="glsr-modal-pagination">
        <label class="screen-reader-text" for="glsri-pagination"><?= __('Go to page', 'site-reviews-images'); ?></label>
        <input id="glsri-pagination" type="number" min="1" max="<?= $max_pages; ?>" value="<?= $page; ?>" aria-describedby="glsri-pagination-inputhint"<?= 1 === $max_pages ? ' disabled' : ''; ?>>
        <span id="glsri-pagination-inputhint" class="screen-reader-text" aria-hidden="true"><?= __('Press Return/Enter key to go to the page', 'site-reviews-images'); ?></span>
        <span class="screen-reader-text"><?= sprintf(__('Page %d', 'site-reviews-images'), $page); ?></span>
        <span><?= __('of', 'site-reviews-images'); ?></span>
        <span><?= $max_pages; ?></span>
    </div>
    <div class="wp-block-button">
        <button data-next type="button" class="glsr-button btn btn-primary button et_pb_button wp-block-button__link" aria-label="<?= __('Next Page', 'site-reviews-images'); ?>" data-page="<?= $page < $max_pages ? $page + 1 : $page; ?>"<?php if ($page === $max_pages) { echo ' disabled'; } ?>>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                <path fill="currentColor" d="M16.172 11l-5.364-5.364 1.414-1.414L20 12l-7.778 7.778-1.414-1.414L16.172 13H4v-2z"/>
            </svg>
        </button>
    </div>
</div>

<?php defined('WPINC') || die; ?>

<span class="glsr-fallback">
    <input type="file" multiple name="files" accept="<?= $types; ?>">
</span>
<input type="hidden" name="<?= $name; ?>" data-glsr-validate data-dz-images>
<span class="glsr-dz-message dz-message">
    <button type="button" class="glsr-dz-button dz-button">
        <?= __('Drag & Drop your photos or <span>Browse</span>', 'site-reviews-images'); ?>
    </button>
</span>

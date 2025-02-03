<?php defined('WPINC') || die; ?>

<div id="glsr-dz-caption-tmpl" style="display:none!important;">
    <div class="glsr-dz-modal-caption">
        <textarea rows="4" placeholder="<?= __('Enter a caption', 'site-reviews-images'); ?>"></textarea>
        <div class="wp-block-buttons">
            <div class="wp-block-button">
                <button type="button" class="btn btn-primary button wp-block-button__link glsr-dropzone-modal-save"><?= __('Save', 'site-reviews-images'); ?></button>
            </div>
            <div class="wp-block-button">
                <button type="button" class="btn btn-primary button wp-block-button__link glsr-dropzone-modal-close" data-glsr-close aria-label="<?= __('Cancel', 'site-reviews-images'); ?>"><?= __('Cancel', 'site-reviews-images'); ?></button>
            </div>
        </div>
    </div>
</div>
<div id="glsr-dz-preview-tmpl" style="display:none!important;">
    <span class="glsr-dz-preview">
        <span class="glsr-dz-progress"><span data-dz-uploadprogress></span></span>
        <span class="glsr-dz-overlay">
            <svg viewBox="0 0 500 250" preserveAspectRatio="none">
                <rect style="height:100%;width:100%;" x="0" width="500" height="250" fill="currentColor" mask="url(#glsr-svg-overlay-mask)"></rect>
            </svg>
        </span>
        <span class="glsr-dz-details">
            <span class="glsr-dz-buttons">
                <a class="glsr-dz-button glsr-dz-remove" tabindex="0" href="javascript:undefined;" title="<?= esc_attr__('Remove image', 'site-reviews-images'); ?>" data-dz-remove>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </a>
                <a class="glsr-dz-button glsr-dz-edit" tabindex="0" href="javascript:undefined;" title="<?= __('Edit caption', 'site-reviews-images'); ?>" data-dz-edit>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 5C12 5 12.0610767 2 7 2M11.9999661 5C11.9999661 5 11.9389233 2 17 2M7 22C12.1495319 22 11.9995455 19 11.9995455 19M17 22C11.8504681 22 12.0004545 19 12.0004545 19"/>
                        <line x1="15" x2="9" y1="12" y2="12"/>
                        <line x1="12" x2="12" y1="4.881" y2="19.167"/>
                    </svg>
                </a>
            </span>
            <span class="glsr-dz-filename"><span data-dz-name></span></span>
            <span class="glsr-dz-caption"><span data-dz-caption></span></span>
            <span class="glsr-dz-error-message"><span data-dz-errormessage></span></span>
        </span>
        <span class="glsr-dz-image">
            <img data-dz-thumbnail data-no-lazy="" /> <!-- [data-no-lazy] prevents the Flying Images plugin from lazy-loading the image -->
        </span>
    </span>
</div>

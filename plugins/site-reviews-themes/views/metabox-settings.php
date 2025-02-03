<?php defined('WPINC') || die; ?>

<div id="glsrt-metabox-settings" class="submitbox">
    <input type="hidden" name="theme_settings" data-settings>
    <div class="edit-post-sidebar">
        <div class="loading-content">
            <div class="spinner"></div>
            <?= _x('Loading settings...', 'admin-text', 'site-reviews-themes'); ?>
        </div>
    </div>
    <div id="glsrt-settings"></div>

    <div id="submitpost">
        <div id="major-publishing-actions">
            <div id="delete-action">
                <?php if (current_user_can('delete_post', $postId)) : ?>
                    <a class="submitdelete deletion" href="<?= get_delete_post_link($postId); ?>"><?= $deleteText; ?></a>
                <?php endif; ?>
            </div>
            <div id="publishing-action">
                <span class="spinner"></span>
                <?php if (!in_array($post->post_status, ['publish', 'future', 'private'], true) || 0 === $postId) : ?>
                    <?php if ($canPublish) : ?>
                        <?php if (!empty($post->post_date_gmt) && time() < strtotime($post->post_date_gmt.' +0000')) : ?>
                            <input name="original_publish" type="hidden" id="original_publish" value="<?= esc_attr__('Schedule'); ?>" />
                            <?php submit_button(_x('Schedule', 'post action/button label'), 'primary large', 'publish', false); ?>
                        <?php else : ?>
                            <input name="original_publish" type="hidden" id="original_publish" value="<?= esc_attr__('Publish'); ?>" />
                            <?php submit_button(__('Publish'), 'primary large', 'publish', false); ?>
                        <?php endif; ?>
                    <?php else : ?>
                        <input name="original_publish" type="hidden" id="original_publish" value="<?= esc_attr__('Submit for Review'); ?>" />
                        <?php submit_button(__('Submit for Review'), 'primary large', 'publish', false); ?>
                    <?php endif; ?>
                <?php else : ?>
                    <input name="original_publish" type="hidden" id="original_publish" value="<?= esc_attr__('Update'); ?>" />
                    <?php submit_button(__('Update'), 'primary large', 'save', false, ['id' => 'publish']); ?>
                <?php endif; ?>
            </div>
            <div class="clear"></div>
        </div>
    </div>
</div>

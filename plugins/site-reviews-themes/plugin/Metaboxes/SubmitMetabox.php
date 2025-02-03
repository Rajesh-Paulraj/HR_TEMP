<?php

namespace GeminiLabs\SiteReviews\Addon\Themes\Metaboxes;

use GeminiLabs\SiteReviews\Addon\Themes\Application;
use GeminiLabs\SiteReviews\Contracts\MetaboxContract;

class SubmitMetabox implements MetaboxContract
{
    /**
     * {@inheritdoc}
     */
    public function register($post)
    {
        remove_meta_box('submitdiv', Application::POST_TYPE, 'side');
        $title = _x('Theme Settings', 'admin-text', 'site-reviews-themes');
        add_meta_box('submitdiv', $title, [$this, 'render'], Application::POST_TYPE, 'side', 'high');
    }

    /**
     * {@inheritdoc}
     */
    public function render($post)
    {
        $deleteText = __('Delete permanently');
        if (EMPTY_TRASH_DAYS) {
            $deleteText = __('Move to Trash');
        }
        $postTypeObj = get_post_type_object($post->post_type);
        glsr()->render(Application::ID.'/views/metabox-settings', [
            'canPublish' => current_user_can($postTypeObj->cap->publish_posts),
            'deleteText' => $deleteText,
            'post' => $post,
            'postId' => (int) $post->ID,
        ]);
    }

    /**
     * @param int $postId
     * @return void
     */
    public function save($postId)
    {
    }
}

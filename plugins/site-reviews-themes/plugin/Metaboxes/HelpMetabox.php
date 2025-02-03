<?php

namespace GeminiLabs\SiteReviews\Addon\Themes\Metaboxes;

use GeminiLabs\SiteReviews\Addon\Themes\Application;
use GeminiLabs\SiteReviews\Addon\Themes\Columns\ShortcodeColumn;
use GeminiLabs\SiteReviews\Contracts\MetaboxContract;

class HelpMetabox implements MetaboxContract
{
    /**
     * {@inheritdoc}
     */
    public function register($post)
    {
        $title = _x('How To Use', 'admin-text', 'site-reviews-themes');
        add_meta_box('helpdiv', $title, [$this, 'render'], Application::POST_TYPE, 'side');
    }

    /**
     * {@inheritdoc}
     */
    public function render($post)
    {
        glsr()->render(Application::ID.'/views/metabox-help', [
            'site_reviews' => glsr(ShortcodeColumn::class, ['postId' => $post->ID])->build(),
            'site_reviews_form' => glsr(ShortcodeColumn::class, ['postId' => $post->ID])->buildForm(),
            'site_reviews_summary' => glsr(ShortcodeColumn::class, ['postId' => $post->ID])->buildSummary(),
        ]);
    }
}

<?php

namespace GeminiLabs\SiteReviews\Addon\Forms\Metaboxes;

use GeminiLabs\SiteReviews\Addon\Forms\Application;
use GeminiLabs\SiteReviews\Addon\Forms\Columns\ShortcodeColumn;
use GeminiLabs\SiteReviews\Contracts\MetaboxContract;

class HelpMetabox implements MetaboxContract
{
    /**
     * {@inheritdoc}
     */
    public function register($post)
    {
        $id = Application::POST_TYPE.'-helpdiv';
        $title = _x('How to Use', 'admin-text', 'site-reviews');
        add_meta_box($id, $title, [$this, 'render'], Application::POST_TYPE, 'normal');
    }

    /**
     * {@inheritdoc}
     */
    public function render($post)
    {
        $column = glsr(ShortcodeColumn::class, ['postId' => $post->ID]);
        glsr(Application::class)->render('views/metabox-help', [
            'site_reviews' => $column->build('site_reviews'),
            'site_reviews_form' => $column->build('site_reviews_form'),
        ]);
    }
}

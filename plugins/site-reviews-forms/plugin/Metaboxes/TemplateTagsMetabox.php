<?php

namespace GeminiLabs\SiteReviews\Addon\Forms\Metaboxes;

use GeminiLabs\SiteReviews\Addon\Forms\Application;
use GeminiLabs\SiteReviews\Addon\Forms\ReviewTemplate;
use GeminiLabs\SiteReviews\Contracts\MetaboxContract;

class TemplateTagsMetabox implements MetaboxContract
{
    /**
     * {@inheritdoc}
     */
    public function register($post)
    {
        $id = Application::POST_TYPE.'-templatetagsdiv';
        $title = _x('Reserved Tags', 'admin-text', 'site-reviews');
        add_meta_box($id, $title, [$this, 'render'], Application::POST_TYPE, 'side', 'low');
    }

    /**
     * {@inheritdoc}
     */
    public function render($post)
    {
        glsr()->render(Application::ID.'/views/metabox-template-tags', [
            'tags' => glsr(ReviewTemplate::class)->reservedTags(),
        ]);
    }
}

<?php

namespace GeminiLabs\SiteReviews\Addon\Forms\Metaboxes;

use GeminiLabs\SiteReviews\Addon\Forms\Application;
use GeminiLabs\SiteReviews\Addon\Forms\ReviewTemplate;
use GeminiLabs\SiteReviews\Contracts\MetaboxContract;

class TemplateMetabox implements MetaboxContract
{
    /**
     * {@inheritdoc}
     */
    public function register($post)
    {
        $id = Application::POST_TYPE.'-templatediv';
        $title = _x('Review Template', 'admin-text', 'site-reviews');
        add_meta_box($id, $title, [$this, 'render'], Application::POST_TYPE, 'normal', 'high');
    }

    /**
     * {@inheritdoc}
     */
    public function render($post)
    {
        $template = glsr(ReviewTemplate::class)->normalizedTemplate(get_the_ID());
        glsr()->render(Application::ID.'/views/metabox-template', [
            'template' => $template,
        ]);
    }

    /**
     * @param int $postId
     * @return void
     */
    public function save($postId)
    {
        $template = filter_input(INPUT_POST, 'review_template');
        glsr(ReviewTemplate::class)->save($postId, $template);
    }
}

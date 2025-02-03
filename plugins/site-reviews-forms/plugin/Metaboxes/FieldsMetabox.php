<?php

namespace GeminiLabs\SiteReviews\Addon\Forms\Metaboxes;

use GeminiLabs\SiteReviews\Addon\Forms\Application;
use GeminiLabs\SiteReviews\Addon\Forms\FormFields;
use GeminiLabs\SiteReviews\Contracts\MetaboxContract;
use GeminiLabs\SiteReviews\Helpers\Cast;

class FieldsMetabox implements MetaboxContract
{
    /**
     * {@inheritdoc}
     */
    public function register($post)
    {
        $id = Application::POST_TYPE.'-fieldsdiv';
        $title = _x('Form Fields', 'admin-text', 'site-reviews');
        add_meta_box($id, $title, [$this, 'render'], Application::POST_TYPE, 'normal', 'high');
    }

    /**
     * {@inheritdoc}
     */
    public function render($post)
    {
        $fields = glsr(FormFields::class)->normalizedFieldsForMetaboxIndexed(get_the_ID());
        if (empty($fields)) {
            $fields = glsr(FormFields::class)->defaultFieldsForMetaboxIndexed();
        }
        glsr()->render(Application::ID.'/views/metabox-fields', [
            'fields' => json_encode($fields, JSON_HEX_APOS),
        ]);
    }

    /**
     * @param int $postId
     * @return void
     */
    public function save($postId)
    {
        if ($fields = filter_input(INPUT_POST, 'fields')) {
            $fields = Cast::toArray(json_decode($fields));
            glsr(FormFields::class)->saveFields($postId, $fields);
        }
    }
}

<?php

namespace GeminiLabs\SiteReviews\Addon\Forms\Fields;

class CustomUrl extends Field
{
    public $options = [
        'format', 'label', 'name', 'placeholder', 'required', 'tag', 'tag_label', 'type', 'value'
    ];

    /**
     * @return array
     */
    public function formats()
    {
        return [
            'link' => 'Link',
            'link_blank' => 'Link (open in new tab/window)',
            'plain' => 'Plain Text',
        ];
    }

    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'format' => 'link',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function handle()
    {
        return _x('Custom: URL', 'admin-text', 'site-reviews-forms');
    }

    /**
     * {@inheritdoc}
     */
    protected function type()
    {
        return 'url';
    }
}

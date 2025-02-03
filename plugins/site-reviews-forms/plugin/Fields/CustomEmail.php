<?php

namespace GeminiLabs\SiteReviews\Addon\Forms\Fields;

class CustomEmail extends Field
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
            'link' => 'Link (mailto:)',
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
        return _x('Custom: Email', 'admin-text', 'site-reviews-forms');
    }

    /**
     * {@inheritdoc}
     */
    protected function type()
    {
        return 'email';
    }
}

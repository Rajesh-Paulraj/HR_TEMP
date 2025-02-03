<?php

namespace GeminiLabs\SiteReviews\Addon\Forms\Fields;

class CustomNumber extends Field
{
    public $options = [
        'label', 'name', 'placeholder', 'required', 'tag', 'tag_label', 'type', 'value'
    ];

    /**
     * {@inheritdoc}
     */
    protected function handle()
    {
        return _x('Custom: Number', 'admin-text', 'site-reviews-forms');
    }

    /**
     * {@inheritdoc}
     */
    protected function type()
    {
        return 'number';
    }

    /**
     * {@inheritdoc}
     */
    protected function validation()
    {
        return [
            'value' => 'number',
        ];
    }
}

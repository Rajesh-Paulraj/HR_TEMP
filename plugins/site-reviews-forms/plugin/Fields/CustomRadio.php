<?php

namespace GeminiLabs\SiteReviews\Addon\Forms\Fields;

class CustomRadio extends Field
{
    public $options = [
        'label', 'name', 'options', 'required', 'tag', 'tag_label', 'type',
    ];

    /**
     * {@inheritdoc}
     */
    protected function handle()
    {
        return _x('Custom: Radio', 'admin-text', 'site-reviews-forms');
    }

    /**
     * {@inheritdoc}
     */
    protected function type()
    {
        return 'radio';
    }

    /**
     * {@inheritdoc}
     */
    protected function validation()
    {
        return [
            'options' => 'required',
        ];
    }
}

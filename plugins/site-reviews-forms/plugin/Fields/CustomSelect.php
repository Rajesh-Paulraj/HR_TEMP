<?php

namespace GeminiLabs\SiteReviews\Addon\Forms\Fields;

class CustomSelect extends Field
{
    public $options = [
        'label', 'name', 'options', 'placeholder', 'required', 'tag', 'tag_label', 'type', 'value'
    ];

    /**
     * {@inheritdoc}
     */
    protected function handle()
    {
        return _x('Custom: Select', 'admin-text', 'site-reviews-forms');
    }

    /**
     * {@inheritdoc}
     */
    protected function type()
    {
        return 'select';
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

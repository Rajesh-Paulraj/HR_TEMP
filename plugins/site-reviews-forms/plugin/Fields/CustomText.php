<?php

namespace GeminiLabs\SiteReviews\Addon\Forms\Fields;

class CustomText extends Field
{
    public $options = [
        'label', 'name', 'placeholder', 'required', 'tag', 'tag_label', 'type', 'value'
    ];

    /**
     * {@inheritdoc}
     */
    protected function handle()
    {
        return _x('Custom: Text', 'admin-text', 'site-reviews-forms');
    }

    /**
     * {@inheritdoc}
     */
    protected function type()
    {
        return 'text';
    }
}

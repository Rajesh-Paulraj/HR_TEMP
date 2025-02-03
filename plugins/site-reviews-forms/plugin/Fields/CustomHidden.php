<?php

namespace GeminiLabs\SiteReviews\Addon\Forms\Fields;

class CustomHidden extends Field
{
    public $options = [
        'label', 'name', 'type', 'value'
    ];

    /**
     * {@inheritdoc}
     */
    protected function handle()
    {
        return _x('Custom: Hidden', 'admin-text', 'site-reviews-forms');
    }

    /**
     * {@inheritdoc}
     */
    protected function type()
    {
        return 'hidden';
    }
}

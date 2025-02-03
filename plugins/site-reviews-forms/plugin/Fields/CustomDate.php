<?php

namespace GeminiLabs\SiteReviews\Addon\Forms\Fields;

class CustomDate extends Field
{
    public $options = [
        'format', 'label', 'name', 'required', 'tag', 'tag_label', 'type', 'value'
    ];

    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'format' => 'F j, Y',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function handle()
    {
        return _x('Custom: Date', 'admin-text', 'site-reviews-forms');
    }

    /**
     * {@inheritdoc}
     */
    protected function type()
    {
        return 'date';
    }
}

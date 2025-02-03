<?php

namespace GeminiLabs\SiteReviews\Addon\Forms\Fields;

class CustomCheckbox extends Field
{
    public $options = [
        'format', 'label', 'name', 'options', 'required', 'tag', 'tag_label', 'type',
    ];

    /**
     * @return array
     */
    public function formats()
    {
        return [
            'ul' => 'Bulleted List',
            'comma' => 'Comma Separated Values',
            'ol' => 'Numbered List',
        ];
    }

    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'format' => 'comma',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function handle()
    {
        return _x('Custom: Checkbox', 'admin-text', 'site-reviews-forms');
    }

    /**
     * {@inheritdoc}
     */
    protected function type()
    {
        return 'checkbox';
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

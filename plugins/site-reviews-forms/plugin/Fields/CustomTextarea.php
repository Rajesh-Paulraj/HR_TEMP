<?php

namespace GeminiLabs\SiteReviews\Addon\Forms\Fields;

class CustomTextarea extends Field
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
            'ul' => 'Bulleted List (item per paragraph)',
            'excerpt' => 'Excerpt (expandable paragraph)',
            'paragraph' => 'Multiple Paragraphs',
            'ol' => 'Numbered List (item per paragraph)',
        ];
    }

    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'format' => 'excerpt',
        ];
    }
    /**
     * {@inheritdoc}
     */
    protected function handle()
    {
        return _x('Custom: Textarea', 'admin-text', 'site-reviews-forms');
    }

    /**
     * {@inheritdoc}
     */
    protected function type()
    {
        return 'textarea';
    }
}

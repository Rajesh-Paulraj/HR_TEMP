<?php

namespace GeminiLabs\SiteReviews\Addon\Forms\Fields;

class ReviewContent extends Field
{
    public $name = 'content';
    public $options = ['label', 'placeholder', 'required', 'tag_label', 'type'];
    public $tag = 'content';

    /**
     * {@inheritdoc}
     */
    protected function handle()
    {
        return _x('Review: Content', 'admin-text', 'site-reviews-forms');
    }

    /**
     * {@inheritdoc}
     */
    protected function type()
    {
        return 'review_content';
    }

    /**
     * {@inheritdoc}
     */
    protected function validation()
    {
        return [
            'name' => 'required|slug|unique',
            'type' => 'unique',
        ];
    }
}

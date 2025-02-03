<?php

namespace GeminiLabs\SiteReviews\Addon\Forms\Fields;

class ReviewEmail extends Field
{
    public $name = 'email';
    public $options = ['label', 'placeholder', 'required', 'tag_label', 'type'];
    public $tag = 'email';

    /**
     * {@inheritdoc}
     */
    protected function handle()
    {
        return _x('Review: Email', 'admin-text', 'site-reviews-forms');
    }

    /**
     * {@inheritdoc}
     */
    protected function type()
    {
        return 'review_email';
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

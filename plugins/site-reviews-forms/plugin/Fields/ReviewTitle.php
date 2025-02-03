<?php

namespace GeminiLabs\SiteReviews\Addon\Forms\Fields;

class ReviewTitle extends Field
{
    public $name = 'title';
    public $options = ['label', 'placeholder', 'required', 'tag_label', 'type'];
    public $tag = 'title';

    /**
     * {@inheritdoc}
     */
    protected function handle()
    {
        return _x('Review: Title', 'admin-text', 'site-reviews-forms');
    }

    /**
     * {@inheritdoc}
     */
    protected function type()
    {
        return 'review_title';
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

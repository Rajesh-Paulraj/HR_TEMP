<?php

namespace GeminiLabs\SiteReviews\Addon\Forms\Fields;

class ReviewName extends Field
{
    public $name = 'name';
    public $options = ['label', 'placeholder', 'required', 'tag_label', 'type'];
    public $tag = 'author';

    /**
     * {@inheritdoc}
     */
    protected function handle()
    {
        return _x('Review: Name', 'admin-text', 'site-reviews-forms');
    }

    /**
     * {@inheritdoc}
     */
    protected function type()
    {
        return 'review_name';
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

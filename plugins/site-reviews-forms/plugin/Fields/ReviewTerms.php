<?php

namespace GeminiLabs\SiteReviews\Addon\Forms\Fields;

class ReviewTerms extends Field
{
    public $name = 'terms';
    public $options = ['label', 'required', 'type'];
    public $tag = '';

    /**
     * {@inheritdoc}
     */
    protected function handle()
    {
        return _x('Review: Terms', 'admin-text', 'site-reviews-forms');
    }

    /**
     * {@inheritdoc}
     */
    protected function type()
    {
        return 'review_terms';
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

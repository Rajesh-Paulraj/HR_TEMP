<?php

namespace GeminiLabs\SiteReviews\Addon\Forms\Fields;

class ReviewAssignedTerms extends Field
{
    public $name = 'assigned_terms';
    public $options = ['hidden', 'hidden:terms', 'hidden:type', 'label', 'placeholder', 'required', 'tag_label', 'terms', 'type'];
    public $tag = 'assigned_terms';

    /**
     * {@inheritdoc}
     */
    protected function handle()
    {
        return _x('Review: Categories', 'admin-text', 'site-reviews-forms');
    }

    /**
     * {@inheritdoc}
     */
    protected function type()
    {
        return 'review_assigned_terms';
    }

    /**
     * {@inheritdoc}
     */
    protected function validation()
    {
        return [
            'name' => 'required|slug',
        ];
    }
}

<?php

namespace GeminiLabs\SiteReviews\Addon\Forms\Fields;

class ReviewAssignedPosts extends Field
{
    public $name = 'assigned_posts';
    public $options = ['hidden', 'hidden:type', 'hidden:value', 'label', 'placeholder', 'posttypes', 'required', 'tag_label', 'type'];
    public $tag = 'assigned_links';

    /**
     * {@inheritdoc}
     */
    protected function handle()
    {
        return _x('Review: Assigned Posts', 'admin-text', 'site-reviews-forms');
    }

    /**
     * {@inheritdoc}
     */
    protected function type()
    {
        return 'review_assigned_posts';
    }

    /**
     * {@inheritdoc}
     */
    protected function validation()
    {
        return [
            'name' => 'required|slug',
            'posttypes' => 'required',
        ];
    }
}

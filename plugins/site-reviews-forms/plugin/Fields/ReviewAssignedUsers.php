<?php

namespace GeminiLabs\SiteReviews\Addon\Forms\Fields;

class ReviewAssignedUsers extends Field
{
    public $name = 'assigned_users';
    public $options = ['hidden', 'hidden:type', 'hidden:users', 'label', 'placeholder', 'required', 'tag_label', 'roles', 'type'];
    public $tag = 'assigned_users';

    /**
     * {@inheritdoc}
     */
    protected function handle()
    {
        return _x('Review: Assigned Users', 'admin-text', 'site-reviews-forms');
    }

    /**
     * {@inheritdoc}
     */
    protected function type()
    {
        return 'review_assigned_users';
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

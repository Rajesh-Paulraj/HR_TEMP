<?php

namespace GeminiLabs\SiteReviews\Addon\Themes\Mock\Tags;

class AssignedUsersTag extends Tag
{
    /**
     * {@inheritdoc}
     */
    protected function value($value = null)
    {
        return _x('Peter and Jane', 'admin-text', 'site-reviews-themes');
    }
}

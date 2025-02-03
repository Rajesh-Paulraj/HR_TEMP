<?php

namespace GeminiLabs\SiteReviews\Addon\Themes\Mock\Tags;

class AssignedPostsTag extends Tag
{
    /**
     * {@inheritdoc}
     */
    protected function value($value = null)
    {
        return _x('This Product', 'admin-text', 'site-reviews-themes');
    }
}

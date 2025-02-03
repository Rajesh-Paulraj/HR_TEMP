<?php

namespace GeminiLabs\SiteReviews\Addon\Themes\Mock\Tags;

class AssignedTermsTag extends Tag
{
    /**
     * {@inheritdoc}
     */
    protected function value($value = null)
    {
        return _x('People and Places', 'admin-text', 'site-reviews-themes');
    }
}

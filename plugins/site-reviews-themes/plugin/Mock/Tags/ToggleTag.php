<?php

namespace GeminiLabs\SiteReviews\Addon\Themes\Mock\Tags;

class ToggleTag extends Tag
{
    /**
     * @param mixed $value
     * @return string
     */
    protected function value($value = null)
    {
        return _x('Yes', 'admin-text', 'site-reviews-themes');
    }
}

<?php

namespace GeminiLabs\SiteReviews\Addon\Themes\Mock\Tags;

class OptionTag extends Tag
{
    /**
     * @param mixed $value
     * @return string
     */
    protected function value($value = null)
    {
        return _x('Selected Value', 'admin-text', 'site-reviews-themes');
    }
}

<?php

namespace GeminiLabs\SiteReviews\Addon\Themes\Mock\Tags;

class OptionsTag extends Tag
{
    /**
     * @param mixed $value
     * @return string
     */
    protected function value($value = null)
    {
        return 'Red, Yellow, and Green';
    }
}

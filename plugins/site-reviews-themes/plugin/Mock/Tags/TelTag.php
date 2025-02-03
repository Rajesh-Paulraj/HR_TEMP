<?php

namespace GeminiLabs\SiteReviews\Addon\Themes\Mock\Tags;

class TelTag extends Tag
{
    /**
     * @param mixed $value
     * @return string
     */
    protected function value($value = null)
    {
        return '<a href="javascript:void(0)">+1 (234) 567-8900</a>';
    }
}

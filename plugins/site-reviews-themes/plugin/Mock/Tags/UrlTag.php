<?php

namespace GeminiLabs\SiteReviews\Addon\Themes\Mock\Tags;

class UrlTag extends Tag
{
    /**
     * @param mixed $value
     * @return string
     */
    protected function value($value = null)
    {
        return '<a href="javascript:void(0)">https://website.com</a>';
    }
}

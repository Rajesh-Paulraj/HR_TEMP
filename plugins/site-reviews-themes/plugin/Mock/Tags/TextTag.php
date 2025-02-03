<?php

namespace GeminiLabs\SiteReviews\Addon\Themes\Mock\Tags;

class TextTag extends Tag
{
    /**
     * @param mixed $value
     * @return string
     */
    protected function value($value = null)
    {
        return sprintf('{{ %s }}', $this->tag);
    }
}

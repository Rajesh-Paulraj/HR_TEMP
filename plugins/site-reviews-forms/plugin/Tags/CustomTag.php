<?php

namespace GeminiLabs\SiteReviews\Addon\Forms\Tags;

use GeminiLabs\SiteReviews\Modules\Html\Tags\Tag;

class CustomTag extends Tag
{
    /**
     * {@inheritdoc}
     */
    protected function handle($value = null)
    {
        return $this->wrap($value, 'span');
    }
}

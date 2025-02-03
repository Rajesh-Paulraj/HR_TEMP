<?php

namespace GeminiLabs\SiteReviews\Addon\Forms\Tags;

use GeminiLabs\SiteReviews\Modules\Html\Tags\Tag;

class CustomTelTag extends Tag
{
    /**
     * {@inheritdoc}
     */
    protected function handle($value = null)
    {
        $value = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
        if ('link' === $this->with->get('format', 'link')) {
            $value = sprintf('<a href="tel:%1$s">%1$s</a>', $value);
        }
        return $this->wrap($value);
    }
}

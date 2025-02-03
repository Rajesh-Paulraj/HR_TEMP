<?php

namespace GeminiLabs\SiteReviews\Addon\Forms\Tags;

use GeminiLabs\SiteReviews\Modules\Html\Tags\Tag;

class CustomEmailTag extends Tag
{
    /**
     * {@inheritdoc}
     */
    protected function handle($value = null)
    {
        if ('link' === $this->with->get('format', 'link')) {
            $value = sprintf('<a href="mailto:%1$s">%1$s</a>', esc_url($value));
        }
        return $this->wrap($value);
    }
}

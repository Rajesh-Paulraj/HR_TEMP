<?php

namespace GeminiLabs\SiteReviews\Addon\Forms\Tags;

use GeminiLabs\SiteReviews\Modules\Html\Tags\Tag;

class CustomUrlTag extends Tag
{
    /**
     * {@inheritdoc}
     */
    protected function handle($value = null)
    {
        $format = $this->with->get('format', 'link');
        if ('link' === $format) {
            $value = sprintf('<a href="%1$s">%1$s</a>', esc_url($value));
        } elseif ('link_blank' === $format) {
            $value = sprintf('<a href="%1$s" target="_blank">%1$s</a>', esc_url($value));
        }
        return $this->wrap($value);
    }
}

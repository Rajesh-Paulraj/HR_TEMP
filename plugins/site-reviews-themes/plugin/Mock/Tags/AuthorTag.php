<?php

namespace GeminiLabs\SiteReviews\Addon\Themes\Mock\Tags;

use GeminiLabs\SiteReviews\Helpers\Text;

class AuthorTag extends Tag
{
    /**
     * {@inheritdoc}
     */
    protected function handle($value = null)
    {
        $format = glsr_get_option('reviews.name.format');
        $initial = glsr_get_option('reviews.name.initial');
        return Text::name($value, $format, $initial);
    }

    /**
     * {@inheritdoc}
     */
    protected function value($value = null)
    {
        return 'Jane Doe';
    }
}

<?php

namespace GeminiLabs\SiteReviews\Addon\Themes\Mock\Tags;

use GeminiLabs\SiteReviews\Modules\Html\Builder;

class TitleTag extends Tag
{
    /**
     * {@inheritdoc}
     */
    protected function handle($value = null)
    {
        return glsr(Builder::class)->h3($value);
    }

    /**
     * {@inheritdoc}
     */
    protected function value($value = null)
    {
        return _x('Review Title', 'admin-text', 'site-reviews-themes');
    }
}

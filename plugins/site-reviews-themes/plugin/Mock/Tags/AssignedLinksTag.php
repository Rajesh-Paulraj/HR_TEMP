<?php

namespace GeminiLabs\SiteReviews\Addon\Themes\Mock\Tags;

class AssignedLinksTag extends AssignedPostsTag
{
    /**
     * {@inheritdoc}
     */
    protected function handle($value = null)
    {
        $link = sprintf('<a href="javascript:void(0)">%s</a>', $value);
        return sprintf(_x('Review of %s', 'admin-text', 'site-reviews-themes'), $link);
    }
}

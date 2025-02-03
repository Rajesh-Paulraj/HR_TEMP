<?php

namespace GeminiLabs\SiteReviews\Addon\Themes\Mock\Tags;

use GeminiLabs\SiteReviews\Addon\Themes\Application;

class AvatarTag extends Tag
{
    /**
     * {@inheritdoc}
     */
    protected function value($value = null)
    {
        $image = file_get_contents(
            glsr(Application::class)->path('assets/images/icons/avatar.svg')
        );
        return trim($image);
    }
}

<?php

namespace GeminiLabs\SiteReviews\Addon\Filters;

use GeminiLabs\SiteReviews\Modules\Html\Template as DefaultTemplate;

class Template extends DefaultTemplate
{
    /**
     * {@inheritdoc}
     */
    public function app()
    {
        return glsr(Application::class);
    }
}

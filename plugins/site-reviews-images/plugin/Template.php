<?php

namespace GeminiLabs\SiteReviews\Addon\Images;

use GeminiLabs\SiteReviews\Modules\Html\Template as DefaultTemplate;

class Template extends DefaultTemplate
{
    /**
     * @param string $html
     * @return string
     */
    public function minify($html)
    {
        $html = preg_replace('/\v+/u', '', $html);
        $html = preg_replace('/>\s+</u', '><', $html);
        return $html;
    }
    /**
     * {@inheritdoc}
     */
    public function app()
    {
        return glsr(Application::class);
    }
}

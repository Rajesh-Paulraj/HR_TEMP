<?php

namespace GeminiLabs\SiteReviews\Addon\Forms\Defaults;

use GeminiLabs\SiteReviews\Addon\Forms\Application;
use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class FieldTypeSanitizerDefaults extends Defaults
{
    /**
     * @return \GeminiLabs\SiteReviews\Application|\GeminiLabs\SiteReviews\Addons\Addon
     */
    protected function app()
    {
        return glsr(Application::class);
    }

    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'checkbox' => 'text',
            'date' => 'date',
            'email' => 'email',
            'hidden' => 'text',
            'number' => 'numeric',
            'radio' => 'text',
            'rating' => 'int',
            'select' => 'text',
            'tel' => 'text',
            'text' => 'text',
            'textarea' => 'text-multiline',
            'toggle' => 'text',
            'url' => 'url',
        ];
    }
}

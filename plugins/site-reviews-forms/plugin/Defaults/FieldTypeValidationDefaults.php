<?php

namespace GeminiLabs\SiteReviews\Addon\Forms\Defaults;

use GeminiLabs\SiteReviews\Addon\Forms\Application;
use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;
use GeminiLabs\SiteReviews\Modules\Rating;

class FieldTypeValidationDefaults extends Defaults
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
        $maxRating = max(1, (int) glsr()->constant('MAX_RATING', Rating::class));
        return [
            'email' => 'email',
            'number' => 'number',
            'rating' => 'between:0,'.$maxRating,
            'tel' => 'tel',
            'url' => 'url',
        ];
    }
}

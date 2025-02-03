<?php

namespace GeminiLabs\SiteReviews\Addon\Themes\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class DesignTypographyDefaults extends Defaults
{
    /**
     * @return array
     */
    public $casts = [
        'text_large' => 'string',
        'text_normal' => 'string',
        'text_small' => 'string',
    ];

    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'text_large' => '18|27|px|#000000',
            'text_normal' => '14|21|px|#000000',
            'text_small' => '12|18|px|#000000',
        ];
    }
}

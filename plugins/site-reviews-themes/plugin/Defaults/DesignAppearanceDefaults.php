<?php

namespace GeminiLabs\SiteReviews\Addon\Themes\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class DesignAppearanceDefaults extends Defaults
{
    /**
     * @return array
     */
    public $casts = [
        'background_color' => 'string',
        'border_color' => 'string',
        'border_radius' => 'string',
        'border_width' => 'string',
        'padding' => 'string',
        'shadow_1' => 'string',
        'shadow_2' => 'string',
    ];

    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'background_color' => '#ffffff',
            'border_color' => '',
            'border_radius' => '8|8|8|8|px|1',
            'border_width' => '0|0|0|0|px|1',
            'padding' => '16|16|16|16|px|1',
            'shadow_1' => '0|1|3|0|rgba(0,0,0,0.1)',
            'shadow_2' => '0|1|2|0|rgba(0,0,0,0.06)',
        ];
    }
}

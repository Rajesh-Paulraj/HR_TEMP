<?php

namespace GeminiLabs\SiteReviews\Addon\Themes\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class PresentationLayoutDefaults extends Defaults
{
    /**
     * @return array
     */
    public $casts = [
        'appearance' => 'string',
        'display_as' => 'string',
        'max_columns' => 'int',
        'max_slides' => 'int',
        'spacing' => 'int',
    ];

    /**
     * @return array
     */
    public $enum = [
        'appearance' => ['custom', 'dark', 'light', 'transparent'],
        'display_as' => ['carousel', 'grid', 'list'],
        'max_columns' => [0,2,3,4,5,6],
        'max_slides' => [1,2,3,4,5,6],
    ];

    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'appearance' => 'light',
            'display_as' => 'grid',
            'max_columns' => 0,
            'max_slides' => 0,
            'spacing' => 16,
        ];
    }
}

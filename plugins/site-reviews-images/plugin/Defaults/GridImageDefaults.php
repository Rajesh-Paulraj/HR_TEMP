<?php

namespace GeminiLabs\SiteReviews\Addon\Images\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class GridImageDefaults extends Defaults
{
    /**
     * @var array
     */
    public $casts = [
        'ID' => 'int',
        'index' => 'int',
        'review_id' => 'int',
    ];

    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'ID' => 0,
            'index' => 0,
            'review_id' => 0,
        ];
    }
}

<?php

namespace GeminiLabs\SiteReviews\Addon\Themes\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class DesignRatingDefaults extends Defaults
{
    /**
     * @return array
     */
    public $casts = [
        'rating_colors' => 'string',
        'rating_image' => 'string',
        'rating_size' => 'int',
    ];

    /**
     * @return array
     */
    public $enum = [
        'rating_image' => [
            'rating-circle',
            'rating-heart',
            'rating-heart-circle',
            'rating-star',
            'rating-star-circle',
            'rating-star-rounded',
            'rating-star-square',
            'rating-star-wordpress',
            'rating-emoji1',
            'rating-emoji2',
        ],
    ];

    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'rating_colors' => '',
            'rating_image' => 'rating-star',
            'rating_size' => 20,
        ];
    }
}

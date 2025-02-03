<?php

namespace GeminiLabs\SiteReviews\Addon\Themes\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class PresentationExcerptDefaults extends Defaults
{
    /**
     * @return array
     */
    public $casts = [
        'excerpt_action' => 'string',
        'excerpt_length' => 'string',
    ];

    /**
     * @return array
     */
    public $enum = [
        'excerpt_action' => ['disabled', 'expand', 'modal'],
    ];

    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'excerpt_action' => 'modal',
            'excerpt_length' => '120|chars',
        ];
    }
}

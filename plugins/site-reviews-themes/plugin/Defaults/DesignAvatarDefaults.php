<?php

namespace GeminiLabs\SiteReviews\Addon\Themes\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class DesignAvatarDefaults extends Defaults
{
    /**
     * @return array
     */
    public $casts = [
        'avatar_radius' => 'string',
        'avatar_size' => 'int',
    ];

    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'avatar_radius' => '40|40|40|40|px|1',
            'avatar_size' => 40,
        ];
    }
}

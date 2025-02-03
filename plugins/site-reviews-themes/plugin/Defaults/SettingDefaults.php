<?php

namespace GeminiLabs\SiteReviews\Addon\Themes\Defaults;

use GeminiLabs\SiteReviews\Defaults\FieldDefaults as Defaults;

class SettingDefaults extends Defaults
{
    /**
     * @return array
     */
    public $casts = [
        'class' => 'string',
        'label' => 'string',
        'name' => 'string',
        'options' => 'array',
        'required' => 'bool',
        'tag' => 'string',
        'type' => 'string',
        // 'value' => 'string', // disabled because checkbox field value can be an array
    ];

    /**
     * @var array
     */
    public $sanitize = [
        'name' => 'key',
        'tag' => 'key',
    ];

    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'class' => '',
            'label' => '',
            'name' => '',
            'options' => [],
            'type' => '',
            'value' => '',
        ];
    }

    /**
     * @return array
     */
    protected function normalize(array $values = [])
    {
        unset($values['expanded']);
        return $values;
    }
}

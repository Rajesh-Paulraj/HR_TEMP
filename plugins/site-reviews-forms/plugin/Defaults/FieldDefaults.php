<?php

namespace GeminiLabs\SiteReviews\Addon\Forms\Defaults;

use GeminiLabs\SiteReviews\Addon\Forms\Application;
use GeminiLabs\SiteReviews\Defaults\FieldDefaults as Defaults;

class FieldDefaults extends Defaults
{
    /**
     * @return array
     */
    public $casts = [
        'class' => 'string',
        'format' => 'string',
        'label' => 'string',
        'name' => 'string',
        'options' => 'array',
        'required' => 'bool',
        'tag' => 'string',
        'tag_label' => 'string',
        'type' => 'string',
        // 'value' => 'string', // disabled because checkbox field value can be an array
    ];

    /**
     * @var array
     */
    public $sanitize = [
        'label' => 'text-html',
        'name' => 'key',
        'tag' => 'key',
    ];

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
            'class' => '',
            'format' => '',
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

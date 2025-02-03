<?php

namespace GeminiLabs\SiteReviews\Addon\Notifications\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;
use GeminiLabs\SiteReviews\Modules\Sanitizer;

class SettingsDefaults extends Defaults
{
    /**
     * @var array
     */
    public $sanitize = [
        'background_color' => 'text',
        'body_background_color' => 'text',
        'body_link_color' => 'text',
        'body_text_color' => 'text',
        'brand_color' => 'text',
        'footer_text' => 'text-html',
        'from_email' => 'text',
        'from_name' => 'text',
        'header_image' => 'url',
        'notifications' => 'json',
        'reply_to_email' => 'text',
    ];

    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'background_color' => '',
            'body_background_color' => '',
            'body_link_color' => '',
            'body_text_color' => '',
            'brand_color' => '',
            'footer_text' => '',
            'from_email' => '',
            'from_name' => '',
            'header_image' => '',
            'notifications' => '',
            'reply_to_email' => '',
        ];
    }

    /**
     * Normalize provided values, this always runs first.
     * @return array
     */
    protected function normalize(array $values = [])
    {
        foreach (['from_email', 'reply_to_email'] as $key) {
            if ('{admin_email}' !== glsr_get($values, $key, '{admin_email}')) {
                $values[$key] = glsr(Sanitizer::class)->sanitizeEmail($values[$key]);
            }
        }
        return $values;
    }
}

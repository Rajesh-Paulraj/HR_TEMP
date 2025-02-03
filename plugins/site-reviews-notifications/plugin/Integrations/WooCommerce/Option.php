<?php

namespace GeminiLabs\SiteReviews\Addon\Notifications\Integrations\WooCommerce;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;

class Option
{
    public const OPTION_KEY = 'woocommerce_glsr_reminder_settings';

    /**
     * @param mixed $fallback
     * @return mixed
     */
    public static function get(string $path, $fallback = '', string $cast = 'string')
    {
        static $settings;
        if (empty($settings)) {
            $settings = Cast::toArray(get_option(static::OPTION_KEY));
        }
        return Cast::to($cast, Arr::get($settings, $path, $fallback));
    }
}

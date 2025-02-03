<?php

namespace GeminiLabs\SiteReviews\Addon\Themes\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;

class MockFieldDefaults extends Defaults
{
    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'custom' => false,
            'name' => '',
            'tag' => '',
            'tag_label' => '',
            'type' => '',
            'value' => '',
        ];
    }

    /**
     * Normalize provided values, this always runs first.
     * @return array
     */
    protected function normalize(array $values = [])
    {
        $values['custom'] = !Str::startsWith(Arr::get($values, 'type'), 'review_');
        return $values;
    }
}

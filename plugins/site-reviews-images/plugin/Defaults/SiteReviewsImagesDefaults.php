<?php

namespace GeminiLabs\SiteReviews\Addon\Images\Defaults;

use GeminiLabs\SiteReviews\Addon\Images\Application;
use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;
use GeminiLabs\SiteReviews\Helpers\Arr;

class SiteReviewsImagesDefaults extends Defaults
{
    /**
     * @var array
     */
    public $casts = [
        'debug' => 'bool',
        'display' => 'int',
        'hide' => 'array',
        'rating' => 'int',
    ];

    /**
     * @var string[]
     */
    public $guarded = [
        'title',
    ];

    /**
     * @var array
     */
    public $mapped = [
        'assigned_post' => 'assigned_posts',
        'assigned_term' => 'assigned_terms',
        'assigned_user' => 'assigned_users',
    ];

    /**
     * @var array
     */
    public $sanitize = [
        'id' => 'id',
    ];

    public function app()
    {
        return glsr(Application::class);
    }

    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'assigned_posts' => '',
            'assigned_terms' => '',
            'assigned_users' => '',
            'class' => '',
            'display' => 8,
            'debug' => false,
            'hide' => [],
            'id' => '',
            'rating' => 0,
            'rating_field' => 'rating', // used for custom rating fields
            'terms' => '',
            'title' => '',
            'type' => 'local',
        ];
    }

    /**
     * Normalize provided values, this always runs first.
     * @return array
     */
    protected function normalize(array $values = [])
    {
        foreach ($this->mapped as $old => $new) {
            if ('custom' === Arr::get($values, $old)) {
                $values[$old] = Arr::get($values, $new);
            }
        }
        return $values;
    }
}

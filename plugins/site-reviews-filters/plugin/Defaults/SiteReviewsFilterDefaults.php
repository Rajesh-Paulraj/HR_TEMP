<?php

namespace GeminiLabs\SiteReviews\Addon\Filters\Defaults;

use GeminiLabs\SiteReviews\Addon\Filters\Application;
use GeminiLabs\SiteReviews\Addon\Filters\Shortcodes\SiteReviewsFilterShortcode;
use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;
use GeminiLabs\SiteReviews\Helpers\Cast;

class SiteReviewsFilterDefaults extends Defaults
{
    /**
     * @var array
     */
    public $casts = [
        'hide' => 'array',
    ];

    /**
     * @var array
     */
    public $guarded = [
        'fallback',
        'title',
    ];

    /**
     * {@inheritdoc}
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
            'hide' => [],
            'id' => '',
            'reviews_id' => '',
            'title' => '',
        ];
    }

    /**
     * @return array
     */
    protected function normalize(array $args = [])
    {
        if (empty($args['filters'])) {
            return $args;
        }
        $filters = Cast::toArray($args['filters']);
        $hide = array_keys(glsr(SiteReviewsFilterShortcode::class)->getHideOptions());
        $args['hide'] = empty(array_intersect($filters, ['1','true']))
            ? array_values(array_diff($hide, $filters))
            : [];
        return $args;
    }
}

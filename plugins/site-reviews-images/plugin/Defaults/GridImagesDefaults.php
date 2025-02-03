<?php

namespace GeminiLabs\SiteReviews\Addon\Images\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;

class GridImagesDefaults extends Defaults
{
    /**
     * @var array
     */
    public $casts = [
        'offset' => 'int',
        'page' => 'int',
        'pagination' => 'string',
        'per_page' => 'int',
        'rating' => 'int',
        'rating_field' => 'string',
        'status' => 'string',
    ];

    /**
     * @var array
     */
    public $mapped = [
        'display' => 'per_page',
        'exclude' => 'post__not_in',
        'include' => 'post__in',
    ];

    /**
     * @var array
     */
    public $sanitize = [
        'assigned_posts' => 'post-ids',
        'assigned_terms' => 'term-ids',
        'assigned_users' => 'user-ids',
        'post__in' => 'array-int',
        'post__not_in' => 'array-int',
        'rating_field' => 'name',
        'type' => 'key',
        'user__in' => 'user-ids',
        'user__not_in' => 'user-ids',
    ];

    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'assigned_posts' => '',
            'assigned_posts_types' => [],
            'assigned_terms' => '',
            'assigned_users' => '',
            'offset' => 0,
            'page' => 1,
            'pagination' => false,
            'per_page' => 12,
            'post__in' => [],
            'post__not_in' => [],
            'rating' => '',
            'rating_field' => 'rating', // used for custom rating fields
            'status' => 'approved',
            'terms' => '',
            'type' => '',
            'user__in' => [],
            'user__not_in' => [],
        ];
    }

    /**
     * Map old or deprecated keys to new keys.
     * @return array
     */
    protected function mapKeys(array $args)
    {
        $values = parent::mapKeys($args);
        if (is_numeric(Arr::get($args, 'display'))) {
            $values['per_page'] = max(0, Cast::toInt($args['display'])); // allow zero results
        }
        return $values;
    }

    /**
     * {@inheritdoc}
     */
    protected function normalize(array $values = [])
    {
        $values['offset'] = max(0, Cast::toInt(Arr::get($values, 'offset')));
        $values['page'] = max(1, Cast::toInt(Arr::get($values, 'page')));
        if (empty($values['assigned_posts'])) {
            return $values;
        }
        $postIds = Cast::toArray($values['assigned_posts']);
        $values['assigned_posts_types'] = [];
        foreach ($postIds as $postType) {
            if (!is_numeric($postType) && post_type_exists($postType)) {
                $values['assigned_posts'] = []; // query only by assigned post types!
                $values['assigned_posts_types'][] = $postType;
            }
        }
        return $values;
    }
}

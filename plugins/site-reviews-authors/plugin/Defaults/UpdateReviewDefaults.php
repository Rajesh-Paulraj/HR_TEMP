<?php

namespace GeminiLabs\SiteReviews\Addon\Authors\Defaults;

use GeminiLabs\SiteReviews\Addon\Authors\Application;
use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract;

class UpdateReviewDefaults extends DefaultsAbstract
{
    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     * @var array
     */
    public $sanitize = [
        'assigned_posts' => 'post-ids',
        'assigned_terms' => 'term-ids',
        'assigned_users' => 'user-ids',
        'content' => 'text-multiline',
        'email' => 'user-email',
        'name' => 'user-name',
        'rating' => 'rating',
        'title' => 'text',
        'type' => 'slug',
        'url' => 'url',
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
            'assigned_posts' => '',
            'assigned_terms' => '',
            'assigned_users' => '',
            'content' => '',
            'email' => '',
            'name' => '',
            'rating' => '',
            'title' => '',
            'type' => '',
            'url' => '',
        ];
    }

    /**
     * Finalize provided values, this always runs last.
     * @return array
     */
    protected function finalize(array $values = [])
    {
        $types = glsr()->retrieveAs('array', 'review_types');
        if (!array_key_exists($values['type'], $types)) {
            $values['type'] = 'local';
        }
        return $values;
    }
}

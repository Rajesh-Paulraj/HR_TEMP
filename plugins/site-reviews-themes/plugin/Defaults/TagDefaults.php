<?php

namespace GeminiLabs\SiteReviews\Addon\Themes\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class TagDefaults extends Defaults
{
    /**
     * These tags are always available in a review template
     * @return array
     */
    protected function defaults()
    {
        return [
            'author' => 'review_author',
            'assigned_links' => 'review_assigned_links',
            'assigned_posts' => 'review_assigned_posts',
            'assigned_terms' => 'review_assigned_terms',
            'assigned_users' => 'review_assigned_users',
            'avatar' => 'review_avatar',
            'date' => 'review_date',
            'rating' => 'review_rating',
            'response' => 'review_response',
            'verified' => 'review_verified',
        ];
    }
}

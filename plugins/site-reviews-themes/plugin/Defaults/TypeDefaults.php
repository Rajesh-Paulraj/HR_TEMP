<?php

namespace GeminiLabs\SiteReviews\Addon\Themes\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class TypeDefaults extends Defaults
{
    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'checkbox' => 'options',
            'content' => 'textarea',
            'file' => 'files',
            'images' => 'images',
            'name' => 'author',
            'number' => 'text',
            'radio' => 'option',
            'rating' => 'rating',
            'response' => 'textarea',
            'review_assigned_links' => 'assigned_links',
            'review_assigned_posts' => 'assigned_posts',
            'review_assigned_terms' => 'assigned_terms',
            'review_assigned_users' => 'assigned_users',
            'review_author' => 'author',
            'review_avatar' => 'avatar',
            'review_content' => 'textarea',
            'review_date' => 'date',
            'review_dropzone' => 'images', // Is this needed?
            'review_images' => 'images',
            'review_name' => 'author',
            'review_rating' => 'rating',
            'review_response' => 'response',
            'review_title' => 'title',
            'review_verified' => 'verified',
            'select' => 'option',
            'tel' => 'tel',
            'text' => 'text',
            'textarea' => 'textarea',
            'title' => 'title',
            'toggle' => 'toggle',
            'url' => 'url',
        ];
    }
}

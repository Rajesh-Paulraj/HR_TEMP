<?php

namespace GeminiLabs\SiteReviews\Addon\Images\Controllers;

use GeminiLabs\SiteReviews\Controllers\Api\Version1\Response\Prepare;

class ApiController
{
    /**
     * @param mixed $value
     * @return array
     * @filter site-reviews/api/reviews/prepare/images
     */
    public function filterApiReviewsPrepareImages($value, Prepare $prepare)
    {
        return $prepare->review->images;
    }

    /**
     * @return array
     * @filter site-reviews/api/reviews/properties
     */
    public function filterApiReviewsProperties(array $properties)
    {
        $properties['images'] = [
            'context' => ['edit', 'view'],
            'description' => _x('The attachment IDs of images attached to the review.', 'admin-text', 'site-reviews-images'),
            'items' => [
                'type' => ['integer'],
            ],
            'type' => 'array',
        ];
        return $properties;
    }
}

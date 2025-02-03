<?php

namespace GeminiLabs\SiteReviews\Addon\Themes\Controllers;

class ApiController
{
    /**
     * @return array
     * @filter site-reviews/api/reviews/parameters
     */
    public function filterApiReviewsParameters(array $parameters)
    {
        $parameters['theme'] = [
            'description' => _x('Render the review with a specific custom review theme (ID); only works with the rendered parameter.', 'admin-text', 'site-reviews-themes'),
            'sanitize_callback' => 'absint',
            'type' => 'integer',
        ];
        return $parameters;
    }

    /**
     * @return array
     * @filter site-reviews/api/summary/parameters
     */
    public function filterApiSummaryParameters(array $parameters)
    {
        $parameters['theme'] = [
            'description' => _x('Render the summary with a specific custom review theme (ID); only works with the rendered parameter.', 'admin-text', 'site-reviews-themes'),
            'sanitize_callback' => 'absint',
            'type' => 'integer',
        ];
        return $parameters;
    }
}

<?php

namespace GeminiLabs\SiteReviews\Addon\Forms\Controllers;

class ApiController
{
    /**
     * @return array
     * @filter site-reviews/api/reviews/parameters
     */
    public function filterApiReviewsParameters(array $parameters)
    {
        $parameters['form'] = [
            'description' => _x('Render the summary with a specific custom form (ID) review template; only works with the rendered parameter.', 'admin-text', 'site-reviews'),
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
        $parameters['rating_field'] = [
            'description' => _x('Use rating values of a custom rating field; use the custom rating Field Name as the value. ', 'admin-text', 'site-reviews'),
            'type' => 'string',
        ];
        return $parameters;
    }
}

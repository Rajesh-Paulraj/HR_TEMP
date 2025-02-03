<?php

namespace GeminiLabs\SiteReviews\Addon\Filters\Defaults;

use GeminiLabs\SiteReviews\Addon\Filters\Application;
use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Url;

class FilteredDefaults extends Defaults
{
    /**
     * @var array
     */
    public $casts = [
        'filter_by_rating' => 'string',
        'filter_by_term' => 'int',
        'search_for' => 'string',
        'sort_by' => 'string',
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
            'filter_by_rating' => '',
            'filter_by_term' => '',
            'search_for' => '',
            'sort_by' => '',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function normalize(array $values = [])
    {
        $args = array_fill_keys(array_keys($this->defaults), FILTER_SANITIZE_FULL_SPECIAL_CHARS); // restrict input keys to defaults
        $parameters = Cast::toArray(filter_input_array(INPUT_GET, $args, false));
        if ($request = glsr()->retrieve(glsr()->paged_handle)) { // use the pagination request
            $urlParameters = filter_var_array(Url::queries($request->url), $args, false);
            $parameters = wp_parse_args($urlParameters, $parameters);
        }
        $values = wp_parse_args($parameters, $values);
        $values = array_map('strtolower', $values); // cast to lowercase!
        return $values;
    }
}

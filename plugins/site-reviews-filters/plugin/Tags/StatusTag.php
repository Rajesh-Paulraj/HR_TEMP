<?php

namespace GeminiLabs\SiteReviews\Addon\Filters\Tags;

use GeminiLabs\SiteReviews\Addon\Filters\Application;
use GeminiLabs\SiteReviews\Addon\Filters\Defaults\FilteredDefaults;
use GeminiLabs\SiteReviews\Addon\Filters\SqlModifier;
use GeminiLabs\SiteReviews\Database\NormalizePaginationArgs;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Helpers\Url;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Html\Template;

class StatusTag extends Tag
{
    /**
     * @return array
     */
    protected function getContext()
    {
        $filters = array_filter(glsr(FilteredDefaults::class)->merge());
        unset($filters['sort_by']); // technically this isn't a filter...
        if ($filters) {
            return [
                'clear_filters' => $this->clearFilterLink(),
                'filtered_by' => $this->filteredBy(),
                'label' => __('Filtered by', 'site-reviews-filters'),
            ];
        }
        return [];
    }

    /**
     * @return string
     */
    protected function clearFilterLink()
    {
        $paginationArgs = new NormalizePaginationArgs();
        $parameters = array_keys(glsr(FilteredDefaults::class)->defaults());
        return glsr(Builder::class)->a([
            'href' => esc_url(remove_query_arg($parameters, $paginationArgs->pageUrl)),
            'text' => __('Clear filters', 'site-reviews-filters'),
        ]);
    }

    /**
     * @return string
     */
    protected function filteredBy()
    {
        $filteredBy = [];
        $filters = array_filter(glsr(FilteredDefaults::class)->merge());
        foreach ($filters as $key => $value) {
            if ('filter_by_rating' === $key) {
                $filteredBy[] = $this->filteredByRating($value);
            }
            if ('filter_by_term' === $key && term_exists((int) $value, glsr()->taxonomy)) {
                $filteredBy[] = get_term($value, glsr()->taxonomy)->name;
            }
            if ('search_for' === $key) {
                $filteredBy[] = sprintf(__('Containing %s', 'site-reviews-filters'), '"'.$value.'"');
            }
        }
        $filteredBy = glsr(Application::class)->filterArrayUnique('status/filtered-by', $filteredBy, $filters);
        return implode('; ', $filteredBy);
    }

    /**
     * @return string
     */
    protected function filteredByRating($value)
    {
        $ratingText = [
            'critical' => __('Critical', 'site-reviews-filters'),
            'positive' => __('Positive', 'site-reviews-filters'),
        ];
        if (is_numeric($value)) {
            return sprintf(__('%s Star', 'site-reviews-filters'), $value);
        }
        return Arr::get($ratingText, $value);
    }
}

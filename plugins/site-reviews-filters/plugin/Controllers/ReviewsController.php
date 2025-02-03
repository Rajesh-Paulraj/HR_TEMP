<?php

namespace GeminiLabs\SiteReviews\Addon\Filters\Controllers;

use GeminiLabs\SiteReviews\Addon\Filters\Application;
use GeminiLabs\SiteReviews\Addon\Filters\Blocks\SiteReviewsFiltersBlock;
use GeminiLabs\SiteReviews\Addon\Filters\Defaults\FilteredDefaults;
use GeminiLabs\SiteReviews\Addon\Filters\Defaults\SiteReviewsFilterDefaults;
use GeminiLabs\SiteReviews\Addon\Filters\Shortcodes\SiteReviewsFilterShortcode;
use GeminiLabs\SiteReviews\Addons\Controller as AddonController;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Review;
use GeminiLabs\SiteReviews\Reviews;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsShortcode;

class ReviewsController extends AddonController
{
    /**
     * {@inheritdoc}
     */
    protected $addon;

    /**
     * @return array
     * @filter site-reviews/block/reviews/attributes
     */
    public function filterBlockAttributes(array $attributes)
    {
        $attributes['filters'] = [
            'default' => '',
            'type' => 'string',
        ];
        return $attributes;
    }

    /**
     * @param string $shortcode
     * @return array
     * @filter site-reviews/shortcode/display-options
     */
    public function filterDisplayOptions(array $options, $shortcode)
    {
        if ('site_reviews' !== $shortcode) {
            return $options;
        }
        return [
            'filter_by_term' => _x('Display the category filter', 'admin-text', 'site-reviews-filters'),
            'filter_by_rating' => _x('Display the rating filter', 'admin-text', 'site-reviews-filters'),
            'search_for' => _x('Display the search bar', 'admin-text', 'site-reviews-filters'),
            'sort_by' => _x('Display the sort by', 'admin-text', 'site-reviews-filters'),
        ];
    }

    /**
     * @return array
     * @filter site-reviews/documentation/shortcodes/site_reviews
     */
    public function filterDocumentation(array $paths)
    {
        $index = array_search('fallback.php', array_map('basename', $paths));
        return Arr::insertAfter($index, $paths, [$this->addon->path('views/site_reviews/filters.php')]);
    }

    /**
     * @param \Elementor\Widget_Base $widget
     * @return array
     * @filter site-reviews/integration/elementor/register/controls
     */
    public function filterElementorWidgetControls(array $controls, $widget)
    {
        if ('site_reviews' !== $widget->get_name()) {
            return $controls;
        }
        $display = glsr(SiteReviewsShortcode::class)->getDisplayOptions();
        $options = [];
        foreach ($display as $key => $label) {
            $options['filter-'.$key] = [
                'label' => $label,
                'return_value' => '1',
                'type' => \Elementor\Controls_Manager::SWITCHER,
            ];
        }
        return Arr::insertAfter('settings', $controls, [
            'filter_settings' => [
                'label' => _x('Filters', 'admin-text', 'site-reviews-filters'),
                'options' => $options,
            ],
        ]);
    }

    /**
     * @param \Elementor\Widget_Base $widget
     * @return array
     * @filter site-reviews/integration/elementor/display/settings
     */
    public function filterElementorWidgetDisplaySettings(array $settings, $widget)
    {
        if ('site_reviews' !== $widget->get_name()) {
            return $settings;
        }
        $filter = [];
        foreach ($settings as $key => $value) {
            if (Str::startsWith($key, 'filter-') && !empty($value)) {
                $filter[] = Str::removePrefix($key, 'filter-');
            }
        }
        $settings['filters'] = array_filter($filter);
        return $settings;
    }

    /**
     * @param string $type
     * @param string $shortcode
     * @return array
     * @filter site-reviews/shortcode/atts
     */
    public function filterShortcodeAttributes(array $attributes, $type, $shortcode)
    {
        if ('site_reviews' !== $shortcode) {
            return $attributes;
        }
        $parameters = glsr()->args(glsr(FilteredDefaults::class)->merge());
        if ($parameters->filter_by_term) {
            $values = Arr::convertFromString(Arr::get($attributes, 'assigned_terms'));
            $values[] = $parameters->filter_by_term;
            $values = Arr::unique($values);
            $attributes['assigned_terms'] = implode(',', $values);
        }
        return $attributes;
    }

    /**
     * @return array
     * @filter site-reviews/defaults/site-reviews/casts
     */
    public function filterShortcodeCasts(array $casts)
    {
        $casts['filters'] = 'array';
        return $casts;
    }

    /**
     * @return array
     * @filter site-reviews/defaults/site-reviews/defaults
     */
    public function filterShortcodeDefaults(array $defaults)
    {
        $defaults['filters'] = [];
        return $defaults;
    }

    /**
     * @param string $template
     * @return string
     * @filter site-reviews/rendered/template/reviews
     */
    public function filterTemplate($template, array $data)
    {
        if (empty($data['args']->filters)) {
            return $template; // filters are not enabled
        }
        $hideIfNoReviews = $this->addon->filterbool('hide-if-no-reviews', 0 === $data['reviews']->total); // @phpstan-ignore-line
        if (!$this->isUrlFiltered() && $hideIfNoReviews) {
            return $template; // hide the filters when there are no unfiltered reviews to display
        }
        $filters = glsr(SiteReviewsFiltersBlock::class)->renderRaw(
            glsr(SiteReviewsFilterDefaults::class)->restrict([
                'filters' => $data['args']->filters,
                'reviews_id' => $data['args']->reviews_id, // @phpstan-ignore-line
            ])
        );
        $search = '<div class="glsr-reviews-wrap">';
        if (Str::startsWith($template, $search)) {
            return str_replace($search, $search.$filters, $template);
        }
        return $filters.$template;
    }

    /**
     * @return void
     * @action site-reviews/review/build/before
     */
    public function highlightSearchResults(Review $review)
    {
        $parameters = glsr()->args(glsr(FilteredDefaults::class)->merge());
        if ($search = $parameters->search_for) {
            add_filter('site-reviews/option/reviews/excerpts', '__return_false'); // disable excerpts
            $search = preg_replace('/&#(x)?0*(?(1)27|39);?/i', "'", $search); // decode single quotes
            $search = '/('.preg_quote($search).')/i';
            $replace = '<mark>$1</mark>';
            $review->set('content', preg_replace($search, $replace, $review->content));
            $review->set('title', preg_replace($search, $replace, $review->title));
        }
    }

    /**
     * @return bool
     */
    protected function isUrlFiltered()
    {
        $filters = array_filter($_GET, function ($key) {
            return Str::startsWith($key, ['filter_', 'search_']);
        }, ARRAY_FILTER_USE_KEY);
        return !empty(array_filter($filters));
    }

    /**
     * {@inheritdoc}
     */
    protected function setAddon()
    {
        $this->addon = glsr(Application::class);
    }
}

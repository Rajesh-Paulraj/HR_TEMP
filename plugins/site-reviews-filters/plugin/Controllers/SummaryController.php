<?php

namespace GeminiLabs\SiteReviews\Addon\Filters\Controllers;

use GeminiLabs\SiteReviews\Addon\Filters\Application;
use GeminiLabs\SiteReviews\Addon\Filters\Tags\SummaryPercentagesTag;
use GeminiLabs\SiteReviews\Addons\Controller as AddonController;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Reviews;

class SummaryController extends AddonController
{
    /**
     * {@inheritdoc}
     */
    protected $addon;

    /**
     * @return array
     * @filter site-reviews/block/summary/attributes
     */
    public function filterBlockAttributes(array $attributes)
    {
        $attributes['filters'] = [
            'default' => false,
            'type' => 'boolean',
        ];
        $attributes['reviews_id'] = [
            'default' => '',
            'type' => 'string',
        ];
        return $attributes;
    }

    /**
     * @return array
     * @filter site-reviews/documentation/shortcodes/site_reviews_summary
     */
    public function filterDocumentation(array $paths)
    {
        $index1 = array_search('hide.php', array_map('basename', $paths));
        $paths = Arr::insertBefore($index1, $paths, [$this->addon->path('views/site_reviews_summary/filters.php')]);
        $index2 = array_search('schema.php', array_map('basename', $paths));
        $paths = Arr::insertBefore($index2, $paths, [$this->addon->path('views/site_reviews_summary/reviews_id.php')]);
        return $paths;
    }

    /**
     * @param \Elementor\Widget_Base $widget
     * @return array
     * @filter site-reviews/integration/elementor/register/controls
     */
    public function filterElementorWidgetControls(array $controls, $widget)
    {
        if ('site_reviews_summary' !== $widget->get_name()) {
            return $controls;
        }
        $options = [
            'filters' => [
                'label' => _x('Enable the filters?', 'admin-text', 'site-reviews-filters'),
                'return_value' => 'true',
                'type' => \Elementor\Controls_Manager::SWITCHER,
            ],
            'reviews_id' => [
                'default' => '',
                'description' => _x('Enter the Custom ID of a reviews widget to enable AJAX filtering.', 'admin-text', 'site-reviews-filters'),
                'label' => _x('Custom Reviews ID', 'admin-text', 'site-reviews-filters'),
                'label_block' => true,
                'separator' => 'before',
                'type' => \Elementor\Controls_Manager::TEXT,
            ],
        ];
        return Arr::insertAfter('settings', $controls, [
            'filter_settings' => [
                'label' => _x('Filters', 'admin-text', 'site-reviews-filters'),
                'options' => $options,
            ],
        ]);
    }

    /**
     * @return array
     * @filter site-reviews/defaults/site-reviews-summary/defaults
     */
    public function filterShortcodeDefaults(array $defaults)
    {
        $defaults['filters'] = false;
        $defaults['reviews_id'] = '';
        return $defaults;
    }

    /**
     * @param string $field
     * @param array $ratings
     * @param \GeminiLabs\SiteReviews\Shortcodes\SiteReviewsSummaryShortcode $shortcode
     * @return string
     * @filter site-reviews/summary/build/percentages
     */
    public function filterSummaryPercentagesTag($field, $ratings, $shortcode)
    {
        if (!Arr::get($shortcode->args, 'filters')) {
            return $field;
        }
        $args = $shortcode->args;
        $tag = 'percentages';
        $field = glsr(SummaryPercentagesTag::class, compact('tag', 'args'))->handleFor('summary', null, $ratings);
        return $field;
    }

    /**
     * {@inheritdoc}
     */
    protected function setAddon()
    {
        $this->addon = glsr(Application::class);
    }
}

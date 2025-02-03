<?php

namespace GeminiLabs\SiteReviews\Addon\Filters\Integrations\Elementor;

use GeminiLabs\SiteReviews\Addon\Filters\Shortcodes\SiteReviewsFilterShortcode;
use GeminiLabs\SiteReviews\Integrations\Elementor\ElementorWidget;

class ElementorFilterWidget extends ElementorWidget
{
    /**
     * @return string
     */
    public function get_icon()
    {
        return 'eicon-search-results';
    }

    /**
     * @return string
     */
    public function get_shortcode()
    {
        return SiteReviewsFilterShortcode::class;
    }

    /**
     * @return string
     */
    public function get_title()
    {
        return _x('Review Filters', 'admin-text', 'site-reviews-filters');
    }

    protected function settings_basic()
    {
        $options = [];
        $hideOptions = $this->get_shortcode_instance()->getHideOptions();
        foreach ($hideOptions as $key => $label) {
            $separator = $key === key(array_slice($hideOptions, 0, 1)) ? 'before' : 'default';
            $options['hide-'.$key] = [
                'label' => $label,
                'separator' => $separator,
                'return_value' => '1',
                'type' => \Elementor\Controls_Manager::SWITCHER,
            ];
        }
        $options['reviews_id'] = [
            'description' => _x('Link filters to a Reviews widget, block, or shortcode which has this Custom ID.', 'admin-text', 'site-reviews-filters'),
            'label_block' => true,
            'label' => _x('Reviews Custom ID', 'admin-text', 'site-reviews-filters'),
            'separator' => 'before',
            'type' => \Elementor\Controls_Manager::TEXT,
        ];
        return $options;
    }
}

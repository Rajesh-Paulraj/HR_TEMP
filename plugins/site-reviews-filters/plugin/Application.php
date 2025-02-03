<?php

namespace GeminiLabs\SiteReviews\Addon\Filters;

use GeminiLabs\SiteReviews\Addons\Addon;

final class Application extends Addon
{
    const ID = 'site-reviews-filters';
    const LICENSED = true;
    const NAME = 'Review Filters';
    const SLUG = 'filters';
    const UPDATE_URL = 'https://niftyplugins.com';

    public function categories()
    {
        $args = [
            'count' => false,
            'hide_empty' => true,
            'taxonomy' => glsr()->taxonomy,
        ];
        if (!glsr()->filterBool('builder/enable/optgroup', false)) {
            return get_terms(wp_parse_args($args, ['fields' => 'id=>name']));
        }
        $options = [];
        $terms = get_terms($args);
        foreach ($terms as $term) {
            if ($term->parent) {
                continue;
            }
            $children = array_filter($terms, function ($child) use ($term) {
                return $term->term_id === $child->parent;
            });
            if (empty($children)) {
                $options[$term->term_id] = $term->name;
                continue;
            }
            $options[$term->name] = [];
            foreach ($children as $child) {
                $options[$term->name][$child->term_id] = $child->name;
            }
        }
        return $options;
    }
}

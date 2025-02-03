<?php

namespace GeminiLabs\SiteReviews\Addon\Forms\Defaults;

use GeminiLabs\SiteReviews\Addon\Forms\Application;
use GeminiLabs\SiteReviews\Addon\Forms\Controllers\RestController;
use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class PostTypeDefaults extends Defaults
{
    /**
     * @return \GeminiLabs\SiteReviews\Application|\GeminiLabs\SiteReviews\Addons\Addon
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
            'capability_type' => Application::POST_TYPE,
            'exclude_from_search' => true,
            'has_archive' => false,
            'hierarchical' => false,
            'labels' => [],
            'map_meta_cap' => true,
            'menu_icon' => 'dashicons-star-half',
            'public' => false,
            'query_var' => true,
            'rest_base' => 'forms',
            'rest_controller_class' => RestController::class,
            'rewrite' => ['with_front' => false],
            'show_in_menu' => 'edit.php?post_type='.glsr()->post_type,
            'show_in_rest' => true,
            'show_ui' => true,
            'supports' => ['title'], // @todo add 'revisions'
            'taxonomies' => [],
        ];
    }
}

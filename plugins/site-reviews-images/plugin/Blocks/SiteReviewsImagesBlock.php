<?php

namespace GeminiLabs\SiteReviews\Addon\Images\Blocks;

use GeminiLabs\SiteReviews\Addon\Images\Application;
use GeminiLabs\SiteReviews\Addon\Images\Shortcodes\SiteReviewsImagesShortcode as Shortcode;
use GeminiLabs\SiteReviews\Blocks\Block;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Html\Builder;

class SiteReviewsImagesBlock extends Block
{
    /**
     * @return \GeminiLabs\SiteReviews\Application|\GeminiLabs\SiteReviews\Addons\Addon
     */
    public function app()
    {
        return glsr(Application::class);
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return [
            'assigned_post' => [
                'default' => '',
                'type' => 'string',
            ],
            'assigned_posts' => [
                'default' => '',
                'type' => 'string',
            ],
            'assigned_term' => [
                'default' => '',
                'type' => 'string',
            ],
            'assigned_terms' => [
                'default' => '',
                'type' => 'string',
            ],
            'assigned_user' => [
                'default' => '',
                'type' => 'string',
            ],
            'assigned_users' => [
                'default' => '',
                'type' => 'string',
            ],
            'className' => [
                'default' => '',
                'type' => 'string',
            ],
            'display' => [
                'default' => 8,
                'type' => 'number',
            ],
            'hide' => [
                'default' => '',
                'type' => 'string',
            ],
            'id' => [
                'default' => '',
                'type' => 'string',
            ],
            'rating' => [
                'default' => 0,
                'type' => 'number',
            ],
            'terms' => [
                'default' => '',
                'type' => 'string',
            ],
            'type' => [
                'default' => 'local',
                'type' => 'string',
            ],
        ];
    }

    /**
     * @return array
     */
    public function normalizeAttributes(array $attributes)
    {
        $attributes['class'] = Arr::get($attributes, 'className');
        if ('edit' == filter_input(INPUT_GET, 'context')) {
            $attributes = $this->normalize($attributes);
            $this->filterInterpolation();
        }
        return $attributes;
    }

    /**
     * @return string
     */
    public function render(array $attributes)
    {
        return glsr(Shortcode::class)->buildBlock(
            $this->normalizeAttributes($attributes)
        );
    }

    /**
     * @return string
     */
    public function renderRaw(array $attributes)
    {
        $shortcode = glsr(Shortcode::class);
        $attributes = $this->normalizeAttributes($attributes);
        $attributes = $shortcode->normalizeAtts($attributes, 'block');
        return $shortcode->buildTemplate($attributes->toArray());
    }

    /**
     * @return void
     */
    protected function filterInterpolation()
    {
        add_filter('site-reviews-images/interpolate/review-images', function ($context, $template, $data) {
            if (empty($data['results']['total'])) {
                $context['class'] = 'block-editor-warning';
                $context['link'] = glsr(Builder::class)->p([
                    'class' => 'block-editor-warning__message',
                    'text' => _x('No review images were found.', 'admin-text', 'site-reviews-images'),
                ]);
            }
            return $context;
        }, 10, 3);
    }
}

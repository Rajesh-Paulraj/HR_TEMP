<?php

namespace GeminiLabs\SiteReviews\Addon\Filters\Blocks;

use GeminiLabs\SiteReviews\Addon\Filters\Application;
use GeminiLabs\SiteReviews\Addon\Filters\Shortcodes\SiteReviewsFilterShortcode as Shortcode;
use GeminiLabs\SiteReviews\Blocks\Block;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Html\Builder;

class SiteReviewsFiltersBlock extends Block
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
            'className' => [
                'default' => '',
                'type' => 'string',
            ],
            'hide' => [
                'default' => '',
                'type' => 'string',
            ],
            'id' => [
                'default' => '',
                'type' => 'string',
            ],
            'reviews_id' => [
                'default' => '',
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
            if (!$this->hasVisibleFields(glsr(Shortcode::class), $attributes)) {
                $this->filterInterpolation();
            }
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
        add_filter('site-reviews-filters/interpolate/reviews-filter', function ($context) {
            $context['class'] = 'block-editor-warning';
            $context['status'] = glsr(Builder::class)->p([
                'class' => 'block-editor-warning__message',
                'text' => _x('You have hidden all of the fields for this block. However, if you have enabled the filters on the Rating Summary block then the filtered status will display here.', 'admin-text', 'site-reviews-filters'),
            ]);
            return $context;
        });
    }
}

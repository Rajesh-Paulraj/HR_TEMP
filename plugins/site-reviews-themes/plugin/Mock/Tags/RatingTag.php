<?php

namespace GeminiLabs\SiteReviews\Addon\Themes\Mock\Tags;

use GeminiLabs\SiteReviews\Addon\Themes\Application;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Html\Builder;

class RatingTag extends Tag
{
    /**
     * {@inheritdoc}
     */
    protected function handle($value = null)
    {
        $image = file_get_contents(
            glsr(Application::class)->path('assets/images/rating/rating-star.svg')
        );
        $numEmpty = abs(5 - Cast::toInt($value));
        $numFull = abs(5 - $numEmpty);
        $fullStar = glsr(Builder::class)->span([
            'class' => 'glsr-rating-level glsr-rating-full',
            'text' => trim($image),
        ]);
        $emptyStar = glsr(Builder::class)->span([
            'class' => 'glsr-rating-level glsr-rating-empty',
            'text' => trim($image),
        ]);
        return glsr(Builder::class)->div([
            'class' => 'glsr-themed-rating',
            'data-rating' => $value,
            'text' => str_repeat($fullStar, $numFull).str_repeat($emptyStar, $numEmpty),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function value($value = null)
    {
        return '4';
    }
}

<?php

namespace GeminiLabs\SiteReviews\Addon\Filters\Tags;

use GeminiLabs\SiteReviews\Addon\Filters\Template;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Html\Tags\SummaryPercentagesTag as BaseTag;
use GeminiLabs\SiteReviews\Modules\Rating;

class SummaryPercentagesTag extends BaseTag
{
    /**
     * @return void|string
     */
    protected function percentages()
    {
        $percentages = preg_filter('/$/', '%', glsr(Rating::class)->percentages($this->ratings));
        $ratingRange = range(glsr()->constant('MAX_RATING', Rating::class), 1);
        return array_reduce($ratingRange, function ($carry, $level) use ($percentages) {
            $label = $this->ratingLabel($level);
            $bar = $this->ratingBar($level, $percentages);
            $info = $this->ratingInfo($level, $percentages);
            $value = $label.$bar.$info;
            $value = glsr()->filterString('summary/wrap/bar', $value, $this->args, [
                'info' => wp_strip_all_tags($info, true),
                'rating' => $level,
            ]);
            $title = _n('{num} star represents {percent} of the rating', '{num} stars represent {percent} of the rating', $level, 'site-reviews-filters');
            $title = glsr(Template::class)->interpolateContext($title, [
                'num' => $level,
                'percent' => wp_strip_all_tags($info, true),
            ]);
            return $carry.glsr(Builder::class)->a([
                'class' => $this->getFilterClasses($level), // 'glsr-bar',
                'href' => '?filter_by_rating='.$level,
                'data-level' => $level,
                'text' => $value,
                'title' => $title,
            ]);
        });
    }

    /**
     * @return string
     */
    protected function getFilterClasses($rating)
    {
        $classes = [
            'glsr-bar',
            'glsr-bar-filter',
        ];
        $activeFilter = filter_input(INPUT_GET, 'filter_by_rating');
        $min = glsr()->constant('MIN_RATING', Rating::class);
        $max = glsr()->constant('MAX_RATING', Rating::class);
        $ratings = [
            'critical' => range($min, 3),
            'positive' => range(4, $max),
        ];
        $values = Arr::get($ratings, $activeFilter, []);
        if (Cast::toInt($activeFilter) === Cast::toInt($rating) || in_array($rating, $values)) {
            $classes[] = 'glsr-bar-active';
        }
        return implode(' ', $classes);
    }
}

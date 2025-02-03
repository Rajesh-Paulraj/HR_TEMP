<?php

namespace GeminiLabs\SiteReviews\Addon\Themes\Partials;

use GeminiLabs\SiteReviews\Addon\Themes\Application;
use GeminiLabs\SiteReviews\Addon\Themes\Style;
use GeminiLabs\SiteReviews\Addon\Themes\Template;
use GeminiLabs\SiteReviews\Addon\Themes\ThemeSettings;
use GeminiLabs\SiteReviews\Contracts\PartialContract;
use GeminiLabs\SiteReviews\Defaults\StarRatingDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Rating;

class StarRating implements PartialContract
{
    /**
     * @var \GeminiLabs\SiteReviews\Arguments
     */
    public $data;

    /**
     * {@inheritdoc}
     */
    public function build(array $data = [])
    {
        $this->data($data);
        $maxRating = glsr()->constant('MAX_RATING', Rating::class);
        $numFull = intval(floor($this->data->rating));
        $numHalf = intval(ceil($this->data->rating - $numFull));
        $numEmpty = max(0, $maxRating - $numFull - $numHalf);
        $title = $this->data->count > 0
            ? __('Rated %s out of %s based on %s ratings', 'site-reviews')
            : __('Rated %s out of %s', 'site-reviews');
        return glsr(Template::class)->build('templates/rating', [
            'args' => glsr()->args($this->data->args),
            'context' => [
                'class' => 'glsr-themed-rating',
                'empty_stars' => $this->stars('empty', $numEmpty),
                'full_stars' => $this->stars('full', $numFull),
                'half_stars' => $this->stars('half', $numHalf),
                'rating' => round($this->data->rating),
                'star' => $this->data->theme->get('rating_image', 'rating-star'),
                'style' => $this->style(),
                'title' => sprintf($title, $this->data->rating, $maxRating, $this->data->count),
            ],
            'partial' => $this,
        ]);
    }

    /**
     * @return static
     */
    public function data(array $data = [])
    {
        $data = glsr(StarRatingDefaults::class)->merge($data);
        $design = glsr(ThemeSettings::class)
            ->themeId(Arr::get($data, 'args.theme'))
            ->get('design.rating');
        $data['theme'] = glsr()->args(Arr::consolidate($design));
        $this->data = glsr()->args($data);
        return $this;
    }

    /**
     * @param string $type
     * @param int $timesRepeated
     * @return string
     */
    public function stars($type, $timesRepeated)
    {
        $template = glsr(Builder::class)->span([
            'aria-hidden' => 'true',
            'class' => sprintf('glsr-rating-level glsr-rating-%s', $type),
            'style' => '',
            'text' => $this->svg(round($this->data->rating)),
        ]);
        return str_repeat($template, $timesRepeated);
    }

    /**
     * @return string|void
     */
    public function style()
    {
        $themeId = Arr::get($this->data->args, 'theme');
        return glsr(Style::class)->themeId($themeId)
            ->only([
                '--gl-rating-color-0', '--gl-rating-color-1', '--gl-rating-color-2',
                '--gl-rating-color-3', '--gl-rating-color-4', '--gl-rating-color-5',
                '--gl-rating-size',
            ])
            ->toString();
    }

    /**
     * @param int|float $rating
     * @return string|void
     */
    public function svg($rating)
    {
        $imageName = $this->data->theme->get('rating_image', 'rating-star');
        $fileName = Str::startsWith($imageName, 'rating-emoji')
            ? sprintf('%s-%d.svg', $imageName, $rating)
            : sprintf('%s.svg', $imageName);
        $filePath = glsr(Application::class)->path('assets/images/rating/'.$fileName);
        if (file_exists($filePath)) {
            $image = file_get_contents($filePath);
            $image = str_replace('gl-gradient-', sprintf('gl-%s', Str::random()), $image);
            return $image;
        }
    }
}

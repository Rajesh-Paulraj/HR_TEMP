<?php

namespace GeminiLabs\SiteReviews\Addon\Themes\Fields;

use GeminiLabs\SiteReviews\Addon\Themes\Application;
use GeminiLabs\SiteReviews\Addon\Themes\ThemeSettings;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Html\Fields\Field;
use GeminiLabs\SiteReviews\Modules\Rating as RatingModule;

class Rating extends Field
{
    /**
     * @var \GeminiLabs\SiteReviews\Modules\Html\Builder
     */
    protected $builder;

    /**
     * @var \GeminiLabs\SiteReviews\Addon\Themes\ThemeSettings
     */
    protected $themeSettings;

    public function __construct($builder)
    {
        $this->builder = $builder;
        $this->themeSettings = glsr(ThemeSettings::class)->themeId($this->args()->theme);
    }

    /**
     * This is used to build the custom Field type.
     * @return string|void
     */
    public function build()
    {
        $select = $this->builder->select($this->args()->toArray());
        $stars = static::ratingStars();
        return $this->builder->span([
            'class' => 'glsr-star-rating',
            'text' => $select.$stars,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public static function required($fieldLocation = null)
    {
        $options = [];
        foreach (range(glsr()->constant('MAX_RATING', RatingModule::class), 1) as $rating) {
            $options[$rating] = sprintf(_n('%s Star', '%s Stars', $rating, 'site-reviews'), $rating);
        }
        return [
            'class' => 'glsr-select browser-default no_wrap no-wrap',
            'data-options' => ['prebuilt' => true],
            'options' => $options,
            'placeholder' => __('Select a Rating', 'site-reviews'),
            'raw_type' => 'rating',
        ];
    }

    /**
     * @param int $rating
     * @return string
     */
    public function ratingImage($rating)
    {
        $imageName = $this->themeSettings->get('design.rating.rating_image');
        $fileName = Str::startsWith($imageName, 'rating-emoji')
            ? sprintf('%s-%d.svg', $imageName, $rating)
            : sprintf('%s.svg', $imageName);
        $image = file_get_contents(glsr(Application::class)->path('assets/images/rating/'.$fileName));
        $image = str_replace('gl-gradient-', sprintf('gl-%s', Str::random()), $image);
        return $image;
    }

    /**
     * @return string
     */
    public function ratingStars()
    {
        $ratings = glsr(RatingModule::class)->emptyArray();
        $stars = [];
        $i = 0;
        foreach ($ratings as $rating => $value) {
            if ($rating > 0) {
                $stars[] = $this->builder->span([
                    'data-index' => $i++,
                    'data-value' => $rating,
                    'text' => $this->ratingImage($rating),
                ]);
            }
        }
        return $this->builder->span([
            'class' => 'glsr-star-rating--stars',
            'data-stars' => $this->themeSettings->get('design.rating.rating_image'),
            'text' => implode('', $stars),
        ]);
    }
}

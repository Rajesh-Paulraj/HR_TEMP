<?php

namespace GeminiLabs\SiteReviews\Addon\Forms\Fields;

use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Rating;

class ReviewRating extends Field
{
    public $name = 'rating';
    public $options = ['label', 'required', 'type', 'tag_label', 'value'];
    public $tag = 'rating';

    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'value' => 0,
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function handle()
    {
        return _x('Review: Rating', 'admin-text', 'site-reviews-forms');
    }

    /**
     * {@inheritdoc}
     */
    protected function type()
    {
        return 'review_rating';
    }

    /**
     * {@inheritdoc}
     */
    protected function validation()
    {
        $maxRating = Cast::toInt(glsr()->constant('MAX_RATING', Rating::class));
        return [
            'name' => 'required|slug|unique',
            'type' => 'unique',
            'value' => 'number|between:0,'.$maxRating,
        ];
    }
}

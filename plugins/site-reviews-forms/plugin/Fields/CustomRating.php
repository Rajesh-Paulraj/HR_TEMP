<?php

namespace GeminiLabs\SiteReviews\Addon\Forms\Fields;

use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Rating;

class CustomRating extends Field
{
    public $options = [
        'label', 'name', 'required', 'tag', 'tag_label', 'type', 'value',
    ];

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
        return _x('Custom: Rating', 'admin-text', 'site-reviews-forms');
    }

    /**
     * {@inheritdoc}
     */
    protected function type()
    {
        return 'rating';
    }

    /**
     * {@inheritdoc}
     */
    protected function validation()
    {
        $maxRating = Cast::toInt(glsr()->constant('MAX_RATING', Rating::class));
        return [
            'value' => 'number|between:0,'.$maxRating,
        ];
    }
}

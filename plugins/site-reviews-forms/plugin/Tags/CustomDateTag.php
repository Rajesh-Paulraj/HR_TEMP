<?php

namespace GeminiLabs\SiteReviews\Addon\Forms\Tags;

use GeminiLabs\SiteReviews\Modules\Html\Tags\Tag;

class CustomDateTag extends Tag
{
    /**
     * {@inheritdoc}
     */
    protected function handle($value = null)
    {
        $format = $this->with->get('format', 'F j, Y');
        $value = date_i18n($format, strtotime($value));
        return $this->wrap($value);
    }
}

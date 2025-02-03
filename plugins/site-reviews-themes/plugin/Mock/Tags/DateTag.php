<?php

namespace GeminiLabs\SiteReviews\Addon\Themes\Mock\Tags;

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Modules\Date;

class DateTag extends Tag
{
    /**
     * {@inheritdoc}
     */
    protected function handle($value = null)
    {
        $dateFormat = glsr_get_option('reviews.date.format', 'default');
        if ('relative' == $dateFormat) {
            return glsr(Date::class)->relative($value);
        }
        $format = 'custom' == $dateFormat
            ? glsr_get_option('reviews.date.custom', 'M j, Y')
            : glsr(OptionManager::class)->getWP('date_format', 'F j, Y');
        return date_i18n($format, strtotime($value));
    }

    /**
     * @param mixed $value
     * @return string
     */
    protected function value($value = null)
    {
        return wp_date('Y-m-d H:i:s', strtotime('-3 Months'));
    }
}

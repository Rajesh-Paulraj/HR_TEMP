<?php

namespace GeminiLabs\SiteReviews\Addon\Filters\Tags;

use GeminiLabs\SiteReviews\Addon\Filters\Application;
use GeminiLabs\SiteReviews\Modules\Style;

class SearchForTag extends Tag
{
    /**
     * @return string
     */
    protected function getClasses()
    {
        return 'glsr-search-for';
    }

    /**
     * @return array
     */
    protected function getContext()
    {
        $buttonText = __('Search', 'site-reviews-filters');
        if ($fields = $this->fields()) {
            return [
                'button_class' => glsr(Style::class)->classes('button'),
                'class' => $this->getClasses(),
                'search' => $fields,
                'submit_text' => $buttonText,
            ];
        }
        return [];
    }
}

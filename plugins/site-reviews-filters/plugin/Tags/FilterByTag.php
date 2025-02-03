<?php

namespace GeminiLabs\SiteReviews\Addon\Filters\Tags;

use GeminiLabs\SiteReviews\Addon\Filters\Application;

class FilterByTag extends Tag
{
    /**
     * @return string
     */
    protected function getClasses()
    {
        return 'glsr-filter-by';
    }

    /**
     * @return array
     */
    protected function getContext()
    {
        if ($fields = $this->fields()) {
            return [
                'class' => $this->getClasses(),
                'fields' => $fields,
                'label' => __('Filter by', 'site-reviews-filters'),
            ];
        }
        return [];
    }
}

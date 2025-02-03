<?php

namespace GeminiLabs\SiteReviews\Addon\Filters\Tags;

use GeminiLabs\SiteReviews\Addon\Filters\Application;

class SortByTag extends Tag
{
    /**
     * @return string
     */
    protected function getClasses()
    {
        return 'glsr-sort-by';
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
                'label' => __('Sort by', 'site-reviews-filters'),
            ];
        }
        return [];
    }
}

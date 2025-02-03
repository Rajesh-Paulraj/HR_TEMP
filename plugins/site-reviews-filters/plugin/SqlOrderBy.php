<?php

namespace GeminiLabs\SiteReviews\Addon\Filters;

class SqlOrderBy extends SqlModifier
{
    /**
     * @param string $key
     * @param string $value
     * @return void
     */
    protected function buildSortBy($key, $value)
    {
        $orderByOptions = [
            'rating' => ['r.rating desc', 'p.post_date desc'],
            // 'recent' => ['p.post_date desc'],
        ];
        if (array_key_exists($value, $orderByOptions)) {
            $this->values = $orderByOptions[$value];
        }
    }
}

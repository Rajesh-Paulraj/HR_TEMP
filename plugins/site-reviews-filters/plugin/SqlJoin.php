<?php

namespace GeminiLabs\SiteReviews\Addon\Filters;

class SqlJoin extends SqlModifier
{
    /**
     * @param string $key
     * @return void
     */
    protected function buildSearchFor($key)
    {
        global $wpdb;
        $this->values[$key] = "INNER JOIN {$wpdb->posts} AS p ON r.review_id = p.ID";
    }
}

<?php

namespace GeminiLabs\SiteReviews\Addon\Images\Columns;

use GeminiLabs\SiteReviews\Contracts\ColumnValueContract;
use GeminiLabs\SiteReviews\Review;

class ColumnValueImages implements ColumnValueContract
{
    /**
     * {@inheritdoc}
     */
    public function handle(Review $review)
    {
        return (string) count($review->images);
    }
}

<?php

namespace GeminiLabs\SiteReviews\Addon\Authors;

use GeminiLabs\SiteReviews\Addons\Addon;
use GeminiLabs\SiteReviews\Review;

final class Application extends Addon
{
    public const ID = 'site-reviews-authors';
    public const LICENSED = true;
    public const NAME = 'Review Authors';
    public const SLUG = 'authors';
    public const UPDATE_URL = 'https://niftyplugins.com';

    public function canEditOnFrontend(Review $review): bool
    {
        if (!$review->isValid()) {
            return false; // invalid review
        }
        $user = wp_get_current_user();
        if (in_array('administrator', (array) $user->roles)) {
            return true; // is administrator
        }
        $roles = glsr_get_option('addons.authors.roles', [], 'array');
        $hasRole = !empty(array_intersect($roles, (array) $user->roles));
        if ($hasRole && $user->ID === $review->author_id) {
            return true; // is review author
        }
        if ($hasRole && glsr()->can('edit_post', $review->ID)) {
            return true; // has edit capability
        }
        return false;
    }
}

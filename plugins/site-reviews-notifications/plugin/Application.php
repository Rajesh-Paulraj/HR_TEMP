<?php

namespace GeminiLabs\SiteReviews\Addon\Notifications;

use GeminiLabs\SiteReviews\Addon\Notifications\Defaults\NotificationDefaults;
use GeminiLabs\SiteReviews\Addons\Addon;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;

final class Application extends Addon
{
    const ID = 'site-reviews-notifications';
    const LICENSED = true;
    const NAME = 'Review Notifications';
    const SLUG = 'notifications';
    const UPDATE_URL = 'https://niftyplugins.com';

    /**
     * @return array
     */
    public function notifications()
    {
        $notifications = Arr::consolidate(get_option(Str::snakeCase(static::ID)));
        array_walk($notifications, function (&$notification) {
            $notification = glsr(NotificationDefaults::class)->restrict($notification);
        });
        return wp_unslash($notifications); // unslash data!
    }
}

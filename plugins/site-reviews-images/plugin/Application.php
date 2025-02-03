<?php

namespace GeminiLabs\SiteReviews\Addon\Images;

use GeminiLabs\SiteReviews\Addons\Addon;

final class Application extends Addon
{
    const ID = 'site-reviews-images';
    const LICENSED = true;
    const NAME = 'Review Images';
    const SLUG = 'images';
    const UPDATE_URL = 'https://niftyplugins.com';

    /**
     * @return string|false
     */
    public function imageModal()
    {
        $isModalDisabled = glsr_get_option('addons.'.static::SLUG.'.disable_modal', false, 'bool');
        $modal = glsr_get_option('addons.'.static::SLUG.'.modal', 'modal');
        if (!$isModalDisabled) {
            return $modal;
        }
        return false;
    }
}

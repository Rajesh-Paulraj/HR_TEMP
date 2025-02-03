<?php

namespace GeminiLabs\SiteReviews\Addon\Themes\Mock\Tags;

use GeminiLabs\SiteReviews\Addon\Themes\Application;

class ImagesTag extends Tag
{
    /**
     * {@inheritdoc}
     */
    protected function value($value = null)
    {
        $link = sprintf('<a class="glsr-image" href="javascript:void(0)"><img src="%s" width="640" height="480"/></a>',
            glsr(Application::class)->url('assets/images/photos/%s.jpg')
        );
        return sprintf($link, '1').sprintf($link, '2');
    }
}

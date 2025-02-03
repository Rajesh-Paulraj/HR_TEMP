<?php

namespace GeminiLabs\SiteReviews\Addon\Notifications;

use GeminiLabs\SiteReviews\Modules\Queue as Base;

class Queue extends Base
{
    /**
     * {@inheritdoc}
     */
    public function app()
    {
        return glsr(Application::class);
    }
}

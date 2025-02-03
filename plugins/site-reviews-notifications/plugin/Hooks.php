<?php

namespace GeminiLabs\SiteReviews\Addon\Notifications;

use GeminiLabs\SiteReviews\Addon\Notifications\Controllers\Controller;
use GeminiLabs\SiteReviews\Addons\Hooks as AddonHooks;

class Hooks extends AddonHooks
{
    /**
     * @return void
     */
    public function run()
    {
        parent::run();
        add_filter('site-reviews/enqueue/admin/localize', [$this->controller, 'filterLocalizedAdminVariables']);
        add_filter('site-reviews/settings/sanitize', [$this->controller, 'filterSettingsCallback'], 10, 2);
        add_action('site-reviews/route/ajax/notifications', [$this->controller, 'notificationsAjax']);
        add_action('site-reviews/review/created', [$this->controller, 'queueAfterCreated'], 20, 2);
        add_action('site-reviews/review/responded', [$this->controller, 'queueAfterResponded'], 10, 2);
        add_action('site-reviews/review/approved', [$this->controller, 'queueAfterStatusChange'], 10, 3);
        add_action('site-reviews/review/unapproved', [$this->controller, 'queueAfterStatusChange'], 10, 3);
        add_action('admin_footer-site-review_page_glsr-settings', [$this->controller, 'renderTemplates']);
        add_action('site-reviews-notifications/queue/notification', [$this->controller, 'sendNotification'], 10, 2);
    }

    /**
     * @return mixed
     */
    protected function addon()
    {
        return glsr(Application::class);
    }

    /**
     * @return mixed
     */
    protected function controller()
    {
        return glsr(Controller::class);
    }
}

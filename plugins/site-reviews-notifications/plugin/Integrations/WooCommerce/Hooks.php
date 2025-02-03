<?php

namespace GeminiLabs\SiteReviews\Addon\Notifications\Integrations\WooCommerce;

use GeminiLabs\SiteReviews\Contracts\HooksContract;

class Hooks implements HooksContract
{
    /**
     * @var Controller
     */
    public $controller;

    public function __construct()
    {
        $this->controller = glsr(Controller::class);
    }

    /**
     * @return void
     */
    public function run(): void
    {
        if (!$this->isEnabled()) {
            return;
        }
        $status = Option::get('reminder_trigger', 'completed');
        add_filter('woocommerce_email_classes', [$this->controller, 'filterEmailClasses']);
        add_filter('wc_get_template', [$this->controller, 'filterTemplate'], 10, 2);
        add_action('woocommerce_order_status_'.$status, [$this->controller, 'scheduleReminderEmail'], 20);
        add_action('site-reviews-notifications/product/reminder', [$this->controller, 'sendReminderEmail']);
        add_action('action_scheduler_before_execute', [$this->controller, 'verifyReminderEmail']);
    }

    protected function isEnabled(): bool
    {
        if (!function_exists('WC') || !defined('WC_ABSPATH')) {
            return false;
        }
        if (!glsr_get_option('addons.woocommerce.enabled', false, 'bool')) {
            return false;
        }
        if ('yes' !== get_option('woocommerce_enable_reviews', 'yes')) {
            return false;
        }
        return true;
    }
}

<?php

namespace GeminiLabs\SiteReviews\Addon\Notifications\Integrations\WooCommerce;

use GeminiLabs\SiteReviews\Addon\Notifications\Application;
use GeminiLabs\SiteReviews\Addon\Notifications\Queue;
use GeminiLabs\SiteReviews\Addon\Notifications\Integrations\WooCommerce\ReminderEmail;
use GeminiLabs\SiteReviews\Addons\Controller as AddonController;
use GeminiLabs\SiteReviews\Helpers\Arr;

class Controller extends AddonController
{
    protected $addon;

    /**
     * @return array
     * @filter woocommerce_email_classes
     */
    public function filterEmailClasses(array $emails)
    {
        $emails[glsr()->prefix.ReminderEmail::ID] = glsr(ReminderEmail::class);
        return $emails;
    }

    /**
     * @param string $template
     * @param string $templateName
     * @return string
     * @filter wc_get_template
     */
    public function filterTemplate($template, $templateName)
    {
        $templateNames = [
            'woocommerce/review-reminder-html.php',
            'woocommerce/review-reminder-plain.php',
        ];
        if (!in_array($templateName, $templateNames)) {
            return $template;
        }
        return $this->addon->path(sprintf('templates/%s', $templateName));
    }

    /**
     * @param int $orderId
     * @return void
     * @action woocommerce_order_status_<completed|processing>
     */
    public function scheduleReminderEmail($orderId)
    {
        $order = wc_get_order($orderId);
        if (!$this->isQueueable($order)) {
            return;
        }
        if ($this->addon->filterBool('product/reminder/skip', false, $order)) {
            $order->add_order_note(
                _x('A review reminder was not scheduled due to a custom filter hook.', 'admin-text', 'site-reviews-notifications')
            );
            return;
        }
        $timestamp = $this->timestamp();
        $scheduled = glsr(Queue::class)->once($timestamp, 'product/reminder', ['order' => $order->get_id()]);
        if (0 === $scheduled) {
            $order->add_order_note(
                _x('A review reminder could not be scheduled.', 'admin-text', 'site-reviews-notifications')
            );
        } else {
            $order->add_order_note(
                sprintf(_x('A review reminder was scheduled for %s at %s.', 'admin-text', 'site-reviews-notifications'),
                    date_i18n('F j, Y', $timestamp),
                    date_i18n('g:i A', $timestamp)
                )
            );
        }
    }

    /**
     * @param int $orderId
     * @return void
     * @action site-reviews-notifications/product/reminder
     */
    public function sendReminderEmail($orderId)
    {
        $email = WC()->mailer()->emails[glsr()->prefix.ReminderEmail::ID];
        if ($email->trigger($orderId)) { // @phpstan-ignore-line
            update_post_meta($orderId, '_review_reminder_sent', 1);
        }
    }

    /**
     * @return void
     * @action action_scheduler_before_execute
     */
    public function verifyReminderEmail($actionId)
    {
        $action = glsr(Queue::class)->fetchAction($actionId);
        if ($this->addon->id.'/product/reminder' !== $action->get_hook()) {
            return;
        }
        $order = wc_get_order(Arr::get($action->get_args(), 'order'));
        if (!is_a($order, 'WC_Order')) {
            return; // not a valid order ID
        }
        if (Option::get('reminder_recheck', false, 'bool') && $order->get_status() !== Option::get('reminder_trigger', 'completed')) {
            glsr(Queue::class)->cancelAction($actionId);
        }
    }

    /**
     * @param \WC_Order $order
     * @return bool
     */
    protected function isCategoryInOrder($order)
    {
        $termIds = Arr::uniqueInt(Option::get('reminder_categories', [], 'array'));
        if (empty($termIds)) {
            return true;
        }
        $items = $order->get_items();
        foreach ($items as $itemId => $item) {
            if (!apply_filters('woocommerce_order_item_visible', true, $item)) {
                continue;
            }
            $categories = get_the_terms($item['product_id'], 'product_cat');
            $categories = wp_list_pluck($categories, 'term_id');
            if (!empty(array_intersect($categories, $termIds))) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param \WC_Order $order
     * @return bool
     */
    protected function isQueueable($order)
    {
        if (!is_a($order, 'WC_Order')) {
            return false; // this is not an order
        }
        if (!Option::get('enabled', false, 'bool')) {
            return false; // reminders are disabled
        }
        if (function_exists('wcs_order_contains_renewal') && wcs_order_contains_renewal($order->get_id())) {
            return false; // Stripe: don't send reminders for renewals
        }
        if (!$this->isCategoryInOrder($order)) {
            return false; // the order does not contain the required category
        }
        if (!$order->get_user() && !Option::get('reminder_guests', false, 'bool')) {
            return false; // do not send reminders to guest users
        }
        if (glsr(Queue::class)->isPending('product/reminder', ['order' => $order->get_id()])) {
            return false; // do not queue duplicate reminders
        }
        if (!empty(get_post_meta($order->get_id(), '_review_reminder_sent', true))) {
            return false; // only send one reminder per order
        }
        return true;
    }

    /**
     * @return void
     */
    protected function setAddon()
    {
        $this->addon = glsr(Application::ID);
    }

    protected function timestamp(): int
    {
        $days = Option::get('reminder_delay', 7, 'int'); // 7 is the default
        return time() + ($days * DAY_IN_SECONDS);
    }
}

<?php

namespace GeminiLabs\SiteReviews\Addon\Notifications\Controllers;

use GeminiLabs\SiteReviews\Addon\Notifications\Application;
use GeminiLabs\SiteReviews\Addon\Notifications\Defaults\NotificationDefaults;
use GeminiLabs\SiteReviews\Addon\Notifications\Defaults\SettingsDefaults;
use GeminiLabs\SiteReviews\Addon\Notifications\Notifications;
use GeminiLabs\SiteReviews\Addon\Notifications\Template;
use GeminiLabs\SiteReviews\Addons\Controller as AddonController;
use GeminiLabs\SiteReviews\Commands\CreateReview;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Color;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Html\TemplateTags;
use GeminiLabs\SiteReviews\Request;
use GeminiLabs\SiteReviews\Review;

class Controller extends AddonController
{
    protected $addon;

    /**
     * @return void
     * @action admin_enqueue_scripts
     */
    public function enqueueAdminAssets()
    {
        if ('site-review_page_glsr-settings' === glsr_current_screen()->id) {
            wp_enqueue_editor();
            $this->enqueueAsset('css', ['suffix' => 'admin']);
            $this->enqueueAsset('js', [
                'dependencies' => [glsr()->id.'/admin', 'backbone', 'wp-api-fetch'],
                'suffix' => 'admin',
            ]);
        }
    }

    /**
     * @return array
     * @filter site-reviews/enqueue/admin/localize
     */
    public function filterLocalizedAdminVariables(array $variables)
    {
        $variables['addons'][Application::ID] = [
            'criteria' => [
                'conditions' => [
                    'always' => _x('Send always', 'admin-text', 'site-reviews-notifications'),
                    'all' => _x('Send when all of the conditions pass', 'admin-text', 'site-reviews-notifications'),
                    'any' => _x('Send when any of the conditions pass', 'admin-text', 'site-reviews-notifications'),
                ],
                'fields' => [
                    'assigned_post_author' => _x('Assigned Post Author ID', 'admin-text', 'site-reviews-notifications'),
                    'assigned_post' => _x('Assigned Post ID', 'admin-text', 'site-reviews-notifications'),
                    'assigned_user' => _x('Assigned User ID', 'admin-text', 'site-reviews-notifications'),
                    'assigned_term' => _x('Category ID', 'admin-text', 'site-reviews-notifications'),
                    'rating' => _x('Rating', 'admin-text', 'site-reviews-notifications'),
                    'review' => _x('Review', 'admin-text', 'site-reviews-notifications'),
                ],
                'operators' => [
                    'contains' => _x('Contains', 'admin-text', 'site-reviews-notifications'),
                    'equals' => _x('Equals', 'admin-text', 'site-reviews-notifications'),
                    'greater' => _x('Greater Than', 'admin-text', 'site-reviews-notifications'),
                    'less' => _x('Less Than', 'admin-text', 'site-reviews-notifications'),
                    'not' => _x('Not', 'admin-text', 'site-reviews-notifications'),
                ],
                'restrictions' => [
                    'assigned_post_author' => [
                        'operators' => ['contains', 'equals', 'not'],
                        'values' => [],
                    ],
                    'assigned_post' => [
                        'operators' => ['contains', 'equals', 'not'],
                        'values' => [],
                    ],
                    'assigned_user' => [
                        'operators' => ['contains', 'equals', 'not'],
                        'values' => [],
                    ],
                    'assigned_term' => [
                        'operators' => ['contains', 'equals', 'not'],
                        'values' => [],
                    ],
                    'rating' => [
                        'operators' => ['contains', 'equals', 'greater', 'less', 'not'],
                        'values' => [],
                    ],
                    'review' => [
                        'operators' => [],
                        'values' => ['approved', 'unapproved', 'responded'],
                    ],
                ],
                'values' => [ // order is intentional
                    'approved' => _x('was changed to approved', 'admin-text', 'site-reviews-notifications'),
                    'unapproved' => _x('was changed to unapproved', 'admin-text', 'site-reviews-notifications'),
                    'responded' => _x('was responded to', 'admin-text', 'site-reviews-notifications'),
                ],
            ],
            'recipients' => [
                'admin' => _x('Administrator', 'admin-text', 'site-reviews-notifications'),
                'assigned_post_author' => _x('Assigned Post Author', 'admin-text', 'site-reviews-notifications'),
                'assigned_user' => _x('Assigned User', 'admin-text', 'site-reviews-notifications'),
                'reviewer' => _x('Reviewer', 'admin-text', 'site-reviews-notifications'),
            ],
            'data' => wp_unslash(Arr::consolidate(get_option(Str::snakeCase(Application::ID)))), // unslash data!
            'labels' => [
                'conditions' => _x('Send Conditions', 'admin-text', 'site-reviews-notifications'),
                'enabled' => _x('Enabled', 'admin-text', 'site-reviews-notifications'),
                'handle' => _x('Notification Name', 'admin-text', 'site-reviews-notifications'),
                'header' => _x('Email Header', 'admin-text', 'site-reviews-notifications'),
                'message' => _x('Email Message', 'admin-text', 'site-reviews-notifications'),
                'recipients' => _x('Recipients', 'admin-text', 'site-reviews-notifications'),
                'schedule' => _x('Send Schedule', 'admin-text', 'site-reviews-notifications'),
                'subject' => _x('Email Subject', 'admin-text', 'site-reviews-notifications'),
            ],
            'messages' => [
                'between' => _x('The %s field must be between %d and %d', 'admin-text', 'site-reviews-notifications'),
                'criteria' => _x('The %s field requires at least one condition', 'admin-text', 'site-reviews-notifications'),
                'number' => _x('The %s field must be a number', 'admin-text', 'site-reviews-notifications'),
                'required' => _x('The %s field is required', 'admin-text', 'site-reviews-notifications'),
                'unique' => _x('The %s field must be unique', 'admin-text', 'site-reviews-notifications'),
            ],
            'schedule' => [
                '0' => _x('Send immediately', 'admin-text', 'site-reviews-notifications'),
                '1' => _x('Send after 1 day', 'admin-text', 'site-reviews-notifications'),
                '2' => _x('Send after 2 days', 'admin-text', 'site-reviews-notifications'),
                '3' => _x('Send after 3 days', 'admin-text', 'site-reviews-notifications'),
                '4' => _x('Send after 4 days', 'admin-text', 'site-reviews-notifications'),
                '5' => _x('Send after 5 days', 'admin-text', 'site-reviews-notifications'),
                '6' => _x('Send after 6 days', 'admin-text', 'site-reviews-notifications'),
                '7' => _x('Send after 1 week', 'admin-text', 'site-reviews-notifications'),
                '14' => _x('Send after 2 weeks', 'admin-text', 'site-reviews-notifications'),
                '21' => _x('Send after 3 weeks', 'admin-text', 'site-reviews-notifications'),
                '28' => _x('Send after 4 weeks', 'admin-text', 'site-reviews-notifications'),
            ],
            'validation' => $this->validation(),
        ];
        $variables['nonce'][$this->addon->slug] = wp_create_nonce($this->addon->slug);
        return $variables;
    }

    /**
     * @return array
     * @filter site-reviews/settings/sanitize
     */
    public function filterSettingsCallback(array $settings, array $input)
    {
        $path = 'settings.addons.'.$this->addon->slug;
        $options = Arr::get($settings, $path);
        $options = glsr(SettingsDefaults::class)->restrict($options); // sanitize
        array_walk($options['notifications'], function (&$notification) {
            $notification = glsr(NotificationDefaults::class)->restrict($notification);
        });
        update_option(Str::snakeCase(Application::ID), $options['notifications']); // data is slashed
        unset($options['notifications']); // do not save the notifications in the settings
        $settings = Arr::set($settings, $path, $options);
        return $settings;
    }

    /**
     * @return void
     * @action site-reviews/route/ajax/notifications
     */
    public function notificationsAjax(Request $request)
    {
        $emails = glsr(OptionManager::class)->getWP(Str::snakeCase(Application::ID), [], 'array');
        wp_send_json_success($emails);
    }

    /**
     * @return void
     * @action site-reviews/review/created
     */
    public function queueAfterCreated(Review $review, CreateReview $command)
    {
        if (defined('WP_IMPORTING')) {
            return;
        }
        $review = glsr_get_review($review->ID);
        if ($review->isValid()) {
            glsr(Notifications::class)->queue($review);
        }
    }

    /**
     * @param string $response
     * @return void
     * @action site-reviews/review/responded
     */
    public function queueAfterResponded(Review $review, $response)
    {
        if (defined('WP_IMPORTING')) {
            return;
        }
        $review = glsr_get_review($review->ID);
        if ($review->isValid()) {
            $review->set('response', $response);
            glsr(Notifications::class)->queue($review);
        }
    }

    /**
     * @param string $old
     * @param string $new
     * @return void
     * @action site-reviews/review/approved
     * @action site-reviews/review/unapproved
     */
    public function queueAfterStatusChange(Review $review, $old, $new)
    {
        if (defined('WP_IMPORTING')) {
            return;
        }
        if (!in_array($old, ['pending', 'publish'])) {
            return;
        }
        $review = glsr_get_review($review->ID);
        if ($review->isValid()) {
            glsr(Notifications::class)->queue($review, [
                'status' => $new,
            ]);
        }
    }

    /**
     * @param string $rows
     * @return void
     * @action site-reviews/addon/settings/$this->addon->slug
     */
    public function renderSettings($rows)
    {
        glsr(Template::class)->render('views/settings', [
            'context' => [
                'dataKey' => sprintf('%s[settings][addons][%s][notifications]', OptionManager::databaseKey(), $this->addon->slug),
                'rows' => $rows,
                'title' => $this->addon->name,
            ],
        ]);
    }

    /**
     * @return void
     * @action admin_footer-site-review_page_glsr-settings
     */
    public function renderTemplates()
    {
        $html = glsr(Template::class)->build('templates/emails/default');
        $dom = new \DOMDocument();
        $dom->loadHTML($html);
        $body = $dom->getElementsByTagName('body')->item(0);
        $mock = new \DOMDocument();
        foreach ($body->childNodes as $child) {
            $mock->appendChild($mock->importNode($child, true));
        }
        $preview = $mock->saveHTML();
        $preview = preg_replace('/\s{2,}+/', '', $preview);
        $preview = preg_replace('/\{(\w+)\}/', '<%= $1 %>', $preview);
        $colors = [
            'background_color' => $this->addon->option('background_color'),
            'body_background_color' => $this->addon->option('body_background_color'),
            'body_link_color' => $this->addon->option('body_link_color'),
            'body_text_color' => $this->addon->option('body_text_color'),
            'brand_color' => $this->addon->option('brand_color'),
            'footer_text_color' => '',
            'header_text_color' => '',
        ];
        $color = Color::new($colors['background_color']);
        if (!is_wp_error($color)) {
            if ($color->isLight()) {
                $colors['footer_text_color'] = (string) $color->mix('#000', .25)->toHex();
            } else {
                $colors['footer_text_color'] = (string) $color->mix('#fff', .75)->toHex();
            }
        }
        $color = Color::new($colors['brand_color']);
        if (!is_wp_error($color)) {
            if ($color->isLight()) {
                $colors['header_text_color'] = (string) $color->mix('#000', .15)->toHex();
            } else {
                $colors['header_text_color'] = (string) $color->mix('#fff', .85)->toHex();
            }
        }
        $context = array_map('esc_attr', $colors);
        $style = glsr(Template::class)->build('templates/styles/default', compact('context'));
        $style = preg_replace('/\{\{([\w ]+)\}\}/', '<%= $1 %>', $style);
        glsr(Template::class)->render('views/templates', [
            'addon' => $this->addon,
            'preview' => $preview,
            'style' => $style,
            'tags' => glsr(TemplateTags::class)->tagList([
                'exclude' => ['admin_email'],
            ]),
        ]);
    }

    /**
     * @param string $notificationUid
     * @param int $reviewId
     * @return void
     * @action site-reviews-notifications/queue/notification
     */
    public function sendNotification($notificationUid, $reviewId)
    {
        $review = glsr_get_review($reviewId);
        if ($review->isValid()) {
            glsr(Notifications::class)->send($review, $notificationUid);
        }
    }

    /**
     * @return void
     */
    protected function setAddon()
    {
        $this->addon = glsr(Application::class);
    }

    /**
     * @param string $rule
     * @return array
     */
    protected function parseRule($rule)
    {
        $parameters = true;
        if (Str::contains($rule, ':')) {
            list($rule, $parameter) = explode(':', $rule, 2);
            $parameters = str_getcsv($parameter);
        }
        return [$rule, $parameters];
    }

    /**
     * @return array
     */
    protected function parseRules()
    {
        $rules = $this->rules();
        foreach ($rules as $key => $rule) {
            if (is_string($rule)) {
                $rule = explode('|', $rule);
            }
            $rules[$key] = array_filter($rule);
        }
        return $rules;
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'conditions' => 'criteria',
            'enabled' => '',
            'header' => '',
            'message' => 'required',
            'recipients' => 'required',
            'schedule' => '',
            'subject' => 'required',
        ];
    }

    /**
     * @return array
     */
    public function validation()
    {
        $parsed = [];
        foreach ($this->parseRules() as $attribute => $rules) {
            $parsed[$attribute] = [];
            foreach ($rules as $rule) {
                list($rule, $parameters) = $this->parseRule($rule);
                $parsed[$attribute][$rule] = $parameters;
            }
        }
        return $parsed;
    }
}

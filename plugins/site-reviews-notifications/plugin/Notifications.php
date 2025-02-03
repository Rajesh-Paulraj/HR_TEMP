<?php

namespace GeminiLabs\SiteReviews\Addon\Notifications;

use GeminiLabs\SiteReviews\Addon\Notifications\Defaults\SettingsDefaults;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Html\TemplateTags;
use GeminiLabs\SiteReviews\Modules\Sanitizer;
use GeminiLabs\SiteReviews\Review;

class Notifications
{
    /**
     * @var \GeminiLabs\SiteReviews\Arguments
     */
    public $data;

    /**
     * @var Review
     */
    public $review;

    /**
     * @var array
     */
    public $notifications;

    /**
     * @var \GeminiLabs\SiteReviews\Arguments
     */
    public $settings;

    /**
     * @return void
     */
    public function __construct()
    {
        $this->notifications = glsr(Application::ID)->notifications();
        $this->settings = glsr(Application::ID)->options(SettingsDefaults::class);
    }

    /**
     * @return void
     */
    public function queue(Review $review, array $data = [])
    {
        $this->data = glsr()->args($data);
        $this->review = $review;
        foreach ($this->notifications as $notification) {
            if (!$this->isEnabled($notification) || !$this->isValid($notification)) {
                continue;
            }
            $interval = $this->interval($notification);
            glsr(Queue::class)->once(time() + $interval, 'queue/notification', wp_parse_args($data, [
                'notification_uid' => $notification['uid'],
                'review_id' => $review->ID,
            ]));
        }
    }

    /**
     * @return void
     */
    public function send(Review $review, $notificationUid)
    {
        $this->review = $review;
        $notification = $this->getNotification($notificationUid);
        if (empty($notification)) {
            return;
        }
        $notification = glsr(Application::ID)->filterArray('notification', $notification, $review);
        $recipients = $this->recipients($notification);
        if (empty($recipients)) {
            return;
        }
        $templateTags = $this->interpolateTags($notification);
        $data = [
            'args' => [],
            'review' => $this->review,
        ];
        $email = [
            'to' => $recipients,
            'from' => $this->getFromEmail(),
            'reply-to' => $this->getReplyToEmail(),
            'style' => 'default',
            'subject' => $templateTags['subject'],
            'template' => 'default',
            'template-tags' => $templateTags,
        ];
        glsr(Email::class)->compose($email, $data)->send();
    }

    /**
     * @param string $value
     * @return array
     */
    protected function criteria($value)
    {
        $conditions = explode('|', $value);
        $condition = Str::restrictTo(['all', 'always', 'any'], array_shift($conditions));
        $conditions = array_map(function ($values) {
            $parts = explode(':', $values);
            if (3 == count($parts)) {
                return array_combine(['field', 'operator', 'value'], $parts);
            }
            return [];
        }, $conditions);
        return [
            'condition' => $condition,
            'conditions' => array_filter($conditions),
        ];
    }

    /**
     * @return string
     */
    protected function getFromEmail()
    {
        $email = glsr(Template::class)->interpolate($this->settings->from_email, 'notification/from_email', [
            'context' => glsr(TemplateTags::class)->tags($this->review, [
                'include' => ['admin_email'],
            ]),
        ]);
        if (!empty($email) && !empty($this->settings->from_name)) {
            $name = glsr(Template::class)->interpolate($this->settings->from_name, 'notification/from_name', [
                'context' => glsr(TemplateTags::class)->tags($this->review, [
                    'include' => ['site_title'],
                ]),
            ]);
            $email = sprintf('%s <%s>', $name, $email);
        }
        return $email;
    }

    /**
     * @param string $uid
     * @return array|false
     */
    protected function getNotification($uid)
    {
        if ($notification = Arr::searchByKey($uid, $this->notifications, 'uid')) {
            return $notification;
        }
        return false;
    }

    /**
     * @return string
     */
    protected function getReplyToEmail()
    {
        return glsr(Template::class)->interpolate($this->settings->reply_to_email, 'notification/reply_to_email', [
            'context' => glsr(TemplateTags::class)->tags($this->review, [
                'include' => ['admin_email'],
            ]),
        ]);
    }

    /**
     * @return array
     */
    protected function getUserEmails(array $userIds)
    {
        if (empty($userIds)) {
            return [];
        }
        $users = get_users(['fields' => ['user_email'], 'include' => $userIds]);
        return wp_list_pluck($users, 'user_email');
    }

    /**
     * @return array
     */
    protected function interpolateTags(array $notification)
    {
        $footer = glsr(Template::class)->interpolate($this->settings->footer_text, 'notification/footer', [
            'context' => glsr(TemplateTags::class)->tags($this->review, [
                'include' => ['review_ip', 'review_link', 'site_title', 'site_url'],
            ]),
        ]);
        $subject = glsr(Template::class)->interpolate($notification['subject'], 'notification/subject', [
            'context' => glsr(TemplateTags::class)->tags($this->review, [
                'exclude' => ['admin_email'],
            ]),
        ]);
        $heading = glsr(Template::class)->interpolate($notification['heading'], 'notification/heading', [
            'context' => glsr(TemplateTags::class)->tags($this->review, [
                'exclude' => ['admin_email'],
            ]),
        ]);
        $message = glsr(Template::class)->interpolate($notification['message'], 'notification/message', [
            'context' => glsr(TemplateTags::class)->tags($this->review, [
                'exclude' => ['admin_email'],
            ]),
        ]);
        $message = strip_shortcodes($message);
        $message = wptexturize($message);
        $message = wpautop($message);
        $message = str_replace('&lt;&gt; ', '', $message);
        $message = str_replace(']]>', ']]&gt;', $message);
        $image = glsr(Builder::class)->img(['alt' => '', 'src' => $this->settings->header_image]);
        return compact('footer', 'heading', 'image', 'message', 'subject');
    }

    /**
     * @return int
     */
    protected function interval(array $notification)
    {
        if (!empty($notification['schedule'])) {
            return $notification['schedule'] * DAY_IN_SECONDS;
        }
        return 0;
    }

    /**
     * @return bool
     */
    protected function isEnabled(array $notification)
    {
        return Cast::toBool($notification['enabled']);
    }

    /**
     * @return bool
     */
    protected function isValid(array $notification)
    {
        $criteria = $this->criteria($notification['conditions']);
        if (empty($notification['uid']) || empty($criteria['condition'])) {
            return false;
        }
        if ('always' === $criteria['condition']) {
            return true;
        }
        $passed = 0;
        foreach ($criteria['conditions'] as $condition) {
            $method = Helper::buildMethodName($condition['field'], 'validate');
            if (method_exists($this, $method)) {
                $result = call_user_func([$this, $method], $condition['value'], $condition['operator']);
            } else {
                $result = glsr(Application::class)->filterBool('notification/condition', false, $condition, $this->review);
            }
            $passed += Cast::toInt($result);
        }
        if ('any' === $criteria['condition']) {
            return $passed > 0;
        }
        return $passed === count($criteria['conditions']);
    }

    /**
     * @return array
     */
    protected function recipients(array $notification)
    {
        $recipients = [];
        foreach ($notification['recipients'] as $key) {
            if ('admin' === $key) {
                $recipients[] = get_bloginfo('admin_email');
                continue;
            }
            if ('assigned_post_author' === $key) {
                $posts = $this->review->assignedPosts();
                $emails = $this->getUserEmails(wp_list_pluck($posts, 'post_author'));
                $recipients = array_merge($recipients, $emails);
                continue;
            }
            if ('assigned_user' === $key) {
                $emails = $this->getUserEmails($this->review->assigned_users);
                $recipients = array_merge($recipients, $emails);
                continue;
            }
            if ('reviewer' === $key) {
                $recipients[] = $this->review->email;
                continue;
            }
            $recipients[] = $key;
        }
        $recipients = array_map([glsr(Sanitizer::class), 'sanitizeEmail'], $recipients);
        return Arr::reindex(Arr::unique($recipients));
    }

    /**
     * @param string|int $value
     * @param string $operator
     * @return bool
     */
    protected function validateAssignedPost($value, $operator)
    {
        switch ($operator) {
            case 'contains':
                return 0 === count(array_diff(Arr::uniqueInt($value), $this->review->assigned_posts));
            case 'equals':
                return Arr::compare(Arr::uniqueInt($value), $this->review->assigned_posts);
            case 'not':
                return 0 === count(array_intersect(Arr::uniqueInt($value), $this->review->assigned_posts));
        }
        return false;
    }

    /**
     * @param string|int $value
     * @param string $operator
     * @return bool
     */
    protected function validateAssignedPostAuthor($value, $operator)
    {
        $userIds = wp_list_pluck($this->review->assignedPosts(), 'post_author');
        switch ($operator) {
            case 'contains':
                return 0 === count(array_diff(Arr::uniqueInt($value), $userIds));
            case 'equals':
                return Arr::compare(Arr::uniqueInt($value), $userIds);
            case 'not':
                return 0 === count(array_intersect(Arr::uniqueInt($value), $userIds));
        }
        return false;
    }

    /**
     * @param string|int $value
     * @param string $operator
     * @return bool
     */
    protected function validateAssignedUser($value, $operator)
    {
        switch ($operator) {
            case 'contains':
                return 0 === count(array_diff(Arr::uniqueInt($value), $this->review->assigned_users));
            case 'equals':
                return Arr::compare(Arr::uniqueInt($value), $this->review->assigned_users);
            case 'not':
                return 0 === count(array_intersect(Arr::uniqueInt($value), $this->review->assigned_users));
        }
        return false;
    }

    /**
     * @param string|int $value
     * @param string $operator
     * @return bool
     */
    protected function validateAssignedTerm($value, $operator)
    {
        switch ($operator) {
            case 'contains':
                return 0 === count(array_diff(Arr::uniqueInt($value), $this->review->assigned_terms));
            case 'equals':
                return Arr::compare(Arr::uniqueInt($value), $this->review->assigned_terms);
            case 'not':
                return 0 === count(array_intersect(Arr::uniqueInt($value), $this->review->assigned_terms));
        }
        return false;
    }

    /**
     * @param string|int $value
     * @param string $operator
     * @return bool
     */
    protected function validateRating($value, $operator)
    {
        if (!is_numeric($value)) {
            $value = -1;
        }
        if ('contains' === $operator) {
            return in_array($this->review->rating, Arr::uniqueInt($value));
        }
        if ('not' === $operator) {
            return !in_array($this->review->rating, Arr::uniqueInt($value));
        }
        switch ($operator) {
            case 'equals':
                return $this->review->rating === Cast::toInt($value);
            case 'greater':
                return $this->review->rating > Cast::toInt($value);
            case 'less':
                return $this->review->rating < Cast::toInt($value);
        }
        return false;
    }

    /**
     * @param string|int $value
     * @param string $operator
     * @return bool
     */
    protected function validateReview($value, $operator)
    {
        if ('approved' === $value && 'publish' === $this->data->status) {
            return $this->review->is_approved;
        }
        if ('unapproved' === $value && 'pending' === $this->data->status) {
            return !$this->review->is_approved;
        }
        if ('responded' === $value) {
            $hasResponse = !empty($this->review->response);
            $hasResponseBy = !empty(glsr(Database::class)->meta($this->review->ID, 'response_by'));
            return $hasResponse && !$hasResponseBy;
        }
        return false;
    }
}

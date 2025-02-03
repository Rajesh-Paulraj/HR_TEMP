<?php

namespace GeminiLabs\SiteReviews\Addon\Notifications\Defaults;

use GeminiLabs\SiteReviews\Addon\Notifications\Application;
use GeminiLabs\SiteReviews\Addon\Notifications\Template;

class EmailDefaults extends \GeminiLabs\SiteReviews\Defaults\EmailDefaults
{
    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'after' => '',
            'attachments' => [],
            'bcc' => '',
            'before' => '',
            'cc' => '',
            'from' => '',
            'message' => '',
            'reply-to' => '',
            'style' => 'default',
            'subject' => '',
            'template' => 'default',
            'template-tags' => [],
            'to' => '',
        ];
    }

    /**
     * @return string
     */
    protected function getFromEmail()
    {
        $fromEmail = glsr(Application::ID)->option('from_email');
        $email = glsr(Template::class)->interpolate($fromEmail, 'notification/from_email', [
            'context' => ['admin_email' => get_bloginfo('admin_email')],
        ]);
        $fromName = glsr(Application::ID)->option('from_name');
        if (!empty($email) && !empty($fromName)) {
            $name = glsr(Template::class)->interpolate($fromName, 'notification/from_name', [
                'context' => ['admin_email' => get_bloginfo('admin_email')],
            ]);
            $email = sprintf('%s <%s>', $name, $email);
        }
        return $email;
    }

    /**
     * @return string
     */
    protected function getReplyToEmail()
    {
        $replyToEmail = glsr(Application::ID)->option('reply_to_email');
        return glsr(Template::class)->interpolate($replyToEmail, 'notification/reply_to_email', [
            'context' => ['admin_email' => get_bloginfo('admin_email')],
        ]);
    }

    /**
     * Normalize provided values, this always runs first.
     * @return array
     */
    protected function normalize(array $values = [])
    {
        if (empty($values['from'])) {
            $values['from'] = $this->getFromEmail();
        }
        if (empty($values['reply-to'])) {
            $values['reply-to'] = $this->getReplyToEmail();
        }
        return $values;
    }
}

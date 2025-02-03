<?php

namespace GeminiLabs\SiteReviews\Addon\Notifications\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class NotificationDefaults extends Defaults
{
    /**
     * @var array
     */
    public $sanitize = [
        'conditions' => 'text',
        'enabled' => 'bool',
        'heading' => 'text-html',
        'message' => 'text-post',
        'recipients' => 'array',
        'schedule' => 'int',
        'subject' => 'text',
        'uid' => 'text',
    ];

    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'conditions' => 'always',
            'enabled' => false,
            'heading' => '',
            'message' => '',
            'recipients' => [],
            'schedule' => 0,
            'subject' => '',
            'uid' => '',
        ];
    }
}

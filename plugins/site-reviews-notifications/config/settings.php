<?php

$addon = glsr('Addon\Notifications\Application');

return [
    'settings.addons.'.$addon->slug.'.from_name' => [
        'class' => 'regular-text',
        'default' => '{site_title}',
        'label' => _x('From Name', 'setting label (admin-text)', 'site-reviews-notifications'),
        'tooltip' => sprintf(_x('The "From" name (the display name, also known as the email Sender name) tells your recipients who sent them the message. The available placeholder tags are: %s', 'admin-text', 'site-reviews-notifications'), 
            glsr('Modules\Html\TemplateTags')->tagList(['include' => ['site_title']])
        ),
        'type' => 'text',
    ],
    'settings.addons.'.$addon->slug.'.from_email' => [
        'class' => 'regular-text',
        'default' => '{admin_email}',
        'label' => _x('From Email', 'setting label (admin-text)', 'site-reviews-notifications'),
        'tooltip' => sprintf(_x('This is the email address that notifications will be sent from. The available placeholder tags are: %s', 'admin-text', 'site-reviews-notifications'),
            glsr('Modules\Html\TemplateTags')->tagList(['include' => ['admin_email']])
        ),
        'type' => 'text',
    ],
    'settings.addons.'.$addon->slug.'.reply_to_email' => [
        'class' => 'regular-text',
        'default' => '{admin_email}',
        'label' => _x('Reply-To Email', 'setting label (admin-text)', 'site-reviews-notifications'),
        'tooltip' => sprintf(_x('This is the email address that you want reply messages sent to, this can be different from the "From" email. The available placeholder tags are: %s', 'admin-text', 'site-reviews-notifications'), 
            glsr('Modules\Html\TemplateTags')->tagList(['include' => ['admin_email']])
        ),
        'type' => 'text',
    ],
    'settings.addons.'.$addon->slug.'.header_image' => [
        'data-name' => 'header_image',
        'class' => 'regular-text',
        'default' => '',
        'label' => _x('Header Image URL', 'setting label (admin-text)', 'site-reviews-notifications'),
        'tooltip' => _x('This image will be shown above the email, usually you will want this to be your business or website logo.', 'setting description (admin-text)', 'site-reviews-notifications'),
        'type' => 'url',
    ],
    'settings.addons.'.$addon->slug.'.footer_text' => [
        'data-name' => 'footer_text',
        'class' => 'regular-text',
        'default' => 'Powered by <a href="{site_url}">{site_title}</a>',
        'label' => _x('Footer Text', 'setting label (admin-text)', 'site-reviews-notifications'),
        'tooltip' => sprintf(_x('This text is shown at the bottom of the email. The available placeholder tags are: %s', 'admin-text', 'site-reviews-notifications'),
            glsr('Modules\Html\TemplateTags')->tagList(['include' => ['review_ip', 'review_link', 'site_title', 'site_url']])
        ),
        'type' => 'textarea',
    ],
    'settings.addons.'.$addon->slug.'.brand_color' => [
        'data-name' => 'brand_color',
        'default' => '#FFCF02',
        'label' => _x('Brand Colour', 'setting label (admin-text)', 'site-reviews-notifications'),
        'tooltip' => _x('This colour is used for the header background, links, etc.', 'setting description (admin-text)', 'site-reviews-notifications'),
        'type' => 'colorpicker',
    ],
    'settings.addons.'.$addon->slug.'.background_color' => [
        'data-name' => 'background_color',
        'default' => '#F6F7F7',
        'label' => _x('Background Colour', 'setting label (admin-text)', 'site-reviews-notifications'),
        'tooltip' => _x('This colour is used for the background of the email.', 'setting description (admin-text)', 'site-reviews-notifications'),
        'type' => 'colorpicker',
    ],
    'settings.addons.'.$addon->slug.'.body_background_color' => [
        'data-name' => 'body_background_color',
        'default' => '#FFFFFF',
        'label' => _x('Body Background Colour', 'setting label (admin-text)', 'site-reviews-notifications'),
        'tooltip' => _x('This colour is used for the backgound of the message body.', 'setting description (admin-text)', 'site-reviews-notifications'),
        'type' => 'colorpicker',
    ],
    'settings.addons.'.$addon->slug.'.body_link_color' => [
        'data-name' => 'body_link_color',
        'default' => '#ff5202',
        'label' => _x('Body Link Colour', 'setting label (admin-text)', 'site-reviews-notifications'),
        'tooltip' => _x('This colour is used for links inside the message body.', 'setting description (admin-text)', 'site-reviews-notifications'),
        'type' => 'colorpicker',
    ],
    'settings.addons.'.$addon->slug.'.body_text_color' => [
        'data-name' => 'body_text_color',
        'default' => '#333333',
        'label' => _x('Body Text Colour', 'setting label (admin-text)', 'site-reviews-notifications'),
        'tooltip' => _x('This colour is used for the text of the message body.', 'setting description (admin-text)', 'site-reviews-notifications'),
        'type' => 'colorpicker',
    ],
];

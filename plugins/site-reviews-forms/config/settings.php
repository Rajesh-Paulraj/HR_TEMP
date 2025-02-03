<?php

$addon = glsr('Addon\Forms\Application');

return [
    'settings.addons.'.$addon->slug.'.dropdown_library' => [
        'class' => 'regular-text',
        'default' => '',
        'label' => _x('Select Boxes', 'setting label (admin-text)', 'site-reviews-forms'),
        'options' => [
            '' => _x('Use the native Select Boxes', 'setting option (admin-text)', 'site-reviews-forms'),
            'choices.js' => _x('Use Choices.js', 'setting option (admin-text)', 'site-reviews-forms'),
        ],
        'tooltip' => _x('Would you like to use a javascript library to style the Select boxes?', 'setting tooltip (admin-text)', 'site-reviews-forms'),
        'type' => 'select',
    ],
    'settings.addons.'.$addon->slug.'.dropdown_assets' => [
        'default' => 'yes',
        'depends_on' => [
            'settings.addons.'.$addon->slug.'.dropdown_library' => ['choices.js'],
        ],
        'label' => _x('Load Library Assets?', 'setting label (admin-text)', 'site-reviews-forms'),
        'tooltip' => _x('Would you like to load the javascript and CSS of the selected library? If your theme is already using the library, you may want to disable this.', 'setting tooltip (admin-text)', 'site-reviews-forms'),
        'type' => 'yesno',
    ],
];

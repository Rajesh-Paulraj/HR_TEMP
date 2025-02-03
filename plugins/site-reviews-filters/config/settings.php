<?php

$addon = glsr('Addon\Filters\Application');

return [
    'settings.addons.'.$addon->slug.'.search' => [
        'class' => 'regular-text',
        'default' => '',
        'label' => _x('Search Behaviour', 'setting label (admin-text)', 'site-reviews-filters'),
        'options' => [
            '' => _x('Search Content and Titles', 'setting option (admin-text)', 'site-reviews-filters'),
            'content' => _x('Search Only Content', 'setting option (admin-text)', 'site-reviews-filters'),
            'title' => _x('Search Only Titles', 'setting option (admin-text)', 'site-reviews-filters'),
        ],
        'tooltip' => _x('This restricts the review fields which are searched', 'setting description (admin-text)', 'site-reviews-filters'),
        'type' => 'select',
    ],
];

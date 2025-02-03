<?php

$addon = glsr('Addon\Authors\Application');
$roles = array_map('translate_user_role', wp_list_pluck(get_editable_roles(), 'name'));
unset($roles['administrator']); // admins are always allowed to edit reviews on the front-end

return [
    'settings.addons.authors.roles' => [
        'class' => 'regular-text',
        'default' => ['author', 'contributor', 'editor'],
        'label' => _x('Review Editor Roles', 'admin-text', 'site-reviews-authors'),
        'options' => $roles,
        'sanitizer' => 'array-string',
        'tooltip' => _x('Choose which user roles are allowed to edit reviews on the front-end.', 'admin-text', 'site-reviews-authors'),
        'type' => 'checkbox',
    ],
];

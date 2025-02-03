<?php

$addon = glsr('Addon\Images\Application');

return [
    'settings.addons.'.$addon->slug.'.accepted_files' => [
        'class' => 'regular-text',
        'default' => '',
        'label' => _x('Accepted Image Files', 'setting label (admin-text)', 'site-reviews-images'),
        'options' => [
            '' => _x('JPEG and PNG files', 'setting option (admin-text)', 'site-reviews-images'),
            'image/jpeg' => _x('Only JPEG files', 'setting option (admin-text)', 'site-reviews-images'),
            'image/png' => _x('Only PNG files', 'setting option (admin-text)', 'site-reviews-images'),
        ],
        'tooltip' => _x('Restrict uploaded images to specific image file types', 'setting description (admin-text)', 'site-reviews-images'),
        'type' => 'select',
    ],
    'settings.addons.'.$addon->slug.'.deletion' => [
        'default' => 'no',
        'label' => _x('Delete Images with Review', 'setting label (admin-text)', 'site-reviews-images'),
        'tooltip' => _x('Delete attached images when a review is permanently deleted', 'setting description (admin-text)', 'site-reviews-images'),
        'type' => 'yes_no',
    ],
    'settings.addons.'.$addon->slug.'.disable_modal' => [
        'default' => 'no',
        'label' => _x('Disable Image Modal', 'setting label (admin-text)', 'site-reviews-images'),
        'tooltip' => _x('Disable the image modal used for viewing images. For example, if you are using the Elementor Lightbox to view images you may wish to disable the modal.', 'setting description (admin-text)', 'site-reviews-images'),
        'type' => 'yes_no',
    ],
    'settings.addons.'.$addon->slug.'.modal' => [
        'default' => 'modal',
        'depends_on' => [
            'settings.addons.'.$addon->slug.'.disable_modal' => 'no',
        ],
        'label' => _x('Display Images Using', 'setting label (admin-text)', 'site-reviews-images'),
        'options' => [
            'lightbox' => _x('Fullscreen Lightbox', 'admin-text', 'site-reviews-images'),
            'modal' => _x('Single Modal', 'admin-text', 'site-reviews-images'),
        ],
        'tooltip' => _x('The Single Modal will display individual images in a popup. The Fullscreen Lightbox will display the images using the full height/width of the browser and allow you to navigate between each image.', 'setting description (admin-text)', 'site-reviews-images'),
        'type' => 'select',
    ],
    'settings.addons.'.$addon->slug.'.max_file_size' => [
        'after' => _x('MB', 'abbreviation of megabytes (admin-text)', 'site-reviews-images'),
        'default' => 5,
        'label' => _x('Maximum File Size', 'setting label (admin-text)', 'site-reviews-images'),
        'tooltip' => _x('The maximum file size of images that can be uploaded', 'setting description (admin-text)', 'site-reviews-images'),
        'type' => 'number',
    ],
    'settings.addons.'.$addon->slug.'.max_files' => [
        'after' => _x('images', 'maximum number of (admin-text)', 'site-reviews-images'),
        'default' => 5,
        'label' => _x('Maximum Images', 'setting label (admin-text)', 'site-reviews-images'),
        'tooltip' => _x('The maximum number of images that can be added to a review', 'setting description (admin-text)', 'site-reviews-images'),
        'type' => 'number',
    ],
];

<?php

$addon = glsr('Addon\Themes\Application');

return [
    'settings.addons.'.$addon->slug.'.swiper_library' => [
        'class' => 'regular-text',
        'default' => 'splide',
        'label' => _x('Swiper Library', 'setting label (admin-text)', 'site-reviews-themes'),
        'options' => [
            'splide' => _x('Use Splide (default)', 'setting option (admin-text)', 'site-reviews-themes'),
            'swiper' => _x('Use Swiper', 'setting option (admin-text)', 'site-reviews-themes'),
        ],
        'tooltip' => sprintf('%s<ul><li>%s</li><li>%s</li></ul>',
            _x('Select the swiper library that you would like to use for the carousel.', 'setting tooltip (admin-text)', 'site-reviews-themes'),
            _x('<a href="https://splidejs.com" target="_blank">Splide</a> is a lightweight, flexible and accessible slider; it is also used by the Review Images add-on.', 'setting tooltip (admin-text)', 'site-reviews-themes'),
            _x('<a href="https://swiperjs.com" target="_blank">Swiper</a> is a more popular slider, but not as lightweight as Splide.', 'setting tooltip (admin-text)', 'site-reviews-themes')
        ),
        'type' => 'select',
    ],
    'settings.addons.'.$addon->slug.'.swiper_assets' => [
        'default' => 'yes',
        'label' => _x('Load Swiper Assets?', 'setting label (admin-text)', 'site-reviews-themes'),
        'tooltip' => _x('Would you like to load the javascript and CSS of the selected library? If your theme is already using the library, you may want to disable this.', 'setting tooltip (admin-text)', 'site-reviews-themes'),
        'type' => 'yesno',
    ],
];

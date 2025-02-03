<?php defined('ABSPATH') || die; ?>

<div class="glsr-reviews-wrap">
    <div class="gl-swiper-container">
        <div class="gl-swiper gl-carousel" data-options='{{ options }}'>
            <div class="gl-swiper-wrapper {{ class }}" data-reviews>
                {{ reviews }}
            </div>
            <div class="gl-swiper-pagination"></div>
        </div>
    </div>
    {{ pagination }}
</div>

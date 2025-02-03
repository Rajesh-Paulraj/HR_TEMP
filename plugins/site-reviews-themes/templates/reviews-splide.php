<?php defined('ABSPATH') || die; ?>

<div class="glsr-reviews-wrap">
    <div class="gl-swiper-container">
        <div class="splide gl-splide gl-carousel" data-splide='{{ options }}'>
            <div class="splide__track">
                <div class="splide__list gl-swiper-wrapper {{ class }}" data-reviews>
                    {{ reviews }}
                </div>
            </div>
            <div class="splide__pagination gl-swiper-pagination"></div>
        </div>
    </div>
    {{ pagination }}
</div>

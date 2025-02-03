<?php

namespace GeminiLabs\SiteReviews\Addon\Images\Shortcodes;

use GeminiLabs\SiteReviews\Addon\Images\Application;
use GeminiLabs\SiteReviews\Addon\Images\Database\Query;
use GeminiLabs\SiteReviews\Addon\Images\Defaults\SiteReviewsImagesDefaults;
use GeminiLabs\SiteReviews\Addon\Images\Template;
use GeminiLabs\SiteReviews\Modules\Html\Attributes;
use GeminiLabs\SiteReviews\Shortcodes\Shortcode;

class SiteReviewsImagesShortcode extends Shortcode
{
    /**
     * @var array
     */
    public $args;

    /**
     * @return string|void
     */
    public function buildTemplate(array $args = [])
    {
        glsr()->store('use_swiper', true); // this loads the splidejs script
        $this->args = glsr(SiteReviewsImagesDefaults::class)->unguardedMerge($args);
        $results = glsr(Query::class)->reviewImages($this->args);
        $this->debug(compact('results'));
        $images = $results['results'];
        unset($results['results']);
        return glsr(Template::class)->build('templates/review-images', [
            'args' => $this->args,
            'images' => $images,
            'context' => [
                'class' => '',
                'images' => $this->buildTemplateTagImages($images),
                'link' => $this->buildTemplateTagLink($results),
            ],
            'results' => $results,
        ]);
    }

    /**
     * @return string
     */
    protected function buildTemplateTagImages(array $images)
    {
        if (empty($images)) {
            return '';
        }
        $renderedImages = array_reduce($images, function ($carry, $image) {
            $context = $image->toArray(['large', 'medium']);
            $rendered = glsr(Template::class)->build('templates/gallery/image', compact('context'));
            return $carry.$rendered;
        });
        return glsr(Template::class)->build('templates/gallery/images', [
            'context' => [
                'images' => $renderedImages,
            ],
        ]);
    }

    /**
     * @return string
     */
    protected function buildTemplateTagLink(array $results)
    {
        if (empty($results['total'])) {
            return '';
        }
        return glsr(Template::class)->build('templates/gallery/link', [
            'context' => [
                'text' => __('See all images ', 'site-reviews-images'),
            ],
        ]);
    }

    /**
     * @return array
     */
    protected function hideOptions() {
        return [
            'caption' => _x('Hide image captions', 'admin-text', 'site-reviews-images'),
            'review' => _x('Hide image reviews', 'admin-text', 'site-reviews-images'),
        ];
    }
}

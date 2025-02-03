<?php

namespace GeminiLabs\SiteReviews\Addon\Forms\Fields;

class ReviewImages extends Field
{
    public $name = 'images';
    public $options = ['label', 'required', 'tag_label', 'type'];
    public $tag = 'images';

    /**
     * {@inheritdoc}
     */
    public function isActive()
    {
        return class_exists('GeminiLabs\SiteReviews\Addon\Images\Fields\Dropzone');
    }

    /**
     * {@inheritdoc}
     */
    protected function handle()
    {
        return _x('Review: Images', 'admin-text', 'site-reviews-forms');
    }

    /**
     * {@inheritdoc}
     */
    protected function type()
    {
        return 'review_dropzone';
    }

    /**
     * {@inheritdoc}
     */
    protected function validation()
    {
        return [
            'name' => 'required|slug|unique',
            'type' => 'unique',
        ];
    }
}

<?php

namespace GeminiLabs\SiteReviews\Addon\Authors\Tags;

use GeminiLabs\SiteReviews\Addon\Authors\Application;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Html\Tags\ReviewTag;

class ReviewEditUrlTag extends ReviewTag
{
    /**
     * {@inheritdoc}
     */
    protected function handle($value = null)
    {
        if ($this->isHidden()) {
            return;
        }
        if (!glsr(Application::class)->canEditOnFrontend($this->review)) {
            return;
        }
        $value = glsr(Builder::class)->a([
            'data-id' => $this->review->ID,
            'data-glsr-trigger' => 'glsr-modal-form',
            'href' => $value,
            'text' => _x('Edit', 'admin-text', 'site-reviews-authors'),
        ]);
        return $this->wrap($value);
    }
}

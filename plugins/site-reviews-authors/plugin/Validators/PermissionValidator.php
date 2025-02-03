<?php

namespace GeminiLabs\SiteReviews\Addon\Authors\Validators;

use GeminiLabs\SiteReviews\Addon\Authors\Application;
use GeminiLabs\SiteReviews\Modules\Validator\ValidatorAbstract;

class PermissionValidator extends ValidatorAbstract
{
    /**
     * @return bool
     */
    public function isValid()
    {
        $review = glsr_get_review($this->request->review_id);
        return glsr(Application::class)->canEditOnFrontend($review);
    }

    /**
     * @return void
     */
    public function performValidation()
    {
        if (!$this->isValid()) {
            $this->setErrors(__('You do not have permission to update the review.', 'site-reviews-authors'));
        }
    }
}

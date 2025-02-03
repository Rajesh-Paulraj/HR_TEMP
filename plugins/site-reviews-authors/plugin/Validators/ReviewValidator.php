<?php

namespace GeminiLabs\SiteReviews\Addon\Authors\Validators;

use GeminiLabs\SiteReviews\Modules\Validator\ValidatorAbstract;

class ReviewValidator extends ValidatorAbstract
{
    /**
     * @return bool
     */
    public function isValid()
    {
        return glsr_get_review($this->request->review_id)->isValid();
    }

    /**
     * @return void
     */
    public function performValidation()
    {
        if (!$this->isValid()) {
            $this->setErrors(
                sprintf(__('Cannot find the review to update (ID: %s).', 'site-reviews-authors'), $this->request->review_id)
            );
        }
    }
}

<?php

namespace GeminiLabs\SiteReviews\Addon\Authors;

use GeminiLabs\SiteReviews\Addons\Hooks as AddonHooks;

class Hooks extends AddonHooks
{
    /**
     * @return void
     */
    public function run()
    {
        $this->hook(Controller::class, [
            ['fetchReviewFormAjax', 'site-reviews/route/ajax/fetch-review-form'],
            ['filterGuardedCustomFields', 'site-reviews/defaults/custom-fields/guarded'],
            ['filterHideOptions', 'site-reviews/shortcode/hide-options', 10, 2],
            ['filterInlineStyles', 'site-reviews/enqueue/public/inline-styles'],
            ['filterReviewDefaults', 'site-reviews/defaults/review/defaults'],
            ['filterReviewEditUrlTag', 'site-reviews/review/tag/edit_url'],
            ['filterReviewSanitizers', 'site-reviews/defaults/review/sanitize'],
            ['filterReviewTemplate', 'site-reviews/build/template/review', 10, 2],
            ['filterSettingSanitization', 'site-reviews/settings/sanitize', 10, 2],
            ['filterThemeBuilderTemplate', 'site-reviews-themes/config/templates/template_1'],
            ['filterThemeTagDefaults', 'site-reviews-themes/defaults/tag/defaults'],
            ['filterUnguardedActions', 'site-reviews/router/admin/unguarded-actions'],
            ['filterValidation', 'site-reviews/validate/duplicate'],
            ['filterValidation', 'site-reviews/validate/review-limits'],
            ['setReviewEditUrl', 'site-reviews/get/review'],
            ['updateReviewAjax', 'site-reviews/route/ajax/update-review'],
        ]);
        parent::run();
    }

    /**
     * @return mixed
     */
    protected function addon()
    {
        return glsr(Application::class);
    }

    /**
     * @return mixed
     */
    protected function controller()
    {
        return glsr(Controller::class);
    }
}

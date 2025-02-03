<?php

namespace GeminiLabs\SiteReviews\Addon\Authors;

use GeminiLabs\SiteReviews\Addon\Authors\Commands\UpdateReview;
use GeminiLabs\SiteReviews\Addon\Authors\Tags\ReviewEditUrlTag;
use GeminiLabs\SiteReviews\Addons\Controller as AddonController;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Style;
use GeminiLabs\SiteReviews\Request;
use GeminiLabs\SiteReviews\Review;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsFormShortcode;

class Controller extends AddonController
{
    protected $addon;

    /**
     * @action site-reviews/route/ajax/fetch-review-form
     */
    public function fetchReviewFormAjax(Request $request): void
    {
        $review = glsr_get_review($request->review_id);
        if (!$review->isValid()) {
            wp_send_json_error([
                'message' => __('The review could not be found.', 'site-reviews-authors'),
            ]);
        }
        if (!$this->addon->canEditOnFrontend($review)) { // @phpstan-ignore-line
            wp_send_json_error([
                'message' => __('You do not have permission to update the review.', 'site-reviews-authors'),
            ]);
        }
        $args = [
            'form' => $request->form, // $review->form
            'theme' => $request->theme,
        ];
        $atts = glsr(SiteReviewsFormShortcode::class)->attributes($args);
        $form = new Form($review, $args);
        wp_send_json_success([
            'classes' => glsr(Style::class)->styleClasses(),
            'content' => $form->toString(),
            'footer' => '',
            'header' => sprintf(__('Editing Review', 'site-reviews-authors'), $review->ID),
            'images' => $form->images(),
            'style' => Arr::get($atts, 'style'),
        ]);
    }

    /**
     * @filter site-reviews/defaults/custom-fields/guarded
     */
    public function filterGuardedCustomFields(array $guarded): array
    {
        $guarded[] = 'edit_url';
        return Arr::unique($guarded);
    }

    /**
     * @filter site-reviews/shortcode/hide-options
     */
    public function filterHideOptions(array $options, string $shortcode): array
    {
        if ('site_reviews' === $shortcode) {
            $options['edit_url'] = _x('Hide the edit link', 'admin-text', 'site-reviews-authors');
        }
        return $options;
    }

    /**
     * @filter site-reviews/enqueue/public/inline-styles
     */
    public function filterInlineStyles(string $styles): string
    {
        $styles .= '.glsr-review-edit_url{width:100%;}';
        $styles .= '#glsr-modal-form .glsr-modal__close{height:1.5em;width:1.5em;margin:.75em 1em;}';
        $styles .= '#glsr-modal-form .glsr-modal__close::before{font-size:1.5em;}';
        $styles .= '#glsr-modal-form .glsr-modal__header{align-items:center;box-shadow:0 0 1em 0 rgba(0,0,0,0.1);display:flex;font-weight:600;padding:.75em 1.5em;z-index:1;}';
        $styles .= '#glsr-modal-form .glsr-modal__header span{font-weight:400;font-size:.75em;padding:0 .5em;}';
        return $styles;
    }

    /**
     * @filter site-reviews/defaults/review/defaults
     */
    public function filterReviewDefaults(array $defaults): array
    {
        $defaults['edit_url'] = '';
        return $defaults;
    }

    /**
     * @filter site-reviews/review/tag/edit_url
     */
    public function filterReviewEditUrlTag(): string
    {
        return ReviewEditUrlTag::class;
    }

    /**
     * @filter site-reviews/defaults/review/sanitize
     */
    public function filterReviewSanitizers(array $sanitize): array
    {
        $sanitize['edit_url'] = 'url';
        return $sanitize;
    }

    /**
     * @filter site-reviews/build/template/review
     */
    public function filterReviewTemplate(string $template, array $data = []): string
    {
        if (false === strpos($template, '{{ edit_url }}')) {
            $template = str_replace('{{ response }}', '{{ response }} {{ edit_url }}', $template);
        }
        return $template;
    }

    /**
     * @filter site-reviews/settings/sanitize
     */
    public function filterSettingSanitization(array $options, array $input): array
    {
        $key = 'settings.addons.authors.roles';
        $roles = Arr::get($input, $key, []);
        $options = Arr::set($options, $key, $roles);
        return $options;
    }

    /**
     * @filter site-reviews-themes/config/templates/template_1
     */
    public function filterThemeBuilderTemplate(array $config): array
    {
        return glsr(Application::class)->config('themes/templates/template_1');
    }

    /**
     * @filter site-reviews-themes/defaults/tag/defaults
     */
    public function filterThemeTagDefaults(array $defaults): array
    {
        $defaults['edit_url'] = 'review_edit_url';
        ksort($defaults);
        return $defaults;
    }

    /**
     * @filter site-reviews/router/admin/unguarded-actions
     */
    public function filterUnguardedActions(array $actions): array
    {
        $actions[] = 'fetch-review-form';
        return $actions;
    }

    /**
     * @filter site-reviews/validate/duplicate
     * @filter site-reviews/validate/review-limits
     */
    public function filterValidation(bool $result): bool
    {
        $input = Helper::filterInputArray(glsr()->id);
        if ('fetch-review-form' === Arr::get($input, '_action')) {
            return true;
        }
        return $result;
    }

    /**
     * @action site-reviews/get/review
     */
    public function setReviewEditUrl(Review $review): void
    {
        $review->set('edit_url', admin_url('post.php?post='.$review->ID.'&action=edit'));
    }

    /**
     * @action site-reviews/route/ajax/update-review
     */
    public function updateReviewAjax(Request $request): void
    {
        $command = $this->execute(new UpdateReview($request));
        if ($command->success()) {
            wp_send_json_success($command->response());
        }
        wp_send_json_error($command->response());
    }

    /**
     * @return void
     */
    protected function setAddon()
    {
        $this->addon = glsr(Application::class);
    }
}

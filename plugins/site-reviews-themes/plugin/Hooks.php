<?php

namespace GeminiLabs\SiteReviews\Addon\Themes;

use GeminiLabs\SiteReviews\Addon\Themes\Application;
use GeminiLabs\SiteReviews\Addon\Themes\Controllers\ApiController;
use GeminiLabs\SiteReviews\Addon\Themes\Controllers\Controller;
use GeminiLabs\SiteReviews\Addon\Themes\Controllers\ThemeController;
use GeminiLabs\SiteReviews\Addons\Hooks as AddonHooks;

class Hooks extends AddonHooks
{
    protected $api;
    protected $theme;

    /**
     * @return void
     */
    public function run()
    {
        parent::run();
        add_filter('site-reviews/block/form/attributes', [$this->controller, 'filterBlockAttributes']);
        add_filter('site-reviews/block/reviews/attributes', [$this->controller, 'filterBlockAttributes']);
        add_filter('site-reviews/block/summary/attributes', [$this->controller, 'filterBlockAttributes']);
        add_filter('site-reviews-images/block/images/attributes', [$this->controller, 'filterBlockAttributes']);
        add_filter('use_block_editor_for_post_type', [$this->controller, 'filterBlockEditor'], 999, 2); // run late
        add_filter('manage_'.Application::POST_TYPE.'_posts_columns', [$this->controller, 'filterColumnsForPostType']);
        add_filter('site-reviews/integration/elementor/register/controls', [$this->controller, 'filterElementorWidgetControls'], 10, 2);
        add_filter('site-reviews/enqueue/admin/localize', [$this->controller, 'filterLocalizedAdminVariables']);
        add_filter('site-reviews/enqueue/public/localize', [$this->controller, 'filterLocalizedPublicVariables']);
        add_filter('post_row_actions', [$this->controller, 'filterRowActions'], 100, 2);

        add_filter('site-reviews/defaults/site-reviews/defaults', [$this->controller, 'filterShortcodeDefaults']);
        add_filter('site-reviews/defaults/site-reviews-form/defaults', [$this->controller, 'filterShortcodeDefaults']);
        add_filter('site-reviews/defaults/site-reviews-summary/defaults', [$this->controller, 'filterShortcodeDefaults']);
        add_filter('site-reviews-images/defaults/site-reviews-images/defaults', [$this->controller, 'filterShortcodeDefaults']);
        // add_filter('site-reviews/documentation/shortcodes/site_reviews', [$this->controller, 'filterSiteReviewsDocumentation']);
        // add_filter('site-reviews/documentation/shortcodes/site_reviews_form', [$this->controller, 'filterSiteReviewsFormDocumentation']);
        // add_filter('site-reviews/documentation/shortcodes/site_reviews_images', [$this->controller, 'filterSiteReviewsImagesDocumentation']);
        // add_filter('site-reviews/documentation/shortcodes/site_reviews_summary', [$this->controller, 'filterSiteReviewsSummaryDocumentation']);
        add_filter('site-reviews/addon/settings', [$this->controller, 'filterWoocommerceSettings'], 20);
        add_action('add_meta_boxes_'.Application::POST_TYPE, [$this->controller, 'registerMetaBoxes']);
        add_action('init', [$this->controller, 'registerPostType'], 8);
        add_action('admin_notices', [$this->controller, 'renderBetaNotice']);
        add_action('manage_'.Application::POST_TYPE.'_posts_custom_column', [$this->controller, 'renderColumnValues'], 10, 2);
        add_action('edit_form_top', [$this->controller, 'renderNotice']);
        add_action('edit_form_after_editor', [$this->controller, 'renderTheme']);
        add_action('admin_footer', [$this->controller, 'renderTemplates']);
        add_action('admin_menu', [$this->controller, 'reorderMenu'], 10);
        add_action('site-reviews/route/ajax/theme', [$this->controller, 'themeAjax']);
        add_action('site-reviews/route/ajax/theme-tags', [$this->controller, 'themeTagsAjax']);
        add_filter('site-reviews/builder/field/themed-rating', [$this->theme, 'filterFieldThemedRating']);
        add_filter('site-reviews/review-form/fields', [$this->theme, 'filterReviewFormFields'], 10, 2);
        add_filter('site-reviews/interpolate/reviews', [$this->theme, 'filterReviewsContext'], 20, 3);
        add_filter('site-reviews/reviews/html/theme', [$this->theme, 'filterReviewsHtmlTheme'], 10, 2);
        add_filter('site-reviews/build/template/reviews', [$this->theme, 'filterReviewsTemplate'], 100, 2); // 1 higher than site-reviews-forms
        add_filter('site-reviews/build/template/review', [$this->theme, 'filterReviewTemplate'], 100, 2); // 1 higher than site-reviews-forms
        add_filter('site-reviews/shortcode/site_reviews/attributes', [$this->theme, 'filterShortcodeAttributes'], 10, 2);
        add_filter('site-reviews/shortcode/site_reviews_form/attributes', [$this->theme, 'filterShortcodeAttributes'], 10, 2);
        add_filter('site-reviews/shortcode/site_reviews_images/attributes', [$this->theme, 'filterShortcodeAttributes'], 10, 2);
        add_filter('site-reviews/shortcode/site_reviews_summary/attributes', [$this->theme, 'filterShortcodeAttributes'], 10, 2);
        add_filter('site-reviews/partial/classname', [$this->theme, 'filterStarRatingPartial'], 10, 3); // this will override glsr_star_rating output
        add_filter('site-reviews/review/tag/avatar', [$this->theme, 'filterTagAvatar'], 10, 2);
        add_filter('site-reviews/review/tag/content', [$this->theme, 'filterTagContent'], 10, 2);
        add_action('save_post_'.Application::POST_TYPE, [$this->theme, 'saveTheme']);

        add_filter('site-reviews/api/reviews/parameters', [$this->api, 'filterApiReviewsParameters']);
        add_filter('site-reviews/api/summary/parameters', [$this->api, 'filterApiSummaryParameters']);
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
        $this->api = glsr(ApiController::class);
        $this->theme = glsr(ThemeController::class);
        return glsr(Controller::class);
    }
}

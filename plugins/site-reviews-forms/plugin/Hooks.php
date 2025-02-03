<?php

namespace GeminiLabs\SiteReviews\Addon\Forms;

use GeminiLabs\SiteReviews\Addon\Forms\Controllers\ApiController;
use GeminiLabs\SiteReviews\Addon\Forms\Controllers\Controller;
use GeminiLabs\SiteReviews\Addon\Forms\Controllers\FieldController;
use GeminiLabs\SiteReviews\Addon\Forms\Controllers\TemplateController;
use GeminiLabs\SiteReviews\Addons\Hooks as AddonHooks;

class Hooks extends AddonHooks
{
    protected $api;
    protected $fields;
    protected $template;

    /**
     * @return void
     */
    public function run()
    {
        parent::run();
        add_filter('site-reviews/api/reviews/parameters', [$this->api, 'filterApiReviewsParameters']);
        add_filter('site-reviews/api/summary/parameters', [$this->api, 'filterApiSummaryParameters']);
        add_filter('site-reviews/block/form/attributes', [$this->controller, 'filterBlockAttributes']);
        add_filter('site-reviews/block/reviews/attributes', [$this->controller, 'filterBlockAttributes']);
        add_filter('site-reviews-images/block/images/attributes', [$this->controller, 'filterBlockAttributes']);
        add_filter('use_block_editor_for_post_type', [$this->controller, 'filterBlockEditor'], 999, 2); // run late
        add_action('site-reviews/defaults/column-filterby/defaults', [$this->controller, 'filterColumnFilterby']);
        add_action('pre_get_posts', [$this->controller, 'filterColumnFilterQuery'], 10, 3);
        add_filter('manage_'.Application::POST_TYPE.'_posts_columns', [$this->controller, 'filterColumnsForPostType']);
        add_filter('site-reviews/documentation/faq', [$this->controller, 'filterDocumentationFaq']);
        add_filter('site-reviews/integration/elementor/register/controls', [$this->controller, 'filterElementorWidgetControls'], 10, 2);
        add_action('site-reviews/defaults/listtable-filters', [$this->controller, 'filterListtableFilters']);
        add_filter('site-reviews/enqueue/public/localize', [$this->controller, 'filterLocalizedPublicVariables']);
        add_filter('post_row_actions', [$this->controller, 'filterRowActions'], 100, 2);
        add_filter('site-reviews/defaults/site-reviews/defaults', [$this->controller, 'filterShortcodeDefaults']);
        add_filter('site-reviews/defaults/site-reviews-form/defaults', [$this->controller, 'filterShortcodeDefaults']);
        add_filter('site-reviews-images/defaults/site-reviews-images/defaults', [$this->controller, 'filterShortcodeDefaults']);
        add_filter('site-reviews/documentation/shortcodes/site_reviews', [$this->controller, 'filterSiteReviewsDocumentation']);
        add_filter('site-reviews/documentation/shortcodes/site_reviews_form', [$this->controller, 'filterSiteReviewsFormDocumentation']);
        add_filter('site-reviews/documentation/shortcodes/site_reviews_images', [$this->controller, 'filterSiteReviewsImagesDocumentation']);
        add_action('site-reviews/route/ajax/metabox-details', [$this->controller, 'metaboxDetailsAjax']);
        add_action('add_meta_boxes_'.Application::POST_TYPE, [$this->controller, 'registerMetaBoxes']);
        add_action('init', [$this->controller, 'registerPostType'], 8);
        add_action('manage_'.Application::POST_TYPE.'_posts_custom_column', [$this->controller, 'renderColumnValues'], 10, 2);
        add_action('admin_menu', [$this->controller, 'reorderMenu'], 11);
        add_action('save_post_'.Application::POST_TYPE, [$this->controller, 'saveMetaboxes']);
        add_action('site-reviews/route/ajax/filter-form', [$this->controller, 'searchFormsAjax']);
        add_filter('site-reviews/enqueue/admin/localize', [$this->fields, 'filterAdminLocalizedVariables']);
        add_filter('site-reviews/builder/field/assigned-posts', [$this->fields, 'filterBuilderFieldAssignedPosts']);
        add_filter('site-reviews/builder/field/assigned-terms', [$this->fields, 'filterBuilderFieldAssignedTerms']);
        add_filter('site-reviews/builder/field/assigned-users', [$this->fields, 'filterBuilderFieldAssignedUsers']);
        add_filter('site-reviews/review-form/fields', [$this->fields, 'filterFormFields'], 10, 2);
        add_filter('site-reviews/config/forms/metabox-fields', [$this->fields, 'filterMetaboxFieldsConfig'], 20);
        add_filter('site-reviews/review-form/fields/normalized', [$this->fields, 'filterMultiFields'], 10, 2);
        add_filter('site-reviews/review/build/before', [$this->fields, 'filterReviewCustomDefaults'], 10, 2);
        add_filter('site-reviews/shortcode/atts', [$this->fields, 'filterShortcodeAttributes'], 10, 3);
        add_filter('site-reviews/validation/rules/normalized', [$this->fields, 'filterValidationRules'], 99, 2);
        add_action('admin_footer-post.php', [$this->fields, 'renderFieldTemplates']);
        add_action('admin_footer-post-new.php', [$this->fields, 'renderFieldTemplates']);
        add_action('site-reviews/defaults', [$this->fields, 'setFieldSanitizers'], 10, 4);
        add_filter('site-reviews/enqueue/public/inline-styles', [$this->template, 'filterInlineStyles']);
        add_filter('site-reviews/build/template/review', [$this->template, 'filterReviewTemplate'], 99, 2);
        add_filter('site-reviews/review/build/after', [$this->template, 'filterReviewTemplateTags'], 10, 3);
        add_filter('site-reviews/custom/wrapped', [$this->template, 'filterWrappedTagValue'], 10, 3);
        add_filter('site-reviews/review/wrapped', [$this->template, 'filterWrappedTagValue'], 10, 3);
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
        $this->fields = glsr(FieldController::class);
        $this->template = glsr(TemplateController::class);
        return glsr(Controller::class);
    }
}

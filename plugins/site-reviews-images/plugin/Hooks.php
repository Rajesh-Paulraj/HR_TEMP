<?php

namespace GeminiLabs\SiteReviews\Addon\Images;

use GeminiLabs\SiteReviews\Addon\Images\Controllers\Controller;
use GeminiLabs\SiteReviews\Addon\Images\Controllers\ApiController;
use GeminiLabs\SiteReviews\Addon\Images\Controllers\GridController;
use GeminiLabs\SiteReviews\Addon\Images\Controllers\MediaController;
use GeminiLabs\SiteReviews\Addons\Hooks as AddonHooks;

class Hooks extends AddonHooks
{
    protected $api;
    protected $grid;
    protected $media;

    /**
     * @return void
     */
    public function run()
    {
        parent::run();
        add_action('site-reviews-filters/sql-and/build/filter_by_media', [$this->controller, 'addonFiltersBuildAnd'], 10, 3);
        add_action('site-reviews-filters/sql-join/build/filter_by_media', [$this->controller, 'addonFiltersBuildJoin'], 10, 3);
        add_action('wp_ajax_find_posts', [$this->controller, 'allowReviewAttachments'], 0);
        add_action('site-reviews/review/created', [$this->controller, 'attachImagesToReview'], 10, 2);
        add_action('before_delete_post', [$this->controller, 'deleteAttachmentsWithReview']);
        add_action('elementor/widget/site_reviews_form/skins_init', [$this->controller, 'enqueueDropzoneInElementor']);
        add_filter('site-reviews-filters/config/forms/filters-form', [$this->controller, 'filterAddonFiltersConfig']);
        add_filter('site-reviews-filters/status/filtered-by', [$this->controller, 'filterAddonFiltersFilteredBy'], 10, 2);
        add_filter('site-reviews-filters/defaults/filtered/casts', [$this->controller, 'filterAddonFiltersFilteredCasts']);
        add_filter('site-reviews-filters/defaults/filtered/defaults', [$this->controller, 'filterAddonFiltersFilteredDefaults']);
        add_filter('site-reviews-filters/sql-order-by/validate/filter_by_media', [$this->controller, 'filterAddonFiltersValidateOrderBy'], 10, 4);
        add_filter('site-reviews/shortcode/display-options', [$this->controller, 'filterDisplayOptions'], 10, 2);
        add_filter('site-reviews/rendered/template/reviews-form', [$this->controller, 'filterDropzoneTemplate'], 10, 2);
        add_filter('site-reviews/builder/field/dropzone', [$this->controller, 'filterFieldDropzone']);
        add_filter('site-reviews/defaults/custom-fields/guarded', [$this->controller, 'filterGuardedCustomFields']);
        add_filter('site-reviews/shortcode/hide-options', [$this->controller, 'filterHideOptions'], 10, 2);
        add_filter('site-reviews/enqueue/public/localize', [$this->controller, 'filterLocalizedPublicVariables']);
        add_filter('site-reviews/database/sql/query-reviews', [$this->controller, 'filterQueryReviewsSql']);
        add_filter('site-reviews/defaults/review/defaults', [$this->controller, 'filterReviewDefaultsArray']);
        add_filter('site-reviews/config/forms/review-form', [$this->controller, 'filterReviewFormFields'], 9);
        add_filter('site-reviews/review-form/order', [$this->controller, 'filterReviewFormOrder'], 9);
        add_filter('site-reviews/review/tag/'.Application::SLUG, [$this->controller, 'filterReviewImagesTag']);
        add_filter('site-reviews/defaults/review/sanitize', [$this->controller, 'filterReviewSanitizeArray']);
        add_filter('site-reviews/build/template/review', [$this->controller, 'filterReviewTemplate']);
        add_filter('site-reviews/validation/rules', [$this->controller, 'filterValidationRules']);
        add_filter('site-reviews/documentation/shortcodes', [$this->controller, 'filterDocumentationShortcodes']);
        add_action('wp_footer', [$this->controller, 'renderDropzone'], 9); // this must load before wp_enqueue_scripts output
        add_action('wp_footer', [$this->controller, 'renderLightbox'], 9); // this must load before wp_enqueue_scripts output
        add_action('wp_footer', [$this->controller, 'renderSwiper'], 9); // this must load before wp_enqueue_scripts output
        add_action('wp_footer', [$this->controller, 'renderTemplates'], 50);
        add_action('site-reviews/route/ajax/'.Application::ID, [$this->controller, 'routeAjaxRequests'], 10, 2);
        add_action('upgrader_process_complete', [$this->controller, 'upgrade'], 10, 2);

        add_filter('site-reviews/api/reviews/prepare/images', [$this->api, 'filterApiReviewsPrepareImages'], 10, 2);
        add_filter('site-reviews/api/reviews/properties', [$this->api, 'filterApiReviewsProperties']);

        add_action('elementor/widgets/register', [$this->grid, 'registerElementorWidgets']);
        add_filter('site-reviews/enqueue/admin/localize', [$this->grid, 'filterLocalizedAdminVariables']);
        add_action('site-reviews/route/ajax/fetch-image-gallery', [$this->grid, 'fetchImageGalleryAjax']);
        add_action('site-reviews/route/ajax/fetch-image-review', [$this->grid, 'fetchImageReviewAjax']);
        add_filter('site-reviews/router/admin/unguarded-actions', [$this->grid, 'filterUnguardedActions']);
        add_filter('site-reviews/router/public/unguarded-actions', [$this->grid, 'filterUnguardedActions']);

        add_filter('site-reviews/column/'.Application::SLUG, [$this->media, 'filterColumnImages']);
        add_filter('site-reviews/review-table/clauses', [$this->media, 'filterColumnOrderbyClause'], 10, 3);
        add_filter('site-reviews/defaults/column-orderby/defaults', [$this->media, 'filterColumnOrderbyDefaults']);
        add_filter('manage_'.glsr()->post_type.'_posts_columns', [$this->media, 'filterColumnsForPostType'], 11);
        add_filter('wp_insert_attachment_data', [$this->media, 'filterInsertAttachmentData']);
        add_filter('site-reviews/enqueue/admin/localize', [$this->media, 'filterLocalizedAdminVariables']);
        add_filter('media_row_actions', [$this->media, 'filterMediaRowActions'], 10, 2);
        add_filter('plupload_default_params', [$this->media, 'filterPluploadParameters']);
        add_filter('plupload_default_settings', [$this->media, 'filterPluploadSettings']);
        add_filter('ajax_query_attachments_args', [$this->media, 'filterQueryAttachmentsArgs']);
        add_filter('manage_edit-'.glsr()->post_type.'_sortable_columns', [$this->media, 'filterSortableColumns']);
        add_filter('wp_handle_upload_prefilter', [$this->media, 'filterUploadDirectory']);
        add_action('add_meta_boxes_'.glsr()->post_type, [$this->media, 'registerMetaboxes'], 10, 2);
        add_action('admin_bar_menu', [$this->media, 'removeAttachmentAdminBarLink'], 99);
        add_action('edit_form_before_permalink', [$this->media, 'removeAttachmentPermalink']);
        add_action('clean_attachment_cache', [$this->media, 'renameAttachment']);
        add_action('admin_footer-post.php', [$this->media, 'renderTemplates']);
        add_action('admin_footer-post-new.php', [$this->media, 'renderTemplates']);
        add_action('site-reviews/review/saved', [$this->media, 'saveImagesMetabox']);
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
        $this->grid = glsr(GridController::class);
        $this->media = glsr(MediaController::class);
        return glsr(Controller::class);
    }
}

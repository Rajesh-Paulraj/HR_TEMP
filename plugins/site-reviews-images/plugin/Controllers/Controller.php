<?php

namespace GeminiLabs\SiteReviews\Addon\Images\Controllers;

use GeminiLabs\SiteReviews\Addon\Images\Application;
use GeminiLabs\SiteReviews\Addon\Images\Blocks\SiteReviewsImagesBlock;
use GeminiLabs\SiteReviews\Addon\Images\Fields\Dropzone;
use GeminiLabs\SiteReviews\Addon\Images\Shortcodes\SiteReviewsImagesShortcode;
use GeminiLabs\SiteReviews\Addon\Images\Tags\ReviewImagesTag;
use GeminiLabs\SiteReviews\Addon\Images\Tinymce\SiteReviewsImagesTinymce;
use GeminiLabs\SiteReviews\Addon\Images\Upgrader;
use GeminiLabs\SiteReviews\Addon\Images\Uploader;
use GeminiLabs\SiteReviews\Addon\Images\Widgets\SiteReviewsImagesWidget;
use GeminiLabs\SiteReviews\Addons\Controller as AddonController;
use GeminiLabs\SiteReviews\Commands\CreateReview;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Integrations\Elementor\ElementorFormWidget;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Request;
use GeminiLabs\SiteReviews\Review;

class Controller extends AddonController
{
    /**
     * {@inheritdoc}
     */
    protected $addon;

    /**
     * @param int|string $value
     * @param string $key
     * @param \GeminiLabs\SiteReviews\Addon\Filters\SqlModifier $modifier
     * @return void
     * @action site-reviews-filters/sql-and/build/filter_by_media
     * @todo check for WPML Media
     */
    public function addonFiltersBuildAnd($value, $key, $modifier)
    {
        if ('text' === $value) {
            $modifier->values[$key] = "AND img.ID IS NULL";
        }
    }

    /**
     * @param int|string $value
     * @param string $key
     * @param \GeminiLabs\SiteReviews\Addon\Filters\SqlModifier $modifier
     * @return void
     * @action site-reviews-filters/sql-join/build/filter_by_media
     * @todo check for WPML Media
     */
    public function addonFiltersBuildJoin($value, $key, $modifier)
    {
        global $wpdb;
        if ('images' === $value) {
            $modifier->values[$key] = "INNER JOIN {$wpdb->posts} AS img ON (r.review_id = img.post_parent AND img.post_type = 'attachment')";
        }
        if ('text' === $value) {
            $modifier->values[$key] = "LEFT JOIN {$wpdb->posts} AS img ON (r.review_id = img.post_parent AND (img.ID) IN (SELECT ID FROM {$wpdb->posts} WHERE post_type = 'attachment' GROUP BY ID))";
        }
    }

    /**
     * @return void
     * @action wp_ajax_find_posts
     */
    public function allowReviewAttachments()
    {
        global $wp_post_types;
        if (isset($wp_post_types[glsr()->post_type])) {
            $wp_post_types[glsr()->post_type]->public = true;
        }
    }

    /**
     * @return void
     * @action site-reviews/review/created
     */
    public function attachImagesToReview(Review $review, CreateReview $command)
    {
        if (!empty($command->request[Application::SLUG])) {
            $images = json_decode($command->request[Application::SLUG]);
            glsr(Uploader::class)->attachImages($images, $review->ID);
        }
    }

    /**
     * @param int $postId
     * @return void
     * @action before_delete_post
     */
    public function deleteAttachmentsWithReview($postId)
    {
        if (glsr()->post_type !== get_post_type($postId)) {
            return;
        }
        $attachments = get_attached_media('image', $postId);
        $delete = glsr_get_option('addons.'.Application::SLUG.'.deletion');
        foreach ($attachments as $attachment) {
            if ('yes' === $delete) {
                wp_delete_attachment($attachment->ID, true);
                continue;
            }
            $attachment->post_title = $attachment->post_name;
            wp_update_post($attachment);
        }
    }

    /**
     * @return void
     * @action admin_enqueue_scripts
     */
    public function enqueueAdminAssets()
    {
        if ($this->isReviewAdminPage()) { // only load on the Site Reviews pages!
            wp_enqueue_media(); // just load it all, why not
            $this->enqueueAsset('css', [
                'dependencies' => ['media-views'],
                'suffix' => 'admin',
            ]);
            $this->enqueueAsset('js', [
                'dependencies' => ['backbone', 'jquery-ui-sortable', 'media-grid', 'underscore'],
                'suffix' => 'admin',
            ]);
        }
    }

    /**
     * @return void
     * @action wp_enqueue_scripts
     */
    public function enqueuePublicAssets()
    {
        parent::enqueuePublicAssets();
        // wp_enqueue_style(glsr()->id.'/splide',
        //    'https://cdn.jsdelivr.net/npm/@splidejs/splide@4.0.7/dist/css/splide-core.min.css',
        //     [glsr()->id],
        //     '4.0'
        // );
        wp_register_script(glsr()->id.'/dropzone',
            'https://cdn.jsdelivr.net/gh/pryley/dropzone-v6@fix-exif-orientation/dist/dropzone-min.js',
            [glsr()->id],
            '6.0-beta',
            true
        );
        wp_register_script(glsr()->id.'/exif',
            'https://cdn.jsdelivr.net/npm/exif-js@2.3/exif.min.js',
            [glsr()->id],
            '2.3',
            true
        );
        wp_register_script(glsr()->id.'/spotlight',
            'https://cdn.jsdelivr.net/gh/pryley/spotlight@0.7.8-custom/dist/spotlight.bundle.js',
            [glsr()->id],
            '0.7.8',
            true
        );
        wp_register_script(glsr()->id.'/splide',
            'https://cdn.jsdelivr.net/npm/@splidejs/splide@4.0.7/dist/js/splide.min.js',
            [glsr()->id],
            '4.0',
            true
        );
    }

    /**
     * @return void
     * @action elementor/widget/site_reviews_form/skins_init
     */
    public function enqueueDropzoneInElementor()
    {
        glsr()->store('use_dropzone', true);
    }

    /**
     * @return array
     * @filter site-reviews-filters/config/forms/filters-form
     */
    public function filterAddonFiltersConfig(array $config)
    {
        // $filters = glsr_get_option('addons.filters.filter_by');
        // if (in_array('media', $filters)) {
            $config['filter_by_media'] = [
                'options' => [
                    '' => _x('Text, image', 'filter-by option', 'site-reviews-images'),
                    'images' => _x('Images only', 'filter-by option', 'site-reviews-images'),
                    'text' => _x('Text only', 'filter-by option', 'site-reviews-images'),
                ],
                'type' => 'select',
                'value' => filter_input(INPUT_GET, 'filter_by_media', FILTER_SANITIZE_STRING),
            ];
        // }
        return $config;
    }

    /**
     * @return array
     * @filter site-reviews-filters/status/filtered-by
     */
    public function filterAddonFiltersFilteredBy(array $filteredBy, array $urlParameters)
    {
        $filter = Arr::get($urlParameters, 'filter_by_media');
        if ('images' === $filter) {
            $filteredBy[] = __('Images only', 'site-reviews-images');
        }
        if ('text' === $filter) {
            $filteredBy[] = __('Text only', 'site-reviews-images');
        }
        return $filteredBy;
    }

    /**
     * @return array
     * @filter site-reviews/defaults/filtered/casts
     */
    public function filterAddonFiltersFilteredCasts(array $casts)
    {
        $casts['filter_by_media'] = 'string';
        return $casts;
    }

    /**
     * @return array
     * @filter site-reviews/defaults/filtered/defaults
     */
    public function filterAddonFiltersFilteredDefaults(array $defaults)
    {
        $defaults['filter_by_media'] = '';
        return $defaults;
    }

    /**
     * @param bool $result
     * @param int|string $value
     * @param string $parameter
     * @param \GeminiLabs\SiteReviews\Addon\Filters\SqlModifier $modifier
     * @return bool
     * @filter site-reviews-filters/sql-order-by/validate/filter_by_media
     */
    public function filterAddonFiltersValidateOrderBy($result, $value, $parameter, $modifier)
    {
        return in_array($value, ['images', 'text']);
    }

    /**
     * @param string $shortcode
     * @return array
     * @filter site-reviews/shortcode/display-options
     */
    public function filterDisplayOptions(array $options, $shortcode)
    {
        if ('site_reviews' === $shortcode) {
            $options['filter_by_media'] = _x('Display the media filter', 'admin-text', 'site-reviews-images');
            natsort($options);
        }
        return $options;
    }

    /**
     * @return array
     * @filter site-reviews/documentation/shortcodes
     */
    public function filterDocumentationShortcodes(array $sections)
    {
        $sections[] = $this->addon->path('views/documentation/site_reviews_images.php');
        return $sections;
    }

    /**
     * @param string $template
     * @return void|string
     * @filter site-reviews/rendered/template/reviews-form
     */
    public function filterDropzoneTemplate($template, array $data)
    {
        if (!in_array(Application::SLUG, Arr::consolidate(Arr::get($data, 'args.hide')))) {
            glsr()->store('use_dropzone', true);
        }
        return $template;
    }

    /**
     * @return string
     * @filter site-reviews/builder/field/dropzone
     */
    public function filterFieldDropzone()
    {
        return Dropzone::class;
    }

    /**
     * @return array
     * @filter site-reviews/defaults/custom-fields/guarded
     */
    public function filterGuardedCustomFields(array $guarded)
    {
        $guarded[] = 'images';
        return Arr::unique($guarded);
    }

    /**
     * @param string $shortcode
     * @return array
     * @filter site-reviews/shortcode/hide-options
     */
    public function filterHideOptions(array $options, $shortcode)
    {
        $insertIndex = array_search('terms', $options);
        if ('site_reviews' == $shortcode) {
            return Arr::insertBefore($insertIndex, $options, [
                Application::SLUG => esc_html_x('Hide the images', 'admin-text', 'site-reviews-images'),
            ]);
        }
        if ('site_reviews_filter' == $shortcode && glsr('Addon\Filters\Application')->slug) {
            $options = Arr::insertBefore('filter_by_rating', $options, [
                'filter_by_media' => esc_html_x('Hide the images filter', 'admin-text', 'site-reviews-images'),
            ]);
        }
        if ('site_reviews_form' == $shortcode) {
            return Arr::insertBefore($insertIndex, $options, [
                Application::SLUG => esc_html_x('Hide the images field', 'admin-text', 'site-reviews-images'),
            ]);
        }
        return $options;
    }

    /**
     * @return array
     * @filter site-reviews/enqueue/public/localize
     */
    public function filterLocalizedPublicVariables(array $variables)
    {
        $variables['addons'][Application::ID] = [
            'acceptedfiles' => Str::fallback(glsr_get_option('addons.'.Application::SLUG.'.accepted_files'), 'image/jpeg,image/png'),
            'action' => Application::ID,
            'maxfiles' => glsr_get_option('addons.'.Application::SLUG.'.max_files', 5),
            'maxfilesize' => glsr_get_option('addons.'.Application::SLUG.'.max_file_size', 5),
            'modal' => glsr(Application::class)->imageModal(),
            'nonce' => wp_create_nonce(Application::ID),
            'swiper' => null,
            'text' => [
                'cancelUpload' => __('Cancel upload', 'site-reviews-images'),
                'cancelUploadConfirmation' => __('Are you sure you want to cancel this upload?', 'site-reviews-images'),
                'fallbackMessage' => __('Your browser does not support drag & drop file uploads.', 'site-reviews-images'),
                'fallbackText' => __('Please use the fallback form below to upload your files like in the olden days.', 'site-reviews-images'),
                'fileTooBig' => __('File is too big ({{filesize}}MB). Max filesize: {{maxFilesize}}MB.', 'site-reviews-images'),
                'genericModalError' => __('Could not load the modal content.', 'site-reviews-images'),
                'imageGallery' => __('Image Gallery', 'site-reviews-images'),
                'invalidFileType' => __('You cannot upload files of this type.', 'site-reviews-images'),
                'maxFilesExceeded' => __('You cannot upload more than {{maxFiles}} images.', 'site-reviews-images'),
                'pleaseWait' => __('Please wait...', 'site-reviews-images'),
                'removeFileConfirmation' => __('Are you sure you want to remove this image?', 'site-reviews-images'),
                'responseError' => __('Server responded with {{statusCode}} code.', 'site-reviews-images'),
                'uploadCanceled' => __('Upload canceled.', 'site-reviews-images'),
                'viewImageGallery' => __('View Image Gallery', 'site-reviews-images'),
            ],
        ];
        return $variables;
    }

    /**
     * @param string $statement
     * @return string
     * @filter site-reviews/database/sql/query-reviews
     */
    public function filterQueryReviewsSql($statement)
    {
        global $wpdb;
        $wpmlLocale = apply_filters('wpml_current_language', null);
        if (!empty($wpmlLocale) && defined('WPML_MEDIA_VERSION')) { // check for the WPML Media plugin
            $sql = sprintf("SHOW TABLES LIKE '%sicl_translations'", $wpdb->get_blog_prefix());
            if (!empty($wpdb->get_col($sql))) {
                $statement = Str::replaceFirst('FROM', ", GROUP_CONCAT(DISTINCT icl.element_id ORDER BY img.menu_order ASC) AS images FROM", $statement);
                $statement = Str::replaceFirst('WHERE', "
                    LEFT JOIN {$wpdb->posts} AS img ON (r.review_id = img.post_parent AND img.post_type = 'attachment')
                    LEFT JOIN {$wpdb->prefix}icl_translations AS icl ON (img.ID = icl.element_id AND icl.element_type = 'post_attachment' AND icl.language_code = '{$wpmlLocale}')
                    WHERE
                ", $statement);
                return $statement;
            }
        }
        $statement = Str::replaceFirst('FROM', ', GROUP_CONCAT(DISTINCT img.ID ORDER BY img.menu_order ASC) AS images FROM', $statement);
        // make sure the images guids are unique as translation plugins may duplicate the attachments
        $statement = Str::replaceFirst('WHERE', "
            LEFT JOIN {$wpdb->posts} AS img ON (r.review_id = img.post_parent AND (img.ID, img.guid) IN (
                SELECT MIN(ID), guid FROM {$wpdb->posts} WHERE post_type = 'attachment' GROUP BY guid
            ))
            WHERE",
        $statement);
        return $statement;
    }

    /**
     * @return array
     * @filter site-reviews/defaults/review/defaults
     */
    public function filterReviewDefaultsArray(array $defaults)
    {
        $defaults[Application::SLUG] = '';
        return $defaults;
    }

    /**
     * @return string
     * @filter site-reviews/review/tag/{Application::SLUG}
     */
    public function filterReviewImagesTag()
    {
        return ReviewImagesTag::class;
    }

    /**
     * @return array
     * @filter site-reviews/defaults/review/sanitize
     */
    public function filterReviewSanitizeArray(array $sanitize)
    {
        $sanitize[Application::SLUG] = 'array-int';
        return $sanitize;
    }

    /**
     * @param string $template
     * @return string
     * @filter site-reviews/build/template/review
     */
    public function filterReviewTemplate($template)
    {
        if (true === glsr()->retrieve('image_review')) {
            return $template;
        }
        if (false === strpos($template, '{{ images }}')) {
            $template = str_replace('{{ content }}', '{{ content }} {{ images }}', $template);
        }
        return $template;
    }

    /**
     * @return array
     * @filter site-reviews/defer-scripts
     */
    public function filterScriptsDefer(array $handles)
    {
        $handles[] = glsr()->id.'/dropzone';
        $handles[] = glsr()->id.'/exif';
        $handles[] = glsr()->id.'/splide';
        $handles[] = glsr()->id.'/spotlight';
        return $handles;
    }

    /**
     * @return array
     * @filter site-reviews/addon/settings
     */
    public function filterSettings(array $settings)
    {
        $settings = parent::filterSettings($settings);
        $settings['settings.forms.required']['options'][Application::SLUG] = esc_attr_x('Images', 'admin-text', 'site-reviews-images');
        return $settings;
    }

    /**
     * @return array
     * @filter site-reviews/config/forms/review-form
     */
    public function filterReviewFormFields(array $fields)
    {
        $fields[Application::SLUG] = [
            'label' => esc_html__('Do you have photos to share?', 'site-reviews-images'),
            'type' => 'dropzone',
        ];
        return $fields;
    }

    /**
     * @return array
     * @filter site-reviews/review-form/order
     */
    public function filterReviewFormOrder(array $order)
    {
        return Arr::insertBefore(array_search('terms', $order), $order, [Application::SLUG]);
    }

    /**
     * @param array $rules
     * @return array
     */
    public function filterValidationRules($rules)
    {
        $rules[Application::SLUG] = 'required';
        return $rules;
    }

    /**
     * @return void
     * @action init
     */
    public function registerBlocks()
    {
        glsr(SiteReviewsImagesBlock::class)->register('images');
    }

    /**
     * @return void
     * @action init
     */
    public function registerShortcodes()
    {
        add_shortcode('site_reviews_images', [glsr(SiteReviewsImagesShortcode::class), 'buildShortcode']);
    }

    /**
     * @return void
     * @action admin_init
     */
    public function registerTinymcePopups()
    {
        $label = esc_html_x('Filter Reviews', 'admin-text', 'site-reviews-images');
        $shortcode = glsr(SiteReviewsImagesTinymce::class)->register('site_reviews_images', [
            'label' => $label,
            'title' => $label,
        ]);
        glsr()->append('mce', $shortcode->properties, 'site_reviews_images');
    }

    /**
     * @return void
     * @action widgets_init
     */
    public function registerWidgets()
    {
        register_widget(SiteReviewsImagesWidget::class);
    }

    /**
     * @return void
     * @action wp_footer
     */
    public function renderDropzone()
    {
        if (Cast::toBool(glsr()->retrieve('use_dropzone'))) {
            wp_enqueue_script(glsr()->id.'/dropzone');
            wp_enqueue_script(glsr()->id.'/exif');
            glsr(Application::class)->render('views/dropzone');
        }
    }

    /**
     * @return void
     * @action wp_footer
     */
    public function renderLightbox()
    {
        if (Cast::toBool(glsr()->retrieve('use_images'))) { // this tells us that images have been loaded on the page
            if ('lightbox' === glsr(Application::class)->imageModal()) {
                wp_enqueue_script(glsr()->id.'/spotlight');
            }
        }
    }

    /**
     * @return void
     * @action wp_footer
     */
    public function renderSwiper()
    {
        if (Cast::toBool(glsr()->retrieve('use_swiper'))) { // [site_reviews_images] has been loaded on the page
            wp_enqueue_script(glsr()->id.'/splide');
        }
    }

    /**
     * @return void
     * @action wp_footer
     */
    public function renderTemplates()
    {
        glsr(Application::class)->render('views/svg');
    }

    /**
     * @return mixed
     * @action site-reviews/route/ajax/{Application::ID}
     */
    public function routeAjaxRequests(Request $request)
    {
        $result = glsr(Uploader::class)->handle($request);
        return is_wp_error($result)
            ? wp_send_json_error(['error' => $result->get_error_message()])
            : wp_send_json_success($result);
    }

    /**
     * @param mixed $upgrader
     * @return void
     * @action upgrader_process_complete
     */
    public function upgrade($upgrader, array $data)
    {
        if (array_key_exists('plugins', $data)
            && in_array(plugin_basename($this->addon->file), $data['plugins'])
            && 'update' === $data['action']
            && 'plugin' === $data['type']) {
            // (new Upgrader)->run(); @todo run migrations here
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function setAddon()
    {
        $this->addon = glsr(Application::class);
    }
}

<?php

namespace GeminiLabs\SiteReviews\Addon\Images\Controllers;

use GeminiLabs\SiteReviews\Addon\Images\Application;
use GeminiLabs\SiteReviews\Addon\Images\Attachment;
use GeminiLabs\SiteReviews\Addon\Images\Columns\ColumnValueImages;
use GeminiLabs\SiteReviews\Addon\Images\Tags\ReviewImagesTag;
use GeminiLabs\SiteReviews\Addon\Images\Uploader;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Review;

class MediaController
{
    /**
     * @return string
     * @filter site-reviews/column/{Application::SLUG}
     */
    public function filterColumnImages()
    {
        return ColumnValueImages::class;
    }

    /**
     * @param array $columns
     * @return array
     * @filter manage_{glsr()->post_type}_posts_columns
     */
    public function filterColumnsForPostType($columns)
    {
        $columns = Arr::consolidate($columns);
        $label = '<span class="images-icon"><span>'.esc_html_x('Images', 'admin-text', 'site-reviews-images').'</span></span>';
        $columns = Arr::insertBefore('is_pinned', $columns, [
            Application::SLUG => $label,
        ]);
        return $columns;
    }

    /**
     * @param string $table
     * @param \WP_Query $query
     * @return array
     * @filter site-reviews/review-table/clauses
     */
    public function filterColumnOrderbyClause(array $clauses, $table, $query)
    {
        if ('images' !== $query->get('orderby')) {
            return $clauses;
        }
        global $wpdb;
        $order = $query->get('order');
        $clauses['groupby'] = "{$wpdb->posts}.ID";
        $clauses['join'] .= " LEFT JOIN {$wpdb->posts} AS img ON ({$wpdb->posts}.ID = img.post_parent AND (img.ID, img.guid) IN (SELECT MIN(ID), guid FROM {$wpdb->posts} WHERE post_type = 'attachment' GROUP BY guid)) ";
        $clauses['orderby'] = "COUNT(DISTINCT img.ID) $order, {$wpdb->posts}.post_date DESC";
        return $clauses;
    }

    /**
     * @return array
     * @filter site-reviews/defaults/column-orderby/defaults
     */
    public function filterColumnOrderbyDefaults(array $defaults)
    {
        $defaults['images'] = 'images';
        return $defaults;
    }

    /**
     * @return array
     */
    public function filterInsertAttachmentData(array $data)
    {
        if (Application::ID === Helper::filterInput(Application::ID)) {
            $title = sprintf(esc_attr_x('%s Image', 'image title', 'site-reviews-images'), glsr()->name);
            $data['post_status'] = 'private';
            $data['post_title'] = sanitize_text_field($title);
        }
        return $data;
    }

    /**
     * @return array
     * @filter site-reviews/enqueue/admin/localize
     */
    public function filterLocalizedAdminVariables(array $variables)
    {
        $variables['addons'][Application::ID] = [
            'text' => [
                'add' => _x('Add Images', 'media (admin-text)', 'site-reviews-images'),
                'edit' => _x('Edit Image', 'media (admin-text)', 'site-reviews-images'),
                'swap' => _x('Swap Image', 'media (admin-text)', 'site-reviews-images'),
                'extensions' => [
                    'image/jpeg' => ['jpg', 'jpeg'],
                    'image/png' => ['png'],
                ],
                'loadingUrl' => admin_url('images/spinner.gif'),
                'multiple' => _x(' images', 'media (admin-text)', 'site-reviews-images'),
                'noTitle' => _x('No Title', 'media (admin-text)', 'site-reviews-images'),
                'or' => _x('or', 'media (admin-text)', 'site-reviews-images'),
                'remove' => _x('Remove Image', 'media (admin-text)', 'site-reviews-images'),
                'select' => _x('Select Images', 'media (admin-text)', 'site-reviews-images'),
                'single' => _x(' file', 'media (admin-text)', 'site-reviews-images'),
                'uploadInstructions' => _x('Drop files here to upload', 'media (admin-text)', 'site-reviews-images'),
                'view' => _x('View', 'media (admin-text)', 'site-reviews-images'),
            ],
        ];
        return $variables;
    }

    /**
     * @return array
     * @filter media_row_actions
     */
    public function filterMediaRowActions(array $actions, \WP_Post $post)
    {
        if (!is_admin()) {
            return $actions;
        }
        if (!empty($post->post_parent) && $parent = get_post($post->post_parent)) {
            if ($parent->post_type == glsr()->post_type) {
                $actions['view'] = '';
            }
        }
        return $actions;
    }

    /**
     * @param string[] $file
     * @return string[]
     * @filter wp_handle_upload_prefilter
     */
    public function filterUploadDirectory($file)
    {
        if (Application::ID === Helper::filterInput(Application::ID)) {
            glsr(Uploader::class)->setUploadDirectory(glsr()->id);
        }
        return $file;
    }

    /**
     * @param array $parameters
     * @return array
     * @filter plupload_default_params
     */
    public function filterPluploadParameters($parameters)
    {
        $screen = glsr_current_screen();
        if ('post' === $screen->base && $screen->post_type === glsr()->post_type) {
            $parameters = Arr::set($parameters, Application::ID, Application::ID);
        }
        return $parameters;
    }

    /**
     * @param array $settings
     * @return array
     * @filter plupload_default_settings
     */
    public function filterPluploadSettings($settings)
    {
        $screen = glsr_current_screen();
        if ('post' === $screen->base && $screen->post_type === glsr()->post_type) {
            $settings = Arr::set($settings, 'filters.mime_types', [[
                'extensions' => 'jpg,jpeg,png',
            ]]);
        }
        return $settings;
    }

    /**
     * @param array $query
     * @return array
     * @filter ajax_query_attachments_args
     */
    public function filterQueryAttachmentsArgs($query)
    {
        if (Application::ID === Arr::get($query, 's')) {
            unset($query['s']);
            remove_filter('posts_clauses', '_filter_query_attachment_filenames'); // replace the search query with our own
            add_filter('posts_clauses', [$this, 'filterQueryAttachmentsPostClauses']);
        }
        return $query;
    }

    /**
     * @param array $clauses
     * @return array
     * @filter posts_clauses
     * @see filterQueryAttachmentsArgs
     */
    public function filterQueryAttachmentsPostClauses($clauses)
    {
        global $wpdb;
        remove_filter('posts_clauses', [$this, 'filterQueryAttachmentsPostClauses']);
        $clauses['join'] .= " LEFT JOIN {$wpdb->postmeta} AS glsri ON ({$wpdb->posts}.ID = glsri.post_id AND glsri.meta_key = '_wp_attached_file')";
        $clauses['where'] .= " AND (glsri.meta_value LIKE 'site-reviews/%')";
        return $clauses;
    }

    /**
     * @param array $columns
     * @return array
     * @filter manage_edit-{glsr()->post_type}_sortable_columns
     */
    public function filterSortableColumns($columns)
    {
        $columns = Arr::consolidate($columns);
        $columns['images'] = 'images';
        return $columns;
    }

    /**
     * @return void
     * @action add_meta_boxes
     */
    public function registerMetaboxes(\WP_Post $post)
    {
        add_meta_box(Application::ID, _x('Images', 'admin-text', 'site-reviews-images'), [$this, 'renderImagesMetabox'], null, 'normal');
    }

    /**
     * @return void
     * @action admin_bar_menu
     */
    public function removeAttachmentAdminBarLink(\WP_Admin_Bar $adminBar)
    {
        if (!is_admin()) {
            return;
        }
        $screen = glsr_current_screen();
        if ('post' != $screen->base || 'attachment' != $screen->post_type) {
            return;
        }
        $parent = get_post(get_post()->post_parent);
        if ($parent->post_type === glsr()->post_type) {
            $adminBar->remove_node('view');
        }
    }

    /**
     * @return void
     * @action edit_form_before_permalink
     */
    public function removeAttachmentPermalink(\WP_Post $post)
    {
        if (!is_admin()) {
            return;
        }
        if ('attachment' != $post->post_type || empty($post->post_parent)) {
            return;
        }
        $parent = get_post($post->post_parent);
        if ($parent->post_type === glsr()->post_type) {
            global $post_type_object;
            $post_type_object->public = false;
        }
    }

    /**
     * @param int $attachmentId
     * @return void
     * @action clean_attachment_cache
     */
    public function renameAttachment($attachmentId)
    {
        $attachment = get_post($attachmentId);
        $newParentId = filter_input(INPUT_GET, 'found_post_id');
        $oldParentId = filter_input(INPUT_GET, 'parent_post_id');
        if (!empty($oldParentId)) {
            if (glsr()->post_type === get_post_type($oldParentId)) {
                $attachment->post_title = $attachment->post_name;
                wp_update_post($attachment);
            }
        } elseif (!empty($newParentId)) {
            if (glsr()->post_type === get_post_type($newParentId)) {
                $attachment->post_title = sprintf(esc_attr_x('%s Image', 'image title', 'site-reviews-images'), glsr()->name);
                wp_update_post($attachment);
            }
        }
    }

    /**
     * @return void
     * @callback add_meta_box
     */
    public function renderImagesMetabox(\WP_Post $post)
    {
        if ($post->post_type === glsr()->post_type) {
            $attachments = ReviewImagesTag::getAttachmentsForJs($post->ID);
            glsr(Application::class)->render('views/metabox-images', [
                'addon' => Application::ID,
                'input' => glsr(Builder::class)->input([
                    'class' => 'glsri-media',
                    'data-attachments' => json_encode(array_values($attachments)),
                    'name' => glsr(Application::class)->ID,
                    'type' => 'hidden',
                    'value' => implode(',', glsr_get_review($post->ID)->images),
                ]),
            ]);
        }
    }

    /**
     * @return void
     * @action admin_footer-post.php
     */
    public function renderTemplates()
    {
        if (get_post_type() === glsr()->post_type) {
            glsr(Application::class)->render('views/templates', [
                'addon' => Application::ID,
            ]);
        }
    }

    /**
     * @return void
     * @action site-reviews/review/saved
     */
    public function saveImagesMetabox(Review $review)
    {
        if ('post' !== glsr_current_screen()->base) {
            return; // only run this on the edit review page.
        }
        if ($imageIds = Helper::filterInputArray(Application::ID)) {
            $attachIds = array_diff($imageIds, $review->images);
            $detachIds = array_diff($review->images, array_diff($imageIds, $attachIds));
            glsr(Attachment::class)->attach($attachIds, $review->ID);
            glsr(Attachment::class)->detach($detachIds, $review->ID);
            glsr(Attachment::class)->normalize($imageIds); // fix status, slug, and menu order
        } elseif (!empty($review->images)) {
            glsr(Attachment::class)->detach($review->images, $review->ID);
        }
    }
}

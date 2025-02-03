<?php

namespace GeminiLabs\SiteReviews\Addon\Forms\Controllers;

use GeminiLabs\SiteReviews\Addon\Forms\Application;
use GeminiLabs\SiteReviews\Addon\Forms\ColumnFilterForm;
use GeminiLabs\SiteReviews\Addon\Forms\Columns\FieldsColumn;
use GeminiLabs\SiteReviews\Addon\Forms\Columns\ReviewsColumn;
use GeminiLabs\SiteReviews\Addon\Forms\Columns\ShortcodeColumn;
use GeminiLabs\SiteReviews\Addon\Forms\Commands\RegisterPostType;
use GeminiLabs\SiteReviews\Addon\Forms\Metaboxes\FieldsMetabox;
use GeminiLabs\SiteReviews\Addon\Forms\Metaboxes\HelpMetabox;
use GeminiLabs\SiteReviews\Addon\Forms\Metaboxes\TemplateMetabox;
use GeminiLabs\SiteReviews\Addon\Forms\Metaboxes\TemplateTagsMetabox;
use GeminiLabs\SiteReviews\Addon\Forms\SearchForms;
use GeminiLabs\SiteReviews\Addons\Controller as AddonController;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Metaboxes\DetailsMetabox;
use GeminiLabs\SiteReviews\Modules\Sanitizer;
use GeminiLabs\SiteReviews\Request;
use GeminiLabs\SiteReviews\Review;
use GeminiLabs\SiteReviews\Role;

class Controller extends AddonController
{
    /**
     * {@inheritdoc}
     */
    protected $addon;

    /**
     * @return void
     * @action admin_enqueue_scripts
     */
    public function enqueueAdminAssets()
    {
        if ($this->isReviewAdminPage()) {
            $this->enqueueAsset('css', [
                'dependencies' => ['wp-codemirror'],
                'suffix' => 'admin',
            ]);
            $this->enqueueAsset('js', [
                'dependencies' => [glsr()->id.'/admin', 'backbone', 'wp-api-fetch', 'wp-theme-plugin-editor'],
                'suffix' => 'admin',
            ]);
            $codemirror = wp_enqueue_code_editor([
                'codemirror' => ['indentWithTabs' => false, 'lineWrapping' => false],
                'htmlhint' => ['space-tab-mixed-disabled' => 'space'],
                'type' => 'text/html',
            ]);
            wp_localize_script('jquery', 'cm_settings', ['codeEditor' => $codemirror]);
        }
    }

    /**
     * @return void
     * @action enqueue_block_editor_assets
     */
    public function enqueueBlockAssets()
    {
        // The admin dependency loads this before the Site Reviews blocks script as block filters must be loaded first.
        $this->enqueueAsset('js', [
            'dependencies' => [glsr()->id.'/blocks'],
            'suffix' => 'blocks',
        ]);
    }

    /**
     * @return void
     * @action wp_enqueue_scripts
     */
    public function enqueuePublicAssets()
    {
        parent::enqueuePublicAssets();
        $library = glsr_get_option('addons.'.Application::SLUG.'.dropdown_library');
        $loadLibraryFiles = glsr_get_option('addons.'.Application::SLUG.'.dropdown_assets');
        if ('choices.js' === $library && 'yes' === $loadLibraryFiles) {
            $version = $this->addon->filterString('library/'.$library, '10.1.0');
            $cssUrl = sprintf('https://cdn.jsdelivr.net/npm/choices.js@%s/public/assets/styles/choices.min.css', $version);
            $jsUrl = sprintf('https://cdn.jsdelivr.net/npm/choices.js@%s/public/assets/scripts/choices.min.js', $version);
            $script = $this->addon->filterString('enqueue/'.$library, 'GLSR.Event.on("site-reviews/loaded",function(){"undefined"!==typeof Choices&&document.querySelectorAll(".glsr select:not(.browser-default)").forEach(function(a){GLSR.addons["site-reviews-forms"].Choices = new Choices(a,GLSR.addons["site-reviews-forms"].choicesjs)})})');
            wp_enqueue_script(glsr()->id.'/choices', $jsUrl, [glsr()->id], $version, true);
            wp_enqueue_style(glsr()->id.'/choices', $cssUrl, [glsr()->id], $version);
            wp_add_inline_script(glsr()->id.'/choices', $script);
        }
    }

    /**
     * @return array
     * @filter site-reviews/block/form/attributes
     * @filter site-reviews/block/reviews/attributes
     * @filter site-reviews-images/block/images/attributes
     */
    public function filterBlockAttributes(array $attributes)
    {
        $attributes['form'] = [
            'default' => '',
            'type' => 'string',
        ];
        return $attributes;
    }

    /**
     * @param bool $useBlockEditor
     * @param string $postType
     * @return bool
     * @filter use_block_editor_for_post_type
     */
    public function filterBlockEditor($useBlockEditor, $postType)
    {
        if (Application::POST_TYPE === $postType) {
            return false;
        }
        return $useBlockEditor;
    }

    /**
     * @return array
     * @filter site-reviews/defaults/column-filterby/defaults
     */
    public function filterColumnFilterby(array $defaults)
    {
        $defaults['form'] = FILTER_SANITIZE_NUMBER_INT;
        return $defaults;
    }

    /**
     * @return void
     * @action pre_get_posts
     */
    public function filterColumnFilterQuery(\WP_Query $query)
    {
        if (!$this->hasQueryPermission($query)) {
            return;
        }
        if ($filterBy = filter_input(INPUT_GET, 'form', FILTER_SANITIZE_NUMBER_INT)) {
            $meta = new \WP_Meta_Query($query->get('meta_query'));
            $meta->queries[] = ['key' => '_custom_form', 'value' => $filterBy];
            $query->set('meta_query', $meta->queries);
        }
    }

    /**
     * @param array $columns
     * @return array
     * @action manage_{Application::POST_TYPE}_posts_columns
     */
    public function filterColumnsForPostType($columns)
    {
        return Arr::insertAfter('title', $columns, [
            'fields' => _x('Fields', 'Form table columns (admin-text)', 'site-reviews-forms'),
            'reviews' => _x('Reviews', 'Form table columns (admin-text)', 'site-reviews-forms'),
            'shortcode' => _x('Shortcode', 'Form table columns (admin-text)', 'site-reviews-forms'),
        ]);
    }

    /**
     * @return array
     */
    public function filterDocumentationFaq(array $sections)
    {
        $sections[] = glsr(Application::class)->file('faq/enable-optgroup');
        return $sections;
    }

    /**
     * @param \Elementor\Widget_Base $widget
     * @return array
     * @filter site-reviews/integration/elementor/register/controls
     */
    public function filterElementorWidgetControls(array $controls, $widget)
    {
        if (!in_array($widget->get_name(), ['site_reviews', 'site_reviews_form'])) {
            return $controls;
        }
        $option = [
            'default' => '',
            'label_block' => true,
            'options' => glsr(Application::class)->posts(),
            'type' => \Elementor\Controls_Manager::SELECT2,
        ];
        if ('site_reviews' === $widget->get_name()) {
            $option['label'] = _x('Use a Custom Form Review Template', 'admin-text', 'site-reviews-forms');
        } else {
            $option['label'] = _x('Use a Custom Form', 'admin-text', 'site-reviews-forms');
        }
        $options = $controls['settings']['options'];
        $options = Arr::prepend($options, $option, 'form');
        $controls['settings']['options'] = $options;
        return $controls;
    }

    /**
     * @return array
     * @filter site-reviews/defaults/listtable-filters
     */
    public function filterListtableFilters(array $filters)
    {
        $filters['form'] = ColumnFilterForm::class;
        return $filters;
    }

    /**
     * @return array
     * @filter site-reviews/enqueue/public/localize
     */
    public function filterLocalizedPublicVariables(array $variables)
    {
        $library = glsr_get_option('addons.'.Application::SLUG.'.dropdown_library');
        $loadLibraryFiles = glsr_get_option('addons.'.Application::SLUG.'.dropdown_assets');
        if ('choices.js' === $library && 'yes' === $loadLibraryFiles) {
            $variables['addons'][Application::ID] = [
                'choicesjs' => [
                    'itemSelectText' => '',
                    'position' => 'bottom',
                    'shouldSort' => true,
                ],
            ];
        }
        return $variables;
    }

    /**
     * @param array $actions
     * @param \WP_Post $post
     * @return array
     * @filter post_row_actions
     */
    public function filterRowActions($actions, $post)
    {
        if (Application::POST_TYPE === $post->post_type) {
            unset($actions['inline hide-if-no-js']); //Remove Quick-edit
            $action = ['id' => sprintf(_x('<span>ID: %d</span>', 'The Form Post ID (admin-text)', 'site-reviews-forms'), $post->ID)];
            return array_merge($action, $actions);
        }
        return $actions;
    }

    /**
     * @return array
     * @filter site-reviews/defer-scripts
     */
    public function filterScriptsDefer(array $handles)
    {
        $handles[] = glsr()->id.'/choices';
        return $handles;
    }

    /**
     * @return array
     * @filter site-reviews/defaults/site-reviews/defaults
     * @filter site-reviews/defaults/site-reviews-form/defaults
     * @filter site-reviews-images/defaults/site-reviews-images/defaults
     */
    public function filterShortcodeDefaults(array $defaults)
    {
        $defaults['form'] = '';
        return $defaults;
    }

    /**
     * @return array
     * @filter site-reviews/documentation/shortcodes/site_reviews
     */
    public function filterSiteReviewsDocumentation(array $paths)
    {
        $index = array_search('hide.php', array_map('basename', $paths));
        return Arr::insertBefore($index, $paths, [glsr(Application::class)->path('views/site_reviews/form.php')]);
    }

    /**
     * @return array
     * @filter site-reviews/documentation/shortcodes/site_reviews_form
     */
    public function filterSiteReviewsFormDocumentation(array $paths)
    {
        $index = array_search('hide.php', array_map('basename', $paths));
        $paths[$index] = glsr(Application::class)->path('views/site_reviews_form/hide.php');
        $paths = Arr::insertBefore($index, $paths, [glsr(Application::class)->path('views/site_reviews_form/form.php')]);
        return $paths;
    }

    /**
     * @return array
     * @filter site-reviews/documentation/shortcodes/site_reviews
     */
    public function filterSiteReviewsImagesDocumentation(array $paths)
    {
        // $index = array_search('hide.php', array_map('basename', $paths));
        // return Arr::insertBefore($index, $paths, [glsr(Application::class)->path('views/site_reviews_images/form.php')]);
        return $paths;
    }

    /**
     * @return void
     * @action {addon_id}/activate
     */
    public function install()
    {
        glsr(Role::class)->resetAll();
    }

    /**
     * Since this is an AJAX request, We need to set the global $post in order 
     * for `get_the_ID` to work in the "site-reviews/config/forms/metabox-fields" hook
     * 
     * @return void
     * @action site-reviews/route/ajax/metabox-details
     */
    public function metaboxDetailsAjax(Request $request)
    {
        global $post;
        $formId = glsr(Sanitizer::class)->sanitizeInt($request->form_id);
        $postId = glsr(Sanitizer::class)->sanitizeInt($request->post_id);
        $post = get_post($postId);
        update_post_meta($postId, '_custom_form', $formId);
        $review = glsr_get_review($postId);
        $fields = glsr(DetailsMetabox::class)->normalize($review);
        $results = array_reduce($fields, function ($carry, $field) {
            $field->disabled = false;
            return $carry.$field->build();
        });
        wp_send_json_success([
            'items' => $results,
        ]);
    }

    /**
     * @param \WP_Post $post
     * @return void
     * @action add_meta_boxes_{Application::POST_TYPE}
     */
    public function registerMetaBoxes($post)
    {
        glsr(FieldsMetabox::class)->register($post);
        glsr(HelpMetabox::class)->register($post);
        glsr(TemplateMetabox::class)->register($post);
        glsr(TemplateTagsMetabox::class)->register($post);
    }

    /**
     * @return void
     * @action init
     */
    public function registerPostType()
    {
        $this->execute(new RegisterPostType());
    }

    /**
     * @param string $column
     * @param int $postId
     * @return void
     * @action manage_{Application::POST_TYPE}_posts_custom_column
     */
    public function renderColumnValues($column, $postId)
    {
        if ('fields' === $column) {
            glsr(FieldsColumn::class, ['postId' => $postId])->render();
        }
        if ('reviews' === $column) {
            glsr(ReviewsColumn::class, ['postId' => $postId])->render();
        }
        if ('shortcode' === $column) {
            glsr(ShortcodeColumn::class, ['postId' => $postId])->render('site_reviews_form');
        }
    }

    /**
     * Manually change the position of the "All Forms" menu.
     * @return void
     * @action admin_menu
     */
    public function reorderMenu()
    {
        global $submenu;
        $prefix = 'edit.php?post_type=';
        if (empty($menu = $submenu[$prefix.glsr()->post_type])) {
            return;
        }
        $orderedMenu = [];
        $search = array_search($prefix.Application::POST_TYPE, wp_list_pluck($menu, 2));
        if (false === $search) {
            return;
        }
        foreach ($menu as $index => $page) {
            if ($prefix.Application::POST_TYPE !== $page[2]) {
                $orderedMenu[$index] = $page;
            }
            if ($prefix.glsr()->post_type === $page[2]) {
                $orderedMenu[$index + 1] = $menu[$search];
            }
        }
        $submenu[$prefix.glsr()->post_type] = $orderedMenu;
    }

    /**
     * @param int $postId
     * @return void
     * @action save_post_{Application::POST_TYPE}
     */
    public function saveMetaboxes($postId)
    {
        glsr(FieldsMetabox::class)->save($postId);
        glsr(TemplateMetabox::class)->save($postId);
    }

    /**
     * @return void
     * @action site-reviews/route/ajax/filter-form
     */
    public function searchFormsAjax(Request $request)
    {
        $search = glsr(Sanitizer::class)->sanitizeText($request->search);
        $results = glsr(SearchForms::class)->search($search)->results();
        wp_send_json_success([
            'items' => $results,
        ]);
    }

    /**
     * @return bool
     */
    protected function isReviewAdminPage()
    {
        return glsr()->isAdmin() 
            && (Application::POST_TYPE === get_post_type() || $this->isReviewEditor());
    }

    /**
     * {@inheritdoc}
     */
    protected function setAddon()
    {
        $this->addon = glsr(Application::class);
    }
}

<?php

namespace GeminiLabs\SiteReviews\Addon\Themes\Controllers;

use GeminiLabs\SiteReviews\Addon\Themes\Application;
use GeminiLabs\SiteReviews\Addon\Themes\Columns\ShortcodeColumn;
use GeminiLabs\SiteReviews\Addon\Themes\Commands\RegisterPostType;
use GeminiLabs\SiteReviews\Addon\Themes\Metaboxes\FormTagsMetabox;
use GeminiLabs\SiteReviews\Addon\Themes\Metaboxes\HelpMetabox;
use GeminiLabs\SiteReviews\Addon\Themes\Metaboxes\SubmitMetabox;
use GeminiLabs\SiteReviews\Addon\Themes\Template;
use GeminiLabs\SiteReviews\Addon\Themes\Theme;
use GeminiLabs\SiteReviews\Addons\Controller as AddonController;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Request;
use GeminiLabs\SiteReviews\Role;

class Controller extends AddonController
{
    protected $addon;

    /**
     * @action admin_enqueue_scripts
     */
    public function enqueueAdminAssets()
    {
        if ($this->isReviewAdminPage()) {
            $this->enqueueAsset('css', [
                'dependencies' => [
                    // 'wp-block-editor',
                    'wp-codemirror',
                    'wp-color-picker',
                    'wp-components',
                    'wp-edit-post',
                ],
                'suffix' => 'admin',
            ]);
            wp_register_script('glsr_wp-color-picker-alpha', glsr(Application::class)->url('assets/wp-color-picker-alpha.min.js'), ['wp-color-picker'], '3.0.0');
            $this->enqueueAsset('js', [
                'dependencies' => [glsr()->id.'/admin', 'backbone', 'glsr_wp-color-picker-alpha', 'wp-api-fetch', 'wp-theme-plugin-editor', 'wp-components'],
                'suffix' => 'admin',
            ]);
            $codemirror = wp_enqueue_code_editor([
                'codemirror' => ['indentWithTabs' => false, 'lineWrapping' => false],
                'htmlhint' => ['space-tab-mixed-disabled' => 'space'],
                'type' => 'text/html',
            ]);
            wp_localize_script('jquery', 'cm_settings', ['codeEditor' => $codemirror]);
            $this->enqueueSwiperAssets([Application::ID.'/admin']);
        }
    }

    /**
     * @action enqueue_block_editor_assets
     */
    public function enqueueBlockAssets()
    {
        $this->enqueueAsset('css', ['suffix' => 'blocks']);
        // The admin dependency loads this before the Site Reviews blocks script as block filters must be loaded first.
        $this->enqueueAsset('js', [
            'dependencies' => [glsr()->id.'/blocks'],
            'suffix' => 'blocks',
        ]);
        $this->enqueueSwiperAssets([glsr()->id.'/blocks']);
    }

    /**
     * @action wp_enqueue_scripts
     */
    public function enqueuePublicAssets()
    {
        parent::enqueuePublicAssets();
        if ('yes' === $this->addon->option('swiper_assets', 'yes')) {
            $this->enqueueSwiperAssets([glsr()->id]);
        }
    }

    /**
     * @return array
     * @filter site-reviews/block/form/attributes
     * @filter site-reviews/block/reviews/attributes
     * @filter site-reviews/block/summary/attributes
     * @filter site-reviews-images/block/images/attributes
     */
    public function filterBlockAttributes(array $attributes)
    {
        $attributes['theme'] = [
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
     * @param array $columns
     * @return array
     * @action manage_{Application::POST_TYPE}_posts_columns
     */
    public function filterColumnsForPostType($columns)
    {
        return Arr::insertAfter('title', $columns, [
            'shortcode' => _x('Shortcode', 'Theme table columns (admin-text)', 'site-reviews-themes'),
        ]);
    }

    /**
     * @param \Elementor\Widget_Base $widget
     * @return array
     * @filter site-reviews/integration/elementor/register/controls
     */
    public function filterElementorWidgetControls(array $controls, $widget)
    {
        $shortcodes = ['site_reviews', 'site_reviews_form', 'site_reviews_images', 'site_reviews_summary'];
        if (!in_array($widget->get_name(), $shortcodes)) {
            return $controls;
        }
        $option = [
            'default' => '',
            'label' => _x('Use a Custom Theme', 'admin-text', 'site-reviews-themes'),
            'label_block' => true,
            'options' => glsr(Application::class)->posts(),
            'type' => \Elementor\Controls_Manager::SELECT2,
        ];
        $options = $controls['settings']['options'];
        if (array_key_exists('form', $options)) {
            $option['description'] = _x('This overrides the Custom Form Review Template.', 'admin-text', 'site-reviews-themes');
        }
        $options = Arr::prepend($options, $option, 'theme');
        $controls['settings']['options'] = $options;
        return $controls;
    }

    /**
     * @return array
     * @filter site-reviews/enqueue/admin/localize
     */
    public function filterLocalizedAdminVariables(array $variables)
    {
        $variables['addons'][Application::ID] = [
            'error' => [
                'basic' => _x('Unable to load.', 'admin-text', 'site-reviews-themes'),
                'detailed' => _x('Unable to load the Theme Builder. Please read the Basic Troubleshooting steps on the Help page.', 'admin-text', 'site-reviews-themes'),
            ],
            // 'palettes' => ['#cccccc', '#ff6f31', '#ff9f02', '#ffcf02', '#9ace6a', '#57bb8a'],
            'palettes' => ['#dcdce6', '#ff3722', '#ff8622', '#ffce00', '#73cf11', '#00b67a'],
            'swiper' => $this->addon->option('swiper_library', 'splide'),
            'swipers' => [],
        ];
        $variables['nonce']['theme'] = wp_create_nonce('theme');
        $variables['nonce']['theme-tags'] = wp_create_nonce('theme-tags');
        return $variables;
    }

    /**
     * @return array
     * @filter site-reviews/enqueue/public/localize
     */
    public function filterLocalizedPublicVariables(array $variables)
    {
        $variables['addons'][Application::ID] = [
            'swiper' => $this->addon->option('swiper_library', 'splide'),
            'swipers' => [],
        ];
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
            unset($actions['inline hide-if-no-js']); // Remove Quick-edit
            $action = ['id' => sprintf(_x('<span>ID: %d</span>', 'The Theme Post ID (admin-text)', 'site-reviews-themes'), $post->ID)];
            return array_merge($action, $actions);
        }
        return $actions;
    }

    /**
     * @return array
     * @filter site-reviews/defaults/site-reviews/defaults
     * @filter site-reviews/defaults/site-reviews-form/defaults
     * @filter site-reviews/defaults/site-reviews-images/defaults
     * @filter site-reviews/defaults/site-reviews-summary/defaults
     */
    public function filterShortcodeDefaults(array $defaults)
    {
        $defaults['theme'] = '';
        return $defaults;
    }

    /**
     * @return array
     * @filter site-reviews/documentation/shortcodes/site_reviews
     */
    public function filterSiteReviewsDocumentation(array $paths)
    {
        // $index = array_search('hide.php', array_map('basename', $paths));
        // return Arr::insertBefore($index, $paths, [glsr(Application::class)->path('views/site_reviews/theme.php')]);
        return $paths;
    }

    /**
     * @return array
     * @filter site-reviews/documentation/shortcodes/site_reviews_form
     */
    public function filterSiteReviewsFormDocumentation(array $paths)
    {
        // $index = array_search('hide.php', array_map('basename', $paths));
        // return Arr::insertBefore($index, $paths, [glsr(Application::class)->path('views/site_reviews_form/theme.php')]);
        return $paths;
    }

    /**
     * @return array
     * @filter site-reviews/documentation/shortcodes/site_reviews_images
     */
    public function filterSiteReviewsImagesDocumentation(array $paths)
    {
        // $index = array_search('hide.php', array_map('basename', $paths));
        // return Arr::insertBefore($index, $paths, [glsr(Application::class)->path('views/site_reviews_images/theme.php')]);
        return $paths;
    }

    /**
     * @return array
     * @filter site-reviews/documentation/shortcodes/site_reviews_summary
     */
    public function filterSiteReviewsSummaryDocumentation(array $paths)
    {
        // $index = array_search('hide.php', array_map('basename', $paths));
        // return Arr::insertBefore($index, $paths, [glsr(Application::class)->path('views/site_reviews_summary/theme.php')]);
        return $paths;
    }

    /**
     * @return array
     * @filter site-reviews/addon/settings
     */
    public function filterWoocommerceSettings(array $settings)
    {
        if (!isset($settings['settings.addons.woocommerce.style'])) {
            return $settings;
        }
        $style = $settings['settings.addons.woocommerce.style'];
        $options = [];
        $options[_x('Styles', 'admin-text', 'site-reviews-themes')] = $style['options'];
        $options[_x('Themes', 'admin-text', 'site-reviews-themes')] = glsr(Application::class)->posts();
        $style['options'] = $options;
        $settings['settings.addons.woocommerce.style'] = $style;
        return $settings;
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
     * @param \WP_Post $post
     * @return void
     * @action add_meta_boxes_{Application::POST_TYPE}
     */
    public function registerMetaBoxes($post)
    {
        glsr(FormTagsMetabox::class)->register($post);
        glsr(HelpMetabox::class)->register($post);
        glsr(SubmitMetabox::class)->register($post);
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
     * @return void
     * @filter admin_notices
     */
    public function renderBetaNotice()
    {
        $screen = glsr_current_screen();
        $isCurrentScreen = Str::startsWith(glsr_current_screen()->post_type, glsr(Application::class)->post_type);
        if ($isCurrentScreen) {
            glsr(Application::class)->render('beta-notice');
        }
    }

    /**
     * @param string $column
     * @param int $postId
     * @return void
     * @action manage_{Application::POST_TYPE}_posts_custom_column
     */
    public function renderColumnValues($column, $postId)
    {
        if ('shortcode' === $column) {
            glsr(ShortcodeColumn::class, ['postId' => $postId])->render();
        }
    }

    /**
     * @param \WP_Post $post
     * @return void
     * @action edit_form_top
     */
    public function renderNotice($post)
    {
        if (Application::POST_TYPE !== $post->post_type) {
            return;
        }
        $formId = get_post_meta($post->ID, '_form', true);
        if (empty($formId)) {
            return;
        }
        if (!glsr()->addon('site-reviews-forms')) {
            echo glsr(Notice::class)
                ->addError(_x('This theme uses a custom form, but the Review Forms add-on is not activated.', 'admin-text', 'site-reviews-themes'))
                ->get();
            return;
        }
        $form = get_post($formId);
        if (empty($form) || $form->post_type !== glsr('site-reviews-forms')->post_type) {
            echo glsr(Notice::class)
                ->addError(_x('This theme uses a custom form, but the selected form no longer exists.', 'admin-text', 'site-reviews-themes'))
                ->get();
            return;
        }
    }

    /**
     * @return void
     * @action admin_footer
     */
    public function renderTemplates()
    {
        $screen = glsr_current_screen();
        if (Application::POST_TYPE === $screen->id && Application::POST_TYPE === $screen->post_type) {
            glsr(Template::class)->render('views/templates', [
                'context' => [ // $this->mockReviewFields()
                    'assigned_links' => '|assigned_links|',
                    'author' => '|author|',
                    'avatar' => '|avatar|',
                    'content' => '|content|',
                    'date' => '|date|',
                    'rating' => '|rating|',
                    'response' => '|response|',
                    'title' => '|title|',
                ],
            ]);
            glsr(Application::class)->action('templates'); // allows addons to add custom field templates
        }
    }

    /**
     * @param \WP_Post $post
     * @return void
     * @action edit_form_after_editor
     */
    public function renderTheme($post)
    {
        if (Application::POST_TYPE === get_post_type($post)) {
            $formId = get_post_meta($post->ID, '_form', true);
            glsr()->render(Application::ID.'/views/metabox-theme');
        }
    }

    /**
     * Manually change the position of the "All Themes" menu.
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
                $orderedMenu[$index + 2] = $menu[$search];
            }
        }
        $submenu[$prefix.glsr()->post_type] = $orderedMenu;
    }

    /**
     * @return void
     * @action site-reviews/route/ajax/theme
     */
    public function themeAjax(Request $request)
    {
        $theme = glsr(Theme::class, [
            'formId' => $request->formid,
            'themeId' => $request->postid,
        ]);
        wp_send_json_success([
            'reviews' => $theme->reviews(),
            'settings' => $theme->settings(),
            'stars' => $theme->stars(),
            'tags' => $theme->tags(),
            'theme' => [ // order is intentional
                'preview' => $theme->preview(),
                'builder' => $theme->builder(),
            ],
        ]);
    }

    /**
     * @return void
     * @action site-reviews/route/ajax/theme-tags
     */
    public function themeTagsAjax(Request $request)
    {
        $theme = glsr(Theme::class, [
            'formId' => $request->formid,
            'themeId' => $request->postid,
        ]);
        wp_send_json_success([
            'reviews' => $theme->reviews(),
            'tags' => $theme->tags(),
        ]);
    }

    /**
     * @return void
     */
    protected function enqueueSwiperAssets(array $dependencies)
    {
        $libraries = [
            'splide' => [
                'script' => 'https://cdn.jsdelivr.net/npm/@splidejs/splide@%s/dist/js/splide.min.js',
                'style' => 'https://cdn.jsdelivr.net/npm/@splidejs/splide@%s/dist/css/splide-core.min.css',
                'version' => '4.0',
            ],
            'swiper' => [
                'script' => 'https://cdn.jsdelivr.net/npm/swiper@%s/swiper-bundle.min.js',
                // 'style' => 'https://cdn.jsdelivr.net/npm/swiper@%s/swiper-bundle.min.css',
                'style' => '',
                'version' => '8.3',
            ],
        ];
        $library = $this->addon->option('swiper_library', 'splide');
        if (array_key_exists($library, $libraries)) {
            extract($libraries[$library]);
            $handle = glsr()->id.'/'.$library;
            if (!empty($script)) {
                wp_enqueue_script($handle, sprintf($script, $version), $dependencies, $version, true);
            }
            if (!empty($style)) {
                wp_enqueue_style($handle, sprintf($style, $version), $dependencies, $version);
            }
        }
    }

    /**
     * @return bool
     */
    protected function isReviewAdminPage()
    {
        return glsr()->isAdmin() && Application::POST_TYPE === get_post_type();
    }

    /**
     * @return void
     */
    protected function setAddon()
    {
        $this->addon = glsr(Application::class);
    }
}

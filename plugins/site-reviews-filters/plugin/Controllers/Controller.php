<?php

namespace GeminiLabs\SiteReviews\Addon\Filters\Controllers;

use GeminiLabs\SiteReviews\Addon\Filters\Application;
use GeminiLabs\SiteReviews\Addon\Filters\Blocks\SiteReviewsFiltersBlock;
use GeminiLabs\SiteReviews\Addon\Filters\Integrations\Elementor\ElementorFilterWidget;
use GeminiLabs\SiteReviews\Addon\Filters\Shortcodes\SiteReviewsFilterShortcode;
use GeminiLabs\SiteReviews\Addon\Filters\SqlAnd;
use GeminiLabs\SiteReviews\Addon\Filters\SqlJoin;
use GeminiLabs\SiteReviews\Addon\Filters\SqlOrderBy;
use GeminiLabs\SiteReviews\Addon\Filters\Tinymce\SiteReviewsFilterTinymce;
use GeminiLabs\SiteReviews\Addon\Filters\Widgets\SiteReviewsFilterWidget;
use GeminiLabs\SiteReviews\Addons\Controller as AddonController;
use GeminiLabs\SiteReviews\Defaults\SiteReviewsDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Request;
use GeminiLabs\SiteReviews\Reviews;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsShortcode;

class Controller extends AddonController
{
    /**
     * {@inheritdoc}
     */
    protected $addon;

    /**
     * @return mixed
     * @action site-reviews/route/ajax/fetch-filtered-reviews
     */
    public function fetchFilteredReviewsAjax(Request $request)
    {
        glsr()->store(glsr()->paged_handle, $request);
        $html = glsr(SiteReviewsShortcode::class)->buildReviewsHtmlFromArgs(
            $request->cast('atts', 'array')
        );
        $response = [
            'pagination' => $html->getPagination($wrap = true),
            'reviews' => $html->getReviews(),
            'status' => glsr(SiteReviewsFilterShortcode::class)->buildTemplateTag('status'),
        ];
        wp_send_json_success($response);
    }

    /**
     * @return array
     * @filter site-reviews/enqueue/admin/localize
     */
    public function filterAdminLocalizedVariables(array $variables)
    {
        $variables = Arr::set($variables, 'hideoptions.site_reviews_filter',
            glsr(SiteReviewsFilterShortcode::class)->getHideOptions()
        );
        return $variables;
    }

    /**
     * @return array
     * @filter site-reviews-filters/config/forms/filters-form
     */
    public function filterConfigFiltersForm(array $config)
    {
        $style = glsr_get_option('general.style', 'default');
        $terms = $this->addon->categories(); // @phpstan-ignore-line
        if (!empty($terms)) {
            $config['filter_by_term'] = [
                'options' => ['' => _x('All Categories', 'filter-by option', 'site-reviews-filters')] + $terms,
                'type' => 'select',
                'value' => filter_input(INPUT_GET, 'filter_by_term', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
            ];
        }
        if ('divi' === $style) {
            $config['search_for']['class'] = 'search-field et_pb_s';
        }
        return $config;
    }

    /**
     * @return array
     * @filter site-reviews/documentation/shortcodes
     */
    public function filterDocumentationShortcodes(array $sections)
    {
        $sections[] = $this->addon->path('views/shortcode.php');
        return $sections;
    }

    /**
     * @return array
     * @filter site-reviews/enqueue/public/localize
     */
    public function filterLocalizedPublicVariables(array $variables)
    {
        $variables['addons'][Application::ID] = [
            'filters' => [],
        ];
        return $variables;
    }

    /**
     * @param string $handle
     * @return array
     * @filter site-reviews/query/sql/and
     */
    public function filterQuerySqlAnd(array $and, $handle)
    {
        return in_array($handle, ['query-total-reviews', 'query-review-ids'])
            ? glsr(SqlAnd::class)->modify($and)
            : $and;
    }

    /**
     * @param string $handle
     * @return array
     * @filter site-reviews/query/sql/join
     */
    public function filterQuerySqlJoin(array $join, $handle)
    {
        return in_array($handle, ['query-total-reviews', 'query-review-ids'])
            ? glsr(SqlJoin::class)->modify($join)
            : $join;
    }

    /**
     * @param string $handle
     * @return array
     * @filter site-reviews/query/sql/order-by
     */
    public function filterQuerySqlOrderBy(array $orderby, $handle)
    {
        return in_array($handle, ['query-review-ids', 'query-reviews'])
            ? glsr(SqlOrderBy::class)->modify($orderby)
            : $orderby;
    }

    /**
     * @return array
     * @filter site-reviews/style/views
     */
    public function filterStyleViews(array $views)
    {
        return Arr::unique(array_merge($views, [
            Application::ID.'/templates/reviews-filter',
        ]));
    }

    /**
     * @return array
     * @filter site-reviews/router/admin/unguarded-actions
     * @filter site-reviews/router/public/unguarded-actions
     */
    public function filterUnguardedActions(array $actions)
    {
        $actions[] = 'fetch-filtered-reviews';
        return $actions;
    }

    /**
     * @return void
     * @action init
     */
    public function registerBlocks()
    {
        glsr(SiteReviewsFiltersBlock::class)->register('filters');
    }

    /**
     * @param \Elementor\Widgets_Manager $manager
     * @return void
     * @action elementor/widgets/register
     */
    public function registerElementorWidgets($manager)
    {
        $manager->register(new ElementorFilterWidget());
    }

    /**
     * @return void
     * @action init
     */
    public function registerShortcodes()
    {
        add_shortcode('site_reviews_filter', [glsr(SiteReviewsFilterShortcode::class), 'buildShortcode']);
    }

    /**
     * @return void
     * @action admin_init
     */
    public function registerTinymcePopups()
    {
        $label = esc_html_x('Filter Reviews', 'admin-text', 'site-reviews-filters');
        $shortcode = glsr(SiteReviewsFilterTinymce::class)->register('site_reviews_filter', [
            'label' => $label,
            'title' => $label,
        ]);
        glsr()->append('mce', $shortcode->properties, 'site_reviews_filter');
    }

    /**
     * @return void
     * @action widgets_init
     */
    public function registerWidgets()
    {
        register_widget(SiteReviewsFilterWidget::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function setAddon()
    {
        $this->addon = glsr(Application::class);
    }
}

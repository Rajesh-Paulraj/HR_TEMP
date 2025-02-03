<?php

namespace GeminiLabs\SiteReviews\Addon\Filters;

use GeminiLabs\SiteReviews\Addon\Filters\Controllers\Controller;
use GeminiLabs\SiteReviews\Addon\Filters\Controllers\ReviewsController;
use GeminiLabs\SiteReviews\Addon\Filters\Controllers\SummaryController;
use GeminiLabs\SiteReviews\Addons\Hooks as AddonHooks;

class Hooks extends AddonHooks
{
    protected $reviews;
    protected $summary;

    /**
     * @return void
     */
    public function run()
    {
        parent::run();

        add_action('site-reviews/route/ajax/fetch-filtered-reviews', [$this->controller, 'fetchFilteredReviewsAjax']);
        add_filter('site-reviews/enqueue/admin/localize', [$this->controller, 'filterAdminLocalizedVariables']);
        add_filter('site-reviews-filters/config/forms/filters-form', [$this->controller, 'filterConfigFiltersForm']);
        add_filter('site-reviews/documentation/shortcodes', [$this->controller, 'filterDocumentationShortcodes']);
        add_filter('site-reviews/enqueue/public/localize', [$this->controller, 'filterLocalizedPublicVariables']);
        add_filter('site-reviews/query/sql/and', [$this->controller, 'filterQuerySqlAnd'], 20, 2);
        add_filter('site-reviews/query/sql/join', [$this->controller, 'filterQuerySqlJoin'], 20, 2);
        add_filter('site-reviews/query/sql/order-by', [$this->controller, 'filterQuerySqlOrderBy'], 20, 2);
        add_filter('site-reviews/style/views', [$this->controller, 'filterStyleViews']);
        add_filter('site-reviews/router/admin/unguarded-actions', [$this->controller, 'filterUnguardedActions']);
        add_filter('site-reviews/router/public/unguarded-actions', [$this->controller, 'filterUnguardedActions']);
        add_action('elementor/widgets/register', [$this->controller, 'registerElementorWidgets']);

        add_filter('site-reviews/block/reviews/attributes', [$this->reviews, 'filterBlockAttributes']);
        add_filter('site-reviews/shortcode/display-options', [$this->reviews, 'filterDisplayOptions'], 10, 2);
        add_filter('site-reviews/documentation/shortcodes/site_reviews', [$this->reviews, 'filterDocumentation']);
        add_filter('site-reviews/integration/elementor/register/controls', [$this->reviews, 'filterElementorWidgetControls'], 10, 2);
        add_filter('site-reviews/integration/elementor/display/settings', [$this->reviews, 'filterElementorWidgetDisplaySettings'], 10, 2);
        add_filter('site-reviews/shortcode/atts', [$this->reviews, 'filterShortcodeAttributes'], 10, 3);
        add_filter('site-reviews/defaults/site-reviews/casts', [$this->reviews, 'filterShortcodeCasts']);
        add_filter('site-reviews/defaults/site-reviews/defaults', [$this->reviews, 'filterShortcodeDefaults']);
        add_filter('site-reviews/rendered/template/reviews', [$this->reviews, 'filterTemplate'], 10, 2);
        add_action('site-reviews/review/build/before', [$this->reviews, 'highlightSearchResults']);

        add_filter('site-reviews/block/summary/attributes', [$this->summary, 'filterBlockAttributes']);
        add_filter('site-reviews/documentation/shortcodes/site_reviews_summary', [$this->summary, 'filterDocumentation']);
        add_filter('site-reviews/integration/elementor/register/controls', [$this->summary, 'filterElementorWidgetControls'], 10, 2);
        add_filter('site-reviews/defaults/site-reviews-summary/defaults', [$this->summary, 'filterShortcodeDefaults']);
        add_filter('site-reviews/summary/build/percentages', [$this->summary, 'filterSummaryPercentagesTag'], 10, 3);
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
        $this->reviews = glsr(ReviewsController::class);
        $this->summary = glsr(SummaryController::class);
        return glsr(Controller::class);
    }
}

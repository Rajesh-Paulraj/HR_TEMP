<?php

namespace GeminiLabs\SiteReviews\Addon\Filters\Shortcodes;

use GeminiLabs\SiteReviews\Addon\Filters\Application;
use GeminiLabs\SiteReviews\Addon\Filters\Defaults\FilteredDefaults;
use GeminiLabs\SiteReviews\Addon\Filters\Template;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Html\Attributes;
use GeminiLabs\SiteReviews\Modules\Style;
use GeminiLabs\SiteReviews\Shortcodes\Shortcode;

class SiteReviewsFilterShortcode extends Shortcode
{
    /**
     * @var array
     */
    public $args;

    /**
     * @return void|string
     */
    public function buildForm()
    {
        if (!$this->isHidden()) {
            return glsr(Template::class)->build('templates/filters-form', [
                'args' => $this->args,
                'context' => [
                    'attributes' => $this->getDataAttributes(),
                    'class' => $this->getClasses(),
                    'filter_by' => $this->buildTemplateTag('filter_by'),
                    'search_for' => $this->buildTemplateTag('search_for'),
                    'sort_by' => $this->buildTemplateTag('sort_by'),
                ],
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildTemplate(array $args = [])
    {
        $this->args = $args;
        return glsr(Template::class)->build('templates/reviews-filter', [
            'args' => $args,
            'context' => [
                'class' => 'glsr-filters',
                'form' => $this->buildForm(),
                'status' => $this->buildTemplateTag('status'),
            ],
        ]);
    }

    /**
     * @param string $tag
     * @return false|string
     */
    public function buildTemplateTag($tag)
    {
        $args = $this->args;
        $className = Helper::buildClassName($tag.'-tag', 'Addon\Filters\Tags');
        $field = class_exists($className)
            ? glsr($className, compact('tag', 'args'))->handleFor(Application::SLUG)
            : null;
        return glsr(Application::class)->filterString(Application::SLUG.'/build/'.$tag, $field, $this);
    }

    /**
     * @return array
     */
    protected function displayOptions()
    {
        $terms = glsr(Application::class)->categories();
        $options = [
            'filter_by_rating' => _x('Display the rating filter', 'admin-text', 'site-reviews-filters'),
            'search_for' => _x('Display the search bar', 'admin-text', 'site-reviews-filters'),
            'sort_by' => _x('Display the sort by', 'admin-text', 'site-reviews-filters'),
        ];
        if (!empty($terms)) {
            $options['filter_by_term'] = _x('Display the category filter', 'admin-text', 'site-reviews-filters');
        }
        natsort($options);
        return $options;
    }

    /**
     * @return string
     */
    protected function getClasses()
    {
        $classes = [
            'glsr-filters-form',
            glsr(Style::class)->classes('form'),
            $this->args['class'],
        ];
        return trim(implode(' ', array_filter($classes)));
    }

    /**
     * @return string
     */
    protected function getDataAttributes()
    {
        return glsr(Attributes::class)
            ->set(glsr(FilteredDefaults::class)->dataAttributes())
            ->toString();
    }

    /**
     * @return array
     */
    protected function hideOptions()
    {
        return [
            'filter_by_term' => _x('Hide the category filter', 'admin-text', 'site-reviews-filters'),
            'filter_by_rating' => _x('Hide the rating filter', 'admin-text', 'site-reviews-filters'),
            'search_for' => _x('Hide the search bar', 'admin-text', 'site-reviews-filters'),
            'sort_by' => _x('Hide the sort by', 'admin-text', 'site-reviews-filters'),
        ];
    }

    /**
     * @return bool
     */
    protected function isHidden()
    {
        return count($this->args['hide']) === count(glsr(FilteredDefaults::class)->defaults());
    }
}

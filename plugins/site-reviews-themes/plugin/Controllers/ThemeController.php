<?php

namespace GeminiLabs\SiteReviews\Addon\Themes\Controllers;

use GeminiLabs\SiteReviews\Addon\Themes\Application;
use GeminiLabs\SiteReviews\Addon\Themes\Fields\Rating;
use GeminiLabs\SiteReviews\Addon\Themes\Html;
use GeminiLabs\SiteReviews\Addon\Themes\Partials\StarRating;
use GeminiLabs\SiteReviews\Addon\Themes\Style;
use GeminiLabs\SiteReviews\Addon\Themes\Tags\AvatarTag;
use GeminiLabs\SiteReviews\Addon\Themes\Tags\ContentTag;
use GeminiLabs\SiteReviews\Addon\Themes\Theme;
use GeminiLabs\SiteReviews\Addon\Themes\ThemeBuilder;
use GeminiLabs\SiteReviews\Addon\Themes\ThemeSettings;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Html\Partials\StarRating as StarRatingPartial;
use GeminiLabs\SiteReviews\Modules\Html\ReviewHtml;

class ThemeController
{
    /**
     * @return string
     * @filter site-reviews/builder/field/themed-rating
     */
    public function filterFieldThemedRating()
    {
        return Rating::class;
    }

    /**
     * @param array $fields
     * @param \GeminiLabs\SiteReviews\Arguments $args
     * @return array[]
     * @filter site-reviews/review-form/fields
     */
    public function filterReviewFormFields($fields, $args)
    {
        if ($this->isTheme($args->theme)) {
            foreach ($fields as &$field) {
                if ('rating' === $field['type']) {
                    $field['theme'] = $args->theme;
                    $field['type'] = 'themed-rating';
                }
            }
        }
        return $fields;
    }

    /**
     * @param string $template
     * @return array
     * @filter site-reviews/interpolate/reviews
     */
    public function filterReviewsContext(array $context, $template, array $data)
    {
        $themeId = Arr::get($data, 'args.theme');
        $layout = glsr(ThemeSettings::class)->themeId($themeId)->get('presentation.layout');
        if (!empty($layout)) {
            $displayAs = Arr::get($layout, 'display_as');
            if ('carousel' === $displayAs) {
                $options = glsr(Theme::class)->themeId($themeId)->swiperParameters();
                $context['class'] = '';
                $context['options'] = json_encode($options, JSON_HEX_APOS|JSON_HEX_QUOT|JSON_NUMERIC_CHECK);
            } elseif ('grid' === $displayAs) {
                $context['class'] .= sprintf(' gl-grid-%s', Arr::get($layout, 'max_columns'));
            }
        }
        return $context;
    }

    /**
     * @param string $value
     * @param \GeminiLabs\SiteReviews\Modules\Html\ReviewHtml $reviewHtml
     * @return string
     * @filter site-reviews/reviews/html/theme
     */
    public function filterReviewsHtmlTheme($value, $reviewHtml)
    {
        if ($themeId = Arr::get($reviewHtml, 'args.theme')) {
            return glsr(Style::class)->themeId($themeId)->get()->toString();
        }
        return $value;
    }

    /**
     * @param string $template
     * @return string
     * @filter site-reviews/build/template/reviews
     */
    public function filterReviewsTemplate($template, array $data)
    {
        $themeId = Arr::get($data, 'args.theme');
        $settings = glsr(ThemeSettings::class)->themeId($themeId);
        $displayAs = $settings->get('presentation.layout.display_as');
        if (empty($displayAs)) {
            return $template;
        }
        if ('modal' === $settings->get('presentation.excerpt.excerpt_action')) {
            glsr()->store('use_modal', true);
        }
        if ('carousel' === $displayAs) {
            $swiper = glsr(Application::class)->option('swiper_library', 'splide');
            return glsr(Application::class)->build('templates/reviews-'.$swiper);
        }
        return glsr(Application::class)->build('templates/reviews-'.$displayAs);
    }

    /**
     * @param string $template
     * @return string
     * @filter site-reviews/build/template/review
     */
    public function filterReviewTemplate($template, array $data)
    {
        $themeId = Arr::get($data, 'args.theme');
        if ($themeTemplate = glsr(Html::class)->build($themeId)) {
            return $themeTemplate;
        }
        return $template;
    }

    /**
     * @param \GeminiLabs\SiteReviews\Shortcodes\Shortcode $shortcode
     * @return array
     * @filter site-reviews/shortcode/site_reviews/attributes
     * @filter site-reviews/shortcode/site_reviews_images/attributes
     * @filter site-reviews/shortcode/site_reviews_form/attributes
     * @filter site-reviews/shortcode/site_reviews_summary/attributes
     */
    public function filterShortcodeAttributes(array $attributes, $shortcode)
    {
        return $this->modifyShortcodeAttributes($attributes, $shortcode);
    }

    /**
     * @param string $className
     * @param string $path
     * @return string
     * @filter site-reviews/partial/classname
     */
    public function filterStarRatingPartial($className, $path, array $data)
    {
        if (StarRatingPartial::class == $className) {
            if ($this->isTheme(Arr::get($data, 'args.theme'))) {
                return StarRating::class;
            }
        }
        return $className;
    }

    /**
     * @param string $className
     * @return string
     * @filter site-reviews/review/tag/avatar
     */
    public function filterTagAvatar($className, ReviewHtml $reviewHtml)
    {
        $themeId = Arr::get($reviewHtml->args, 'theme');
        if ($this->isTheme($themeId)) {
            return AvatarTag::class;
        }
        return $className;
    }

    /**
     * @param string $className
     * @return string
     * @filter site-reviews/review/tag/content
     */
    public function filterTagContent($className, ReviewHtml $reviewHtml)
    {
        $themeId = Arr::get($reviewHtml->args, 'theme');
        if ($this->isTheme($themeId)) {
            return ContentTag::class;
        }
        return $className;
    }

    /**
     * @param int $postId
     * @return void
     * @action save_post_{Application::POST_TYPE}
     */
    public function saveTheme($postId)
    {
        $params = ['themeId' => $postId];
        update_post_meta($postId, '_form', filter_input(INPUT_POST, 'form'));
        if ($settings = filter_input(INPUT_POST, 'theme_settings')) {
            $settings = Cast::toArray(json_decode($settings, true));
            glsr(ThemeSettings::class, $params)->save($settings);
        }
        if ($builder = filter_input(INPUT_POST, 'theme_builder')) {
            $builder = Cast::toArray(json_decode($builder, true));
            glsr(ThemeBuilder::class, $params)->save($builder);
        }
    }

    /**
     * @return bool
     */
    protected function isTheme($postId)
    {
        return Application::POST_TYPE === get_post_type(Cast::toInt($postId));
    }

    /**
     * @param \GeminiLabs\SiteReviews\Shortcodes\Shortcode $shortcode
     * @return array
     */
    protected function modifyShortcodeAttributes(array $attributes, $shortcode)
    {
        if ($themeId = Arr::get($shortcode, 'args.theme')) {
            $style = glsr(Style::class)->themeId($themeId);
            if ('site_reviews_form' === $shortcode->shortcode) {
                $style->only([
                    '--gl-rating-color-0', '--gl-rating-color-1', '--gl-rating-color-2',
                    '--gl-rating-color-3', '--gl-rating-color-4', '--gl-rating-color-5',
                ]);
            }
            if ('site_reviews_summary' === $shortcode->shortcode) {
                $style->only([
                    '--gl-rating-color-0', '--gl-rating-color-1', '--gl-rating-color-2',
                    '--gl-rating-color-3', '--gl-rating-color-4', '--gl-rating-color-5',
                    '--gl-rating-size',
                ]);
            }
            $attributes['style'] = $style->toString();
        }
        return $attributes;
    }
}

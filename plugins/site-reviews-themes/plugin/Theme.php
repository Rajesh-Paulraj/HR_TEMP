<?php

namespace GeminiLabs\SiteReviews\Addon\Themes;

use GeminiLabs\SiteReviews\Addon\Themes\Application;
use GeminiLabs\SiteReviews\Addon\Themes\Defaults\TagDefaults;
use GeminiLabs\SiteReviews\Addon\Themes\Mock\MockTag;
use GeminiLabs\SiteReviews\Addon\Themes\ThemeBuilder;
use GeminiLabs\SiteReviews\Addon\Themes\ThemeSettings;
use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;

class Theme
{
    /**
     * @var int
     */
    public $formId;

    /**
     * @var int
     */
    public $themeId;

    public function __construct($formId = 0, $themeId = 0)
    {
        $this->formId($formId);
        $this->themeId($themeId);
    }

    /**
     * Get the saved Theme Builder data
     * @return array
     */
    public function builder()
    {
        return [
            'data' => [
                'fields' => glsr(ThemeBuilder::class, $this->params())->toArray(),
                'styles' => $this->presets(),
            ],
            'label' => _x('Builder', 'admin-text', 'site-reviews-themes'),
        ];
    }

    /**
     * @return array[]
     * @uses static::formId
     */
    public function defaultTags()
    {
        $defaults = [];
        if (!glsr()->addon('site-reviews-forms') || empty($this->formId)) {
            $defaults = [
                'content' => 'review_content',
                'rating' => 'review_rating',
                'title' => 'review_title',
            ];
            if (glsr()->addon('site-reviews-images')) {
                $defaults['images'] = 'review_images';
            }
        }
        $tags = glsr(TagDefaults::class)->merge($defaults);
        array_walk($tags, function (&$type, $tag) {
            $type = compact('tag', 'type');
        });
        return array_values($tags);
    }

    /**
     * @param int $postId
     * @return static
     */
    public function formId($postId)
    {
        $this->formId = Cast::toInt($postId);
        return $this;
    }

    /**
     * @return array
     */
    public function forms()
    {
        $forms = ['' => _x('Default Form', 'admin-text', 'site-reviews-themes')];
        if ($addon = glsr()->addon('site-reviews-forms')) {
            $forms += glsr($addon)->forms();
            natcasesort($forms);
        }
        return $forms;
    }

    /**
     * Get the style presets
     * @return array
     */
    public function presets()
    {
        $presets = [];
        $settings = Arr::convertFromDotNotation(glsr(Application::class)->config('theme-settings'));
        $options = Arr::consolidate(Arr::get($settings, 'presentation.layout.appearance.options'));
        foreach ($options as $style => $label) {
            if ($config = glsr(Application::class)->config('presets/'.$style)) {
                $presets[$style] = Arr::convertFromDotNotation($config);
            }
        }
        return $presets;
    }

    /**
     * Get the Theme Preview data
     * @return array
     */
    public function preview()
    {
        return [
            'data' => [], // leave empty for now
            'label' => _x('Preview', 'admin-text', 'site-reviews-themes'),
        ];
    }

    /**
     * Get an array of reviews to use in the Theme Preview
     * @return array[]
     */
    public function reviews()
    {
        $data = [];
        $reviews = glsr_get_reviews([
            'display' => 12,
            'form' => $this->formId,
            'raw' => true, // don't wrap fields and ignore hide options
            'theme' => $this->themeId,
        ]);
        $html = $reviews->build();
        $tags = array_fill_keys(wp_list_pluck($this->tags(), 'tag'), '');
        foreach ($html->rendered as $review) {
            $context = array_intersect_key($review->context, $tags);
            $data[] = $context;
        }
        return $data;
    }

    /**
     * Get the saved Theme Settings
     * @return array
     */
    public function settings()
    {
        $settings = glsr(ThemeSettings::class, $this->params())->settings();
        return [
            'presentation' => [
                'data' => Arr::get($settings, 'presentation', []),
                'label' => _x('Presentation', 'admin-text', 'site-reviews-themes'),
            ],
            'design' => [
                'data' => Arr::get($settings, 'design', []),
                'label' => _x('Design', 'admin-text', 'site-reviews-themes'),
            ],
        ];
    }

    /**
     * Get all of the available SVG star images
     * @return array
     */
    public function stars()
    {
        $images = [];
        $dir = glsr(Application::class)->path('assets/images/rating');
        if (is_dir($dir)) {
            $iterator = new \DirectoryIterator($dir);
            foreach ($iterator as $fileinfo) {
                if ($fileinfo->isFile() && 'svg' === $fileinfo->getExtension()) {
                    $slug = $fileinfo->getBasename('.svg');
                    ob_start();
                    include $fileinfo->getPathname();
                    $image = ob_get_clean();
                    $images[$slug] = $image;
                    if (Str::endsWith($slug, '-5')) {
                        $altSlug = substr($slug, 0, -2);
                        $images[$altSlug] = $image;
                    }
                }
            }
        }
        ksort($images, SORT_NATURAL);
        return $images;
    }

    /**
     * @return array
     */
    public function swiperParameters()
    {
        $layout = glsr(ThemeSettings::class, $this->params())->get('presentation.layout');
        $library = glsr(Application::class)->option('swiper_library', 'splide');
        $spacing = Cast::toInt(Arr::get($layout, 'spacing'));
        $maxSlides = Cast::toInt(Arr::get($layout, 'max_slides'));
        $maxSlides = Helper::ifEmpty($maxSlides, 6, true);
        if ('swiper' === $library) {
            $breakpoints = [];
            $minWidth = 320;
            for ($i = 1; $i <= $maxSlides; $i++) { 
                $breakpoints[$minWidth * $i] = ['slidesPerView' => $i];
            }
            return [
                'breakpoints' => $breakpoints,
                'spaceBetween' => $spacing,
            ];
        }
        if ('splide' === $library) {
            return [
                'gap' => $spacing,
                'maxslides' => $maxSlides,
            ];
        }
        return [];
    }

    /**
     * @return array[]
     * @uses static::formId
     */
    public function tags()
    {
        $fields = $this->defaultTags();
        if (glsr()->addon('site-reviews-forms') && !empty($this->formId)) {
            $formFields = glsr('Addon\Forms\FormFields')->indexedFields($this->formId);
            $formFields = array_filter($formFields, function ($field) {
                return !empty($field['tag']);
            });
            foreach ($fields as $field) {
                if (false === array_search($field['tag'], array_column($formFields, 'tag'))) {
                    $formFields[] = $field;
                }
            }
            $fields = $formFields;
        }
        array_walk($fields, function (&$field) {
            $field = (new MockTag($field))->toArray();
        });
        $fields = array_values(array_unique($fields, SORT_REGULAR));
        $tags = wp_list_pluck($fields, 'tag');
        array_multisort($tags, SORT_ASC, $fields);
        return $fields;
    }

    /**
     * @param int $postId
     * @return static
     */
    public function themeId($postId)
    {
        $this->themeId = Cast::toInt($postId);
        return $this;
    }

    /**
     * @return array
     * @uses static::formId
     * @uses static::themeId
     */
    protected function params()
    {
        return [
            'formId' => $this->formId,
            'themeId' => $this->themeId,
        ];
    }
}

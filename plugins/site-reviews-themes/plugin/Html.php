<?php

namespace GeminiLabs\SiteReviews\Addon\Themes;

use GeminiLabs\SiteReviews\Addon\Themes\Application;
use GeminiLabs\SiteReviews\Addon\Themes\Defaults\HtmlAttributesDefaults;
use GeminiLabs\SiteReviews\Addon\Themes\Style;
use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Html\Builder;

class Html
{
    /**
     * @var array
     */
    protected $attributes = [];

    public function __construct()
    {
        $this->attributes = Arr::convertFromDotNotation(
            glsr(HtmlAttributesDefaults::class)->defaults()
        );
    }

    /**
     * @param int $themeId
     * @return string|void
     */
    public function build($themeId)
    {
        if ($data = glsr(ThemeBuilder::class)->themeId($themeId)->toArray()) {
            $el = glsr()->args(Arr::get($data, 0));
            $html = glsr(Builder::class)->div(wp_parse_args($this->attributes($el), [
                'text' => $this->children($el->children),
            ]));
            $attributes = [
                'class' => 'glsr-review',
                'data-assigned' => '{{ assigned }}',
                'text' => $html,
            ];
            if ('carousel' !== glsr(ThemeSettings::class)->themeId($themeId)->get('presentation.layout.display_as')) {
                $attributes['id'] = 'review-{{ review_id }}';
                return glsr(Builder::class)->div($attributes);
            }
            $attributes['data-id'] = '{{ review_id }}';
            $attributes['style'] = glsr(Style::class)->themeId($themeId)->slideMargin();
            $classes = 'gl-slide';
            if ('splide' === glsr(Application::ID)->option('swiper_library', 'splide')) {
                $classes = sprintf('splide__slide %s', $classes);
            }
            return glsr(Builder::class)->div([
                'class' => $classes,
                'text' => glsr(Builder::class)->div($attributes),
            ]);
        }
    }

    /**
     * @return string
     */
    public function container(Arguments $el)
    {
        $args = wp_parse_args($this->attributes($el), [
            'text' => $this->children($el->children),
        ]);
        return glsr(Builder::class)->div($args);
    }

    /**
     * @return string
     */
    public function field(Arguments $el)
    {
        $args = wp_parse_args($this->attributes($el), [
            'data-tag' => $el->tag,
            'text' => sprintf('{{ %s }}', $el->tag),
        ]);
        return glsr(Builder::class)->div($args);
    }

    /**
     * @param string $attr
     * @param string $key
     * @param scalar $value
     * @return string
     */
    protected function attribute($attr, $key, $value)
    {
        $path = sprintf('%s.%s', $attr, $key);
        if (is_string($value) && !is_numeric($value)) {
            $path = sprintf('%s.%s', $path, $value);
        }
        $attribute = Arr::get($this->attributes, $path);
        if (!empty($attribute) && !empty($value)) {
            if ('class' === $attr) {
                return $attribute;
            }
            if ('style' === $attr) {
                return sprintf($attribute, $value);
            }
        }
        return '';
    }

    /**
     * @return array
     */
    protected function attributes(Arguments $el)
    {
        $class = [];
        $style = [];
        foreach ($el as $key => $value) {
            $class[] = $this->attribute('class', $key, $value);
            $style[] = $this->attribute('style', $key, $value);
        }
        return [
            'class' => implode(' ', array_filter($class)),
            'style' => implode(' ', array_filter($style)),
        ];
    }

    /**
     * @return string
     */
    protected function children(array $data)
    {
        $children = [];
        foreach ($data as $child) {
            $el = glsr()->args($child);
            if ($el->tag) {
                $children[] = $this->field($el);
            } elseif ($el->children) {
                $children[] = $this->container($el);
            }
        }
        return implode('', $children);
    }
}

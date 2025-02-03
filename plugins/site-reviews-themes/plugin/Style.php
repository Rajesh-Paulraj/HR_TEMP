<?php

namespace GeminiLabs\SiteReviews\Addon\Themes;

use GeminiLabs\SiteReviews\Addon\Themes\Application;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;

class Style
{
    const PREFIX = '--gl-';

    /**
     * @var array
     */
    public $only;

    /**
     * @var array
     */
    public $properties;

    /**
     * @var int
     */
    public $themeId;

    /**
     * @return string
     */
    public function __construct($themeId = 0)
    {
        $this->only = [];
        $this->properties = [];
        $this->themeId($themeId);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * @return static
     */
    public function get()
    {
        $settings = $this->settings();
        if (empty($settings)) {
            return $this;
        }
        foreach ($settings as $group => $sections) {
            foreach ($sections as $section => $values) {
                foreach ($values as $key => $value) {
                    // @todo
                    $this->build($key, $value);
                }
            }
        }
        $properties = array_filter($this->properties);
        natsort($properties);
        $this->properties = $properties;
        return $this;
    }

    /**
     * @return static
     */
    public function only(array $propertyKeys)
    {
        $this->only = $propertyKeys;
        return $this;
    }

    /**
     * @return array
     */
    public function properties()
    {
        if (empty($this->properties)) {
            $this->get();
        }
        if (!empty($this->only)) {
            return array_intersect_key($this->properties, array_flip($this->only));
        }
        return $this->properties;
    }

    /**
     * @return array
     */
    public function settings()
    {
        $config = glsr(Application::class)->config('theme-settings');
        $config = array_filter($config, function ($value) {
            return isset($value['theme']);
        });
        $settings = glsr(ThemeSettings::class)->themeId($this->themeId)->toArray();
        $settings = Arr::flatten($settings);
        $settings = array_intersect_key($settings, $config);
        $settings = Arr::convertFromDotNotation($settings);
        return $settings;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->properties();
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
     * @return string
     */
    public function toString()
    {
        return implode('', $this->properties());
    }

    /**
     * @param string $key
     * @param string|int $value
     * @return void
     */
    public function build($key, $value)
    {
        $patterns = [
            'dimensions' => '/^(-?\d+\|){4}px\|\d+$/', // 0|0|0|0|px|1
            'typography' => '/^(-?\d+\|){2}px\|((\#|rgb).+)?$/', // 0|0|px|#000
            'box-shadow' => '/^(-?\d+\|){4}((\#|rgb).+)?$/', // 0|0|0|0|#000
            'rating-colors' => '/^((\#|rgb)[^\|]+\|){6}\d+$/', // #000|#000|#000|#000|#000|#000|1
        ];
        foreach ($patterns as $type => $regex) {
            if (1 === preg_match($regex, $value)) {
                $method = Helper::buildMethodName($type, 'build');
                call_user_func([$this, $method], $key, $value);
                return;
            }
        }
        $this->buildDefault($key, $value);
    }

    public function slideMargin(): string
    {
        return str_replace('padding', 'margin', $this->slidePadding());
    }

    public function slidePadding(): string
    {
        $shadow1 = $this->getShadow(1);
        $shadow2 = $this->getShadow(2);
        $values = [];
        foreach ($shadow1 as $key => $value) {
            $values[$key] = $value > $shadow2[$key] ? $value : $shadow2[$key];
        }
        $size = $values['blur'] + $values['spread'];
        $top = max(0, $size - $values['y']);
        $bottom = max(0, $size + $values['y']);
        $style = sprintf('padding-top:%spx;padding-bottom:%spx;', $top, $bottom);
        return str_replace('0px', '0', $style);
    }

    /**
     * @param string $key
     * @param string|int $value
     * @return void
     */
    protected function buildDefault($key, $value)
    {
        if (is_numeric($value)) {
            $value = sprintf('%spx', $value);
        }
        if (empty($value) && Str::endsWith($key, '_color')) {
            $value = 'transparent';
        }
        $property = $this->property($key);
        $this->properties[$property] = sprintf('%s:%s;', $property, $value);
    }

    /**
     * @param string $key
     * @param string|int $value
     * @return void
     */
    protected function buildDimensions($key, $value)
    {
        $values = explode('|', $value);
        $values = array_combine(['top', 'right', 'bottom', 'left', 'unit'], array_slice($values, 0, 5));
        $property = $this->property($key);
        if (count(array_filter($values)) > 1) {
            $this->properties[$property] = sprintf('%s:%s %s %s %s;',
                $property,
                $values['top'].$values['unit'],
                $values['right'].$values['unit'],
                $values['bottom'].$values['unit'],
                $values['left'].$values['unit']
            );
        } else {
            $this->properties[$property] = sprintf('%s:0;', $property);
        }
    }

    /**
     * @param string $key
     * @param string|int $value
     * @return void
     */
    protected function buildTypography($key, $value)
    {
        $values = explode('|', $value);
        $values = array_pad($values, 4, '');
        $values = array_combine(['fontSize', 'lineHeight', 'unit', 'color'], $values);
        $suffix = str_replace('text_', '', $key);
        if ('' !== $values['color']) {
            $property = $this->property('color', $suffix);
            $this->properties[$property] = sprintf('%s:%s;',
                $property,
                str_replace(' ', '', $values['color'])
            );
        }
        if ('' !== $values['fontSize']) {
            $property = $this->property('font-size', $suffix);
            $this->properties[$property] = sprintf('%s:%s%s;',
                $property,
                $values['fontSize'],
                $values['unit']
            );
        }
        if ('' !== $values['lineHeight']) {
            $property = $this->property('line-height', $suffix);
            $this->properties[$property] = sprintf('%s:%s%s;',
                $property,
                $values['lineHeight'],
                $values['unit']
            );
        }
    }

    /**
     * @param string $key
     * @param string|int $value
     * @return void
     */
    protected function buildBoxShadow($key, $value)
    {
        $values = explode('|', $value);
        $values = array_pad($values, 4, '');
        $values = array_combine(['x', 'y', 'blur', 'spread', 'color'], $values);
        $property = $this->property('box-'.$key);
        if (count(array_filter($values)) > 1 && '' !== $values['color']) {
            $this->properties[$property] = sprintf('%s:%spx %spx %spx %spx %s;',
                $property,
                $values['x'],
                $values['y'],
                $values['blur'],
                $values['spread'],
                str_replace(' ', '', $values['color'])
            );
        } else {
            $this->properties[$property] = sprintf('%s:none;', $property);
        }
    }

    /**
     * @param string $key
     * @param string|int $value
     * @return void
     */
    protected function buildRatingColors($key, $value)
    {
        $values = explode('|', $value);
        $values = array_slice($values, 0, 6);
        foreach ($values as $rating => $color) {
            $property = $this->property('rating-color', $rating);
            $this->properties[$property] = sprintf('%s:%s;', $property, $color);
        }
    }

    protected function getShadow(int $num): array
    {
        $num = max(1, min(2, $num));
        $data = glsr(ThemeSettings::class)->themeId($this->themeId)->get('design.appearance.shadow_'.$num);
        $values = explode('|', $data);
        $values = array_slice(array_pad($values, 4, '0'), 0, 4);
        $values = array_map('intval', $values);
        $values = array_combine(['x', 'y', 'blur', 'spread'], $values);
        return $values;
    }

    /**
     * @param string $key
     * @param string|int $suffix
     * @return string
     */
    protected function property($key, $suffix = null)
    {
        if (!is_null($suffix)) {
            $suffix = Str::prefix(Str::dashcase((string) $suffix), '-');
        }
        return Str::prefix(Str::suffix(Str::dashcase($key), $suffix), static::PREFIX);
    }
}

<?php

namespace GeminiLabs\SiteReviews\Addon\Forms;

use GeminiLabs\SiteReviews\Addon\Forms\Application;
use GeminiLabs\SiteReviews\Addon\Forms\Defaults\FieldDefaults;
use GeminiLabs\SiteReviews\Addon\Forms\Fields\ReviewContent;
use GeminiLabs\SiteReviews\Addon\Forms\Fields\ReviewEmail;
use GeminiLabs\SiteReviews\Addon\Forms\Fields\ReviewImages;
use GeminiLabs\SiteReviews\Addon\Forms\Fields\ReviewName;
use GeminiLabs\SiteReviews\Addon\Forms\Fields\ReviewRating;
use GeminiLabs\SiteReviews\Addon\Forms\Fields\ReviewTerms;
use GeminiLabs\SiteReviews\Addon\Forms\Fields\ReviewTitle;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;

class FormFields
{
    const META_KEY = '_fields';

    /**
     * @param int $formId
     * @return array
     */
    public function customFields($formId)
    {
        $fields = $this->normalizedFieldsKeyed($formId);
        $defaults = $this->defaultFields();
        return array_filter($fields, function ($field) use ($defaults) {
            return !in_array($field['name'], wp_list_pluck($defaults, 'name')) 
                && !$this->isProtectedField($field);
        });
    }

    /**
     * @param int $formId
     * @return array
     */
    public function customTemplateTags($formId)
    {
        $fields = $this->customFields($formId);
        array_walk($fields, function (&$field) {
            $field = Arr::get($field, 'tag');
        });
        return array_filter($fields); // field_name => tag
    }

    /**
     * @return array
     */
    public function defaultFields()
    {
        static $fields;
        if (empty($fields)) {
            $fields = Cast::toArray(glsr()->config('forms/review-form', false)); // bypass filters
            array_walk($fields, function (&$field, $name) {
                $field['name'] = $name;
            });
        }
        return $fields;
    }

    /**
     * @return array  Indexed array
     */
    public function defaultFieldsForMetaboxIndexed()
    {
        $fieldClassnames = [ // order is intentional
            ReviewRating::class,
            ReviewTitle::class,
            ReviewContent::class,
            ReviewName::class,
            ReviewEmail::class,
            ReviewImages::class,
            ReviewTerms::class,
        ];
        $fields = [];
        foreach ($fieldClassnames as $classname) {
            $fieldClass = glsr($classname);
            if ($fieldClass->isActive()) {
                $field = $fieldClass->defaults;
                $field['hidden'] = false;
                $field['required'] = true;
                $fields[] = $field;
            }
        }
        return array_map([glsr(FieldDefaults::class), 'merge'], $fields);
    }

    /**
     * This returns the raw meta data value.
     * @param int $formId
     * @return array  Indexed array
     */
    public function indexedFields($formId)
    {
        return Arr::consolidate(get_post_meta($formId, static::META_KEY, true));
    }

    /**
     * @param int $formId
     * @return array
     */
    public function metaboxConfig($formId, array $config)
    {
        $config['form'] = [
            'label' => _x('Custom Form', 'admin-text', 'site-reviews'),
            'type' => 'select',
            'options' => glsr(Application::class)->forms('&mdash; '._x('Default Form', 'admin-text', 'site-reviews').' &mdash;'),
        ];
        if ($fields = $this->normalizedFieldsKeyed($formId)) {
            $fields = array_merge($fields, array_diff_key($config, $fields));
            foreach ($fields as $name => $field) {
                if ($this->isProtectedField($field) || 'form' === $name) {
                    continue;
                }
                if (empty($field['label'])) {
                    $field['label'] = $name;
                }
                if ('hidden' === $field['type']) {
                    $field['type'] = 'text';
                }
                $keys = ['label', 'options', 'type'];
                $newConfig[$name] = array_filter(shortcode_atts(array_fill_keys($keys, ''), $field));
            }
            if (isset($config['terms'])) {
                $newConfig['terms'] = $config['terms']; // support the new v5.9 "terms" db column
            }
            $newConfig['form'] = $config['form'];
            return $newConfig;
        }
        return $config;
    }

    /**
     * @param int $formId
     * @return array  Indexed array
     */
    public function normalizedFieldsIndexed($formId)
    {
        $fields = $this->indexedFields($formId);
        if (!empty($fields)) {
            $fields[] = [
                'label' => _x('Form ID', 'admin-text', 'site-reviews-forms'),
                'name' => 'form',
                'type' => 'hidden',
                'value' => $formId,
            ];
            $fields = array_map([$this, 'normalizeField'], $fields);
        }
        return $fields;
    }

    /**
     * @param int $formId
     * @return array
     */
    public function normalizedFieldsKeyed($formId)
    {
        $fields = [];
        $indexedFields = $this->normalizedFieldsIndexed($formId);
        foreach ($indexedFields as $field) {
            $fields[glsr_get($field, 'name')] = $field;
        }
        return $fields;
    }

    /**
     * @param int $formId
     * @return array  Indexed array
     */
    public function normalizedFieldsForMetaboxIndexed($formId)
    {
        $fields = $this->indexedFields($formId);
        return array_map([glsr(FieldDefaults::class), 'merge'], $fields);
    }

    /**
     * @param int $formId
     * @return void
     */
    public function saveFields($formId, array $fields)
    {
        $fields = array_map([glsr(FieldDefaults::class), 'merge'], $fields);
        update_post_meta($formId, static::META_KEY, $fields);
    }

    /**
     * @param array $field
     * @return bool
     */
    protected function isProtectedField($field)
    {
        $exclude = ['content', 'images', 'terms', 'title', 'response'];
        return in_array(Arr::get($field, 'name'), $exclude)
            || Str::startsWith(Arr::get($field, 'type'), 'review_');
    }

    /**
     * @return array
     */
    protected function normalizeCustomField(array $field)
    {
        $custom = glsr()->args($field);
        if ('select' === $custom->type && !empty($custom->placeholder)) {
            $field['options'] = Arr::prepend((array) $custom->options, $custom->placeholder, ''); // @phpstan-ignore-line
        }
        return $field;
    }

    /**
     * @return array
     */
    protected function normalizeField(array $field)
    {
        $defaults = $this->defaultFields();
        $field = glsr()->args($field);
        $name = Str::removePrefix($field->type, 'review_');
        if ($default = Arr::get($defaults, $name)) {
            $default['custom'] = true;
            $default['label'] = $field->label;
            $default['name'] = $field->get('name', $name);
            $default['options'] = $field->options;
            $default['placeholder'] = $field->placeholder;
            $default['required'] = $field->cast('required', 'bool');
            return wp_parse_args($default, $field->toArray());
        }
        return $this->normalizeCustomField($field->toArray());
    }
}

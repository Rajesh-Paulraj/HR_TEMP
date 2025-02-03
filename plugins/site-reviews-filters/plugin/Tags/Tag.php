<?php

namespace GeminiLabs\SiteReviews\Addon\Filters\Tags;

use GeminiLabs\SiteReviews\Addon\Filters\Application;
use GeminiLabs\SiteReviews\Addon\Filters\Field;
use GeminiLabs\SiteReviews\Addon\Filters\Template;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Html\Attributes;
use GeminiLabs\SiteReviews\Modules\Html\Tags\Tag as BaseTag;

class Tag extends BaseTag
{
    /**
     * @return string
     */
    protected function fields()
    {
        $fields = glsr(Application::class)->config('forms/filters-form');
        foreach ($fields as $name => &$field) {
            $field = new Field(wp_parse_args($field, ['name' => $name]));
        }
        $fields = $this->normalizeFields($fields);
        return array_reduce($fields, function ($carry, $field) {
            if (Str::startsWith($field->name, $this->tag) && !in_array($field->name, $this->args['hide'])) {
                return $carry.$field;
            }
            return $carry;
        });
    }

    /**
     * @return string
     */
    protected function getClasses()
    {
        return '';
    }

    /**
     * @return array
     */
    protected function getContext()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    protected function handle($value = null)
    {
        if (!empty($context = $this->getContext())) {
            return glsr(Template::class)->build('templates/'.Str::dashCase($this->tag), [
                'args' => $this->args,
                'context' => $context,
            ]);
        }
    }

    /**
     * @return void
     */
    protected function normalizeFieldClasses(Field &$field)
    {
        $fieldClasses = [
            'input' => ['glsr-input', 'glsr-input-'.$field->choiceType()],
            'choice' => ['glsr-input-'.$field->choiceType()],
            'other' => ['glsr-'.$field->field['type']],
        ];
        if ('choice' === $field->fieldType()) {
            $classes = $fieldClasses['choice'];
        } else if (in_array($field->field['type'], Attributes::INPUT_TYPES)) {
            $classes = $fieldClasses['input'];
        } else {
            $classes = $fieldClasses['other'];
        }
        $classes[] = trim(Arr::get($field->field, 'class'));
        $field->field['class'] = implode(' ', $classes);
    }

    /**
     * @return void
     */
    protected function normalizeFieldId(Field &$field)
    {
        if (!empty($this->args->id) && !empty($field->field['id'])) {
            $field->field['id'] .= '-'.$this->args->id;
        }
    }

    /**
     * @return array
     */
    protected function normalizeFields(array $fields)
    {
        $normalizedFields = [];
        foreach ($fields as $field) {
            $hide = explode('_', $field->field['path'])[0];
            if (!in_array($hide, $this->args->hide)) {
                $this->normalizeFieldClasses($field);
                $this->normalizeFieldId($field);
                $normalizedFields[] = $field;
            }
        }
        return $normalizedFields;
    }
}

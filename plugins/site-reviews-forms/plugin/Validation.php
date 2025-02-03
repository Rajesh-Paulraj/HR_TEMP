<?php

namespace GeminiLabs\SiteReviews\Addon\Forms;

use GeminiLabs\SiteReviews\Addon\Forms\Defaults\FieldTypeValidationDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;

class Validation
{
    /**
     * @param int $formId
     * @return array
     */
    public function rules(array $rules, $formId)
    {
        $indexedFields = glsr(FormFields::class)->normalizedFieldsIndexed($formId);
        if (!$indexedFields) {
            return $rules;
        }
        $rules = $this->parseRules($rules, $indexedFields);
        foreach ($indexedFields as $field) {
            $field = glsr()->args($field);
            if ($field->required && !in_array('accepted', $rules[$field->name])) {
                // add the custom required rule for this key
                array_unshift($rules[$field->name], 'required');
            }
            if (!$field->required && false !== ($key = array_search('accepted', glsr_get($rules, $field->name, [])))) {
                // remove the accepted rule if the field is not required
                unset($rules[$field->name][$key]);
            }
        }
        array_walk($rules, function (&$rule) {
            $rule = implode('|', $rule);
        });
        return $rules;
    }

    /**
     * @return array
     */
    protected function parseRules(array $rules, array $indexedFields)
    {
        $defaults = glsr(FieldTypeValidationDefaults::class)->defaults();
        foreach ($indexedFields as $field) {
            $name = Arr::get($field, 'name');
            $type = Arr::get($field, 'type');
            if (!array_key_exists($name, $rules)) {
                // Add the default rule for this field type
                $rules[$name] = glsr_get($defaults, $type, '');
            }
        }
        foreach ($rules as $name => $rule) {
            $rule = array_filter(explode('|', $rule));
            if (false === Arr::searchByKey($name, $indexedFields, 'name')) {
                // remove the default rules if field is missing
                $rules[$name] = [];
                continue;
            }
            // remove the required rule from validation, we will add it back
            // later if the custom field is set as required
            $rules[$name] = array_values(array_diff($rule, ['required']));
        }
        return $rules;
    }
}

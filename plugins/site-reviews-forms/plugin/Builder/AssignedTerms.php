<?php

namespace GeminiLabs\SiteReviews\Addon\Forms\Builder;

use GeminiLabs\SiteReviews\Addon\Forms\Application;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Html\Fields\Field;

class AssignedTerms extends Field
{
    /**
     * {@inheritdoc}
     */
    public function build()
    {
        return Cast::toBool($this->args()->hidden)
            ? $this->buildHidden()
            : $this->buildSelect();
    }

    /**
     * @return string|void
     */
    protected function buildHidden()
    {
        return $this->builder->input([
            'name' => $this->args()->name,
            'type' => 'hidden',
            'value' => implode(',', Arr::consolidate($this->args()->terms)),
        ]);
    }

    /**
     * @return string|void
     */
    protected function buildSelect()
    {
        $options = glsr()->filterBool('builder/enable/optgroup', true)
            ? $this->termGroups()
            : $this->terms();
        $field = $this->args()->merge([
            'class' => 'glsr-select',
            'options' => $options,
        ]);
        return $this->builder->select($field->toArray());
    }

    protected function removeDuplicates(array $options): array
    {
        $children = array_map('array_keys', array_filter($options, 'is_array'));
        $children = Arr::uniqueInt(call_user_func_array('array_merge', $children));
        foreach ($options as $termId => $termIds) {
            if (in_array($termId, $children)) {
                unset($options[$termId]);
            }
        }
        return $options;
    }

    protected function termGroups(): array
    {
        $options = [];
        $terms = $this->terms('all');
        foreach ($terms as $term) {
            $children = array_filter($terms, function ($child) use ($term) {
                return $term->term_id === $child->parent;
            });
            if (empty($children)) {
                $options[$term->term_id] = $term->name;
                continue;
            }
            $options[$term->name] = [];
            foreach ($children as $child) {
                $options[$term->name][$child->term_id] = $child->name;
            }
        }
        return $this->removeDuplicates($options);
    }

    protected function terms(string $fields = 'id=>name'): array
    {
        $args = glsr(Application::class)->filterArray('builder/assigned_terms/args', [
            'count' => false,
            'fields' => $fields,
            'hide_empty' => false,
            'include' => Arr::consolidate($this->builder->args->terms),
            'taxonomy' => glsr()->taxonomy,
        ], $this->args());
        if ('id=>name' === $fields) {
            $args['fields'] = 'id=>name'; // ensure this is correct
        }
        return get_terms($args);
    }
}

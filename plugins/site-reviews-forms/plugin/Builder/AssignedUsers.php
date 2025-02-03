<?php

namespace GeminiLabs\SiteReviews\Addon\Forms\Builder;

use GeminiLabs\SiteReviews\Addon\Forms\Application;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Html\Fields\Field;

class AssignedUsers extends Field
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
            'value' => implode(',', Arr::consolidate($this->args()->users)),
        ]);
    }

    /**
     * @return string|void
     */
    protected function buildSelect()
    {
        $args = glsr(Application::class)->filterArray('builder/assigned_users/args', [
            'fields' => ['ID', 'display_name'],
            'orderby' => 'display_name',
            'role__in' => Arr::consolidate($this->builder->args->roles),
        ], $this->args());
        $users = get_users($args);
        $options = wp_list_pluck($users, 'display_name', 'ID');
        $field = $this->args()->merge([
            'class' => 'glsr-select',
            'options' => $options,
        ]);
        return $this->builder->select($field->toArray());
    }
}

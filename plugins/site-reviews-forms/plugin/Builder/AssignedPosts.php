<?php

namespace GeminiLabs\SiteReviews\Addon\Forms\Builder;

use GeminiLabs\SiteReviews\Addon\Forms\Application;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Html\Fields\Field;

class AssignedPosts extends Field
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
            'value' => $this->args()->value,
        ]);
    }

    /**
     * @return string|void
     */
    protected function buildSelect()
    {
        $args = glsr(Application::class)->filterArray('builder/assigned_posts/args', [
            'no_found_rows' => true, // skip counting the total rows found
            'post_status' => 'publish',
            'post_type' => Arr::consolidate($this->args()->posttypes),
            'posts_per_page' => 100,
            'suppress_filters' => true,
        ], $this->args());
        $posts = get_posts($args);
        $options = wp_list_pluck($posts, 'post_title', 'ID');
        $options = array_filter(array_unique($options));
        natcasesort($options); // sort the results
        $field = $this->args()->merge([
            'class' => 'glsr-select',
            'options' => $options,
        ]);
        return $this->builder->select($field->toArray());
    }
}

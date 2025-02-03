<?php

namespace GeminiLabs\SiteReviews\Addon\Forms\Tags;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Html\Tags\Tag;

class CustomCheckboxTag extends Tag
{
    /**
     * {@inheritdoc}
     */
    protected function handle($value = null)
    {
        $format = $this->with->get('format', 'ul');
        $method = Helper::buildMethodName($format, 'formatWith');
        if (method_exists($this, $method)) {
            $value = call_user_func([$this, $method], $value);
        }
        return $this->wrap($value);
    }

    /**
     * @param string|int $value
     * @return string
     */
    protected function formatWithComma($value)
    {
        $values = explode(',', $value);
        return Str::naturalJoin($values);
    }

    /**
     * @param string|int $value
     * @return string
     */
    protected function formatWithOl($value)
    {
        return glsr(Builder::class)->ol($this->listItems($value));
    }

    /**
     * @param string|int $value
     * @return string
     */
    protected function formatWithUl($value)
    {
        return glsr(Builder::class)->ul($this->listItems($value));
    }

    /**
     * @param string|int $value
     * @return string
     */
    protected function listItems($value)
    {
        $values = explode(',', $value);
        return array_reduce($values, function ($carry, $val) {
            return $carry.glsr(Builder::class)->li(trim($val));
        });
    }
}

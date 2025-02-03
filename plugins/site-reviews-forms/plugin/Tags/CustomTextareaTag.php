<?php

namespace GeminiLabs\SiteReviews\Addon\Forms\Tags;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Text;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Html\Tags\Tag;

class CustomTextareaTag extends Tag
{
    /**
     * {@inheritdoc}
     */
    protected function handle($value = null)
    {
        $format = $this->with->get('format', 'excerpt');
        $method = Helper::buildMethodName($format, 'formatAs');
        if (method_exists($this, $method)) {
            return call_user_func([$this, $method], $value);
        }
        return $this->wrap($value);
    }

    /**
     * @param string|int $value
     * @return string
     */
    protected function formatAsExcerpt($value)
    {
        $limit = Cast::toInt(glsr_get_option('reviews.excerpts_length', 55));
        $text = glsr_get_option('reviews.excerpts', false, 'bool')
            ? glsr(Text::class)->excerpt($value, $limit)
            : glsr(Text::class)->text($value);
        return $this->wrap($text, 'div');
    }

    /**
     * @param string|int $value
     * @return string
     */
    protected function formatAsOl($value)
    {
        $items = glsr(Builder::class)->ol($this->listItems($value));
        return $this->wrap($items);
    }

    /**
     * @param string|int $value
     * @return string
     */
    protected function formatAsParagraph($value)
    {
        $text = $this->normalizeText($value);
        $text = preg_replace('/(\R){1,}/u', PHP_EOL.PHP_EOL, $text);
        $text = wpautop($text); // replace double line breaks with paragraph elements
        $text = glsr(Builder::class)->div([
            'text' => $text,
        ]);
        return $this->wrap($text);
    }

    /**
     * @param string|int $value
     * @return string
     */
    protected function formatAsUl($value)
    {
        $items = glsr(Builder::class)->ul($this->listItems($value));
        return $this->wrap($items);
    }

    /**
     * @param string|int $value
     * @return string
     */
    protected function listItems($value)
    {
        $text = $this->normalizeText($value);
        $values = explode("\n", $text);
        return array_reduce($values, function ($carry, $val) {
            return $carry.glsr(Builder::class)->li(trim($val));
        });
    }

    /**
     * @param string|int $value
     * @return string
     */
    protected function normalizeText($value)
    {
        $text = glsr(Text::class)->normalize($value);
        $text = preg_replace('/( ){1,}/u', ' ', $text);  // replace all multiple space characters with a single space
        $text = wptexturize($text);
        return $text;
    }

    /**
     * @param mixed $with
     * @return bool
     */
    protected function validate($with)
    {
        return true;
    }

    /**
     * @param string $value
     * @param string $tag
     * @return string
     */
    protected function wrapValue($tag, $value)
    {
        return glsr(Builder::class)->$tag([
            'class' => 'glsr-tag-value',
            'data-expanded' => 'false',
            'text' => $value
        ]);
    }
}

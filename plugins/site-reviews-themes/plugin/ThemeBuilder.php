<?php

namespace GeminiLabs\SiteReviews\Addon\Themes;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;

class ThemeBuilder
{
    /**
     * @var array
     */
    public $data;

    /**
     * @var int
     */
    public $formId;

    /**
     * @var int
     */
    public $themeId;

    public function __construct($formId = 0, $themeId = 0)
    {
        $this->data = [];
        $this->formId($formId);
        $this->themeId($themeId);
    }

    public function default()
    {
        return glsr(Application::class)->config('templates/template_1');
    }

    /**
     * @param int $postId
     * @return static
     */
    public function formId($postId)
    {
        $this->formId = Cast::toInt($postId);
        return $this;
    }

    /**
     * @return string
     */
    public function metakey()
    {
        $name = (new \ReflectionClass($this))->getShortName();
        return '_'.Str::snakeCase($name);
    }

    /**
     * @return static
     */
    public function refresh()
    {
        if (Application::POST_TYPE === get_post_type($this->themeId)) {
            $data = Arr::consolidate(get_post_meta($this->themeId, $this->metakey(), true));
            if (empty($data)) {
                $data = $this->default();
            }
            $this->store($data);
        }
        return $this;
    }

    /**
     * @return bool
     */
    public function save(array $data = [])
    {
        if (Application::POST_TYPE === get_post_type($this->themeId)) {
            $this->store($data);
            update_post_meta($this->themeId, $this->metakey(), $this->toArray());
            return true;
        }
        return false;
    }

    /**
     * @return static
     */
    public function store(array $data = [])
    {
        $this->data = $data;
        return $this;
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
     * @return array
     */
    public function toArray()
    {
        if (empty($this->data)) {
            $this->refresh();
        }
        return $this->data;
    }
}

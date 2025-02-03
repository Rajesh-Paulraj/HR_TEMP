<?php

namespace GeminiLabs\SiteReviews\Addon\Filters;

use GeminiLabs\SiteReviews\Addon\Filters\Defaults\FilteredDefaults;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Helpers\Url;
use GeminiLabs\SiteReviews\Modules\Rating;

class SqlModifier
{
    /**
     * @var array
     */
    public $values;

    /**
     * @return array
     */
    public function modify(array $values)
    {
        $this->values = $values;
        foreach (glsr(FilteredDefaults::class)->merge() as $parameter => $value) {
            if (!empty($parameter) && $this->validate($parameter, $value)) {
                $this->buildFragment($parameter, $value);
            }
        }
        return $this->values;
    }

    /**
     * @return string
     */
    protected function hook($for, $parameter)
    {
        $slug = Str::dashCase((new \ReflectionClass($this))->getShortName());
        return sprintf('%s/%s/%s', $slug, $for, $parameter);
    }

    /**
     * @param string $parameter
     * @param int|string $value
     * @return void
     */
    protected function buildFragment($parameter, $value)
    {
        $key = Application::SLUG.'/'.$parameter;
        $method = Helper::buildMethodName($parameter, 'build');
        if (method_exists($this, $method)) {
            call_user_func([$this, $method], $key, $value);
            return;
        }
        glsr(Application::class)->action($this->hook('build', $parameter), $value, $key, $this);
    }

    /**
     * @param string $parameter
     * @param int|string $value
     * @return bool
     */
    protected function validate($parameter, $value = '')
    {
        $method = Helper::buildMethodName($parameter, 'validate');
        if (empty($value)) {
            return false;
        }
        if (method_exists($this, $method) && !call_user_func([$this, $method], $value)) {
            return false;
        }
        return glsr(Application::class)->filterBool($this->hook('validate', $parameter), true, $value, $parameter, $this);
    }

    /**
     * @param int|string $value
     * @return bool
     */
    protected function validateFilterByRating($value)
    {
        $min = glsr()->constant('MIN_RATING', Rating::class);
        $max = glsr()->constant('MAX_RATING', Rating::class);
        $allowedValues = array_merge(range($min, $max), ['critical', 'positive']);
        return in_array($value, $allowedValues);
    }
}

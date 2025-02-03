<?php

namespace GeminiLabs\SiteReviews\Addon\Themes\Mock\Tags;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Html\Tags\Tag as AbstractTag;

class Tag extends AbstractTag
{
    /**
     * {@inheritdoc}
     */
    protected function handle($value = null)
    {
        return $value;
    }

    /**
     * @param string|null $value
     * @param string|null $with
     * @return string|void
     */
    public function handleFor($for, $value = null, $with = null)
    {
        return parent::handleFor($for, $this->value($value), $with);
    }

    /**
     * @param string $path
     * @return bool
     */
    public function isHidden($path = '')
    {
        return false;
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
     * @param mixed $value
     * @return string
     */
    protected function value($value = null)
    {
        return $value;
    }
}

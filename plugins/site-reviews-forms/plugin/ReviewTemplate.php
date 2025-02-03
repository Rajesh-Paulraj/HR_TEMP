<?php

namespace GeminiLabs\SiteReviews\Addon\Forms;

use GeminiLabs\SiteReviews\Addon\Forms\Defaults\FieldDefaults;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Html\Template;

class ReviewTemplate
{
    const META_KEY = '_review_template';

    /**
     * @param int $formId
     * @return string
     */
    public function normalizedTemplate($formId)
    {
        $template = Cast::toString(get_post_meta($formId, static::META_KEY, true));
        return $this->normalizeTemplate($template);
    }

    /**
     * @return array
     */
    public function reservedTags()
    {
        $tags = array_keys(glsr_get_review(0)->build()->context);
        sort($tags);
        return $tags;
    }

    /**
     * @param int $formId
     * @param string $template
     * @return void
     */
    public function save($formId, $template)
    {
        update_post_meta($formId, static::META_KEY, $this->normalizeTemplate($template));
    }

    /**
     * This returns the raw meta data value
     * @param int $formId
     * @return string
     */
    public function template($formId)
    {
        return Cast::toString(get_post_meta($formId, static::META_KEY, true));
    }

    /**
     * @param string $template
     * @return string
     */
    protected function normalizeTemplate($template = '')
    {
        if (empty($template)) {
            $template = glsr(Template::class)->build('templates/review');
        }
        return wp_kses(trim($template), wp_kses_allowed_html('post')); // clean the HTML first
    }
}

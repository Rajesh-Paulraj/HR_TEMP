<?php

namespace GeminiLabs\SiteReviews\Addon\Themes\Columns;

use GeminiLabs\SiteReviews\Helpers\Cast;

class ShortcodeColumn extends Column
{
    /**
     * @return string
     */
    public function build()
    {
        return $this->shortcode('site_reviews', true);
    }

    /**
     * @return string
     */
    public function buildForm()
    {
        return $this->shortcode('site_reviews_form', true);
    }

    /**
     * @return string
     */
    public function buildSummary()
    {
        return $this->shortcode('site_reviews_summary', false);
    }

    /**
     * @param string $name
     * @param bool $includeFormOption
     * @return string
     */
    protected function shortcode($name, $includeFormOption = true)
    {
        $options = [sprintf('theme="%d"', $this->postId)];
        if ($includeFormOption) {
            $formId = Cast::toInt(get_post_meta($this->postId, '_form', true));
            if ($formId) {
                $options[] = sprintf('form="%d"', $formId);
            }
        }
        $dataForm = Cast::toInt($includeFormOption);
        $options = implode(' ', $options);
        $shortcode = sprintf('[%s %s]', $name, $options);
        return sprintf('<code data-select-text data-shortcode="%s" data-form="%d" class="template-tag">%s</code>',
            $name,
            $dataForm,
            $shortcode
        );
    }
}

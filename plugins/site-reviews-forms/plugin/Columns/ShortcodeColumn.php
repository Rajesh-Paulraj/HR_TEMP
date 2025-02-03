<?php

namespace GeminiLabs\SiteReviews\Addon\Forms\Columns;

use GeminiLabs\SiteReviews\Helpers\Cast;

class ShortcodeColumn extends Column
{
    /**
     * {@inheritdoc}
     */
    public function build($name = '')
    {
        $shortcode = sprintf('[%s form="%d"]', $name, $this->postId);
        return sprintf('<code data-select-text class="template-tag">%s</code>', $shortcode);
    }
}

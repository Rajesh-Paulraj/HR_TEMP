<?php

namespace GeminiLabs\SiteReviews\Addon\Themes\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class HtmlAttributesDefaults extends Defaults
{
    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'class.align.center' => 'gl-items-center',
            'class.align.end' => 'gl-items-end',
            'class.align.start' => 'gl-items-start',
            'class.align.stretch' => 'gl-items-stretch',
            'class.direction.col' => 'gl-flex gl-flex-col',
            'class.direction.row' => 'gl-flex gl-flex-row',
            'class.flex.grow' => 'gl-flex-1',
            'class.flex.none' => 'gl-flex-none',
            'class.flex.shrink' => 'gl-flex-0',
            'class.is_bold' => 'gl-bold',
            'class.is_hidden' => 'gl-hidden',
            'class.is_italic' => 'gl-italic',
            'class.is_uppercase' => 'gl-uppercase',
            'class.text.large' => 'gl-text-large',
            'class.text.normal' => 'gl-text-normal',
            'class.text.small' => 'gl-text-small',
            'class.wrap' => 'gl-flex-wrap',
            'style.gap' => 'gap:%spx;',
            'style.minwidth' => 'min-width:%spx;',
        ];
    }
}

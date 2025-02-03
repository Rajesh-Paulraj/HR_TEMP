<?php

namespace GeminiLabs\SiteReviews\Addon\Images\Fields;

use GeminiLabs\SiteReviews\Addon\Images\Application;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Html\Fields\Field;

class Dropzone extends Field
{
    /**
     * @inheritDoc
     */
    public function args()
    {
        $html = glsr(Application::class)->build('views/dropzone-field', [
            'name' => glsr()->id.'['.Application::SLUG.']',
            'types' => Str::fallback(glsr_get_option('addons.'.Application::SLUG.'.accepted_files'), 'image/jpeg,image/png'),
        ]);
        $this->builder->args['text'] = $html;
        return $this->builder->args;
    }

    /**
     * @inheritDoc
     */
    public static function required($fieldLocation = null)
    {
        return [
            'class' => 'glsr-dropzone',
        ];
    }

    /**
     * @inheritDoc
     */
    public function tag()
    {
        return 'span';
    }
}

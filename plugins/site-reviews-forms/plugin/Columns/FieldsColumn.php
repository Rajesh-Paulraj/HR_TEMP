<?php

namespace GeminiLabs\SiteReviews\Addon\Forms\Columns;

use GeminiLabs\SiteReviews\Addon\Forms\FormFields;

class FieldsColumn extends Column
{
    /**
     * {@inheritdoc}
     */
    public function build($value = '')
    {
        $fields = glsr(FormFields::class)->indexedFields($this->postId);
        return (string) count($fields);
    }
}

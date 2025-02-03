<?php

namespace GeminiLabs\SiteReviews\Addon\Filters;

use GeminiLabs\SiteReviews\Modules\Html\Field as DefaultField;

class Field extends DefaultField
{
    /**
     * {@inheritdoc}
     */
    public function getFieldErrors()
    {
        return;
    }

    /**
     * {@inheritdoc}
     */
    protected function normalizeFieldId()
    {
        if (!empty($this->field['id']) || $this->field['is_raw']) {
            return;
        }
        $this->field['id'] = $this->field['path'];
    }

    /**
     * {@inheritdoc}
     */
    protected function normalizeFieldName()
    {
        $this->field['name'] = $this->field['path'];
    }
}

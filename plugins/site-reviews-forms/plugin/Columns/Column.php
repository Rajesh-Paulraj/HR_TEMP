<?php

namespace GeminiLabs\SiteReviews\Addon\Forms\Columns;

abstract class Column
{
    /**
     * @var int
     */
    public $postId;

    public function __construct($postId)
    {
        $this->postId = $postId;
    }

    /**
     * @string $value
     * @return string
     */
    abstract public function build($value = '');

    /**
     * @string $value
     * @return void
     */
    public function render($value = '')
    {
        echo $this->build($value);
    }
}

<?php

namespace GeminiLabs\SiteReviews\Addon\Themes\Columns;

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
     * @return string
     */
    abstract public function build();

    /**
     * @return void
     */
    public function render()
    {
        echo $this->build();
    }
}

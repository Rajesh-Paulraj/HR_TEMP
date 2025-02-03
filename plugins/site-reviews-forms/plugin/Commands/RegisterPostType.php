<?php

namespace GeminiLabs\SiteReviews\Addon\Forms\Commands;

use GeminiLabs\SiteReviews\Addon\Forms\Application;
use GeminiLabs\SiteReviews\Addon\Forms\Defaults\PostTypeDefaults;
use GeminiLabs\SiteReviews\Addon\Forms\Defaults\PostTypeLabelDefaults;
use GeminiLabs\SiteReviews\Contracts\CommandContract as Contract;

class RegisterPostType implements Contract
{
    public $args;

    public function __construct()
    {
        $this->args = glsr(PostTypeDefaults::class)->merge([
            'labels' => glsr(PostTypeLabelDefaults::class)->defaults(),
        ]);
    }

    /**
     * @return void
     */
    public function handle()
    {
        if (!in_array(Application::POST_TYPE, get_post_types(['_builtin' => true]))) {
            register_post_type(Application::POST_TYPE, $this->args);
        }
    }
}

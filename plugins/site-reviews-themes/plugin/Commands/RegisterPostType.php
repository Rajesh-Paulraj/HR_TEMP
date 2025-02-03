<?php

namespace GeminiLabs\SiteReviews\Addon\Themes\Commands;

use GeminiLabs\SiteReviews\Addon\Themes\Application;
use GeminiLabs\SiteReviews\Addon\Themes\Defaults\PostTypeDefaults;
use GeminiLabs\SiteReviews\Addon\Themes\Defaults\PostTypeLabelDefaults;
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

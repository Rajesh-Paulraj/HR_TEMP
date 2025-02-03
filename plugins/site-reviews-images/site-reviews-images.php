<?php
/**
 * ╔═╗╔═╗╔╦╗╦╔╗╔╦  ╦  ╔═╗╔╗ ╔═╗
 * ║ ╦║╣ ║║║║║║║║  ║  ╠═╣╠╩╗╚═╗
 * ╚═╝╚═╝╩ ╩╩╝╚╝╩  ╩═╝╩ ╩╚═╝╚═╝.
 *
 * Plugin Name:       Site Reviews: Review Images
 * Plugin URI:        https://niftyplugins.com/plugins/site-reviews-images
 * Description:       Add images to reviews
 * Version:           3.0.5
 * Author:            Paul Ryley
 * Author URI:        https://niftyplugins.com
 * License:           GPLv3
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Requires at least: 5.8
 * Requires PHP:      7.2
 * Text Domain:       site-reviews-images
 * Domain Path:       languages
 */
defined('WPINC') || die;

if (!class_exists('GL_Plugin_Check_v6')) {
    require_once __DIR__.'/activate.php';
}
if (!(new GL_Plugin_Check_v6(__FILE__))->canProceed()) {
    return;
}
require_once __DIR__.'/autoload.php';
$gatekeeper = new GeminiLabs\SiteReviews\Addon\Images\Gatekeeper(__FILE__, [
    'site-reviews/site-reviews.php' => 'Site Reviews|6.0|https://wordpress.org/plugins/site-reviews|7.0',
]);
if ($gatekeeper->allows()) {
    add_action('site-reviews/addon/register', function ($app) {
        $app->singleton(GeminiLabs\SiteReviews\Addon\Images\Controllers\ApiController::class);
        $app->singleton(GeminiLabs\SiteReviews\Addon\Images\Controllers\Controller::class);
        $app->singleton(GeminiLabs\SiteReviews\Addon\Images\Controllers\GridController::class);
        $app->singleton(GeminiLabs\SiteReviews\Addon\Images\Controllers\MediaController::class);
        $app->register(GeminiLabs\SiteReviews\Addon\Images\Application::class);
        register_deactivation_hook(__FILE__, function () {
            delete_option('glsr_activated_site-reviews-images');
        });
    });
}
add_action('site-reviews/addon/update', function ($app) {
    $app->update(GeminiLabs\SiteReviews\Addon\Images\Application::class, __FILE__);
});

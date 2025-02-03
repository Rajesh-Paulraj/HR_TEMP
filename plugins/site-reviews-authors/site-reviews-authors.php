<?php
/**
 * ╔═╗╔═╗╔╦╗╦╔╗╔╦  ╦  ╔═╗╔╗ ╔═╗
 * ║ ╦║╣ ║║║║║║║║  ║  ╠═╣╠╩╗╚═╗
 * ╚═╝╚═╝╩ ╩╩╝╚╝╩  ╩═╝╩ ╩╚═╝╚═╝.
 *
 * Plugin Name:       Site Reviews: Review Authors
 * Plugin URI:        https://niftyplugins.com/plugins/site-reviews-authors
 * Description:       Allow people to update and manage their reviews from the frontend.
 * Version:           1.0.0-beta6
 * Author:            Paul Ryley
 * Author URI:        https://niftyplugins.com
 * License:           GPLv3
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Requires at least: 5.8
 * Requires PHP:      7.2
 * Text Domain:       site-reviews-authors
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

$gatekeeper = new GeminiLabs\SiteReviews\Addon\Authors\Gatekeeper(__FILE__, [
    'site-reviews/site-reviews.php' => 'Site Reviews|6.7|https://wordpress.org/plugins/site-reviews|6.10',
]);
if ($gatekeeper->allows()) {
    add_action('site-reviews/addon/register', function ($app) {
        $app->register(GeminiLabs\SiteReviews\Addon\Authors\Application::class);
        register_deactivation_hook(__FILE__, function () {
            delete_option('glsr_activated_site-reviews-authors');
        });
    });
}
add_action('site-reviews/addon/update', function ($app) {
    $app->update(GeminiLabs\SiteReviews\Addon\Authors\Application::class, __FILE__);
});

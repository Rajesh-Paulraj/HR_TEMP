<?php

namespace GeminiLabs\SiteReviews\Addon\Forms\Controllers;

use GeminiLabs\SiteReviews\Addon\Forms\Application;
use GeminiLabs\SiteReviews\Helpers\Arr;
use WP_REST_Server as Server;

class RestController extends \WP_REST_Posts_Controller
{
    public function __construct($postType)
    {
        parent::__construct($postType);
        $this->namespace = glsr()->id.'/v1';
    }

    /**
     * @return void
     */
    public function register_routes()
    {
        register_rest_route($this->namespace, '/'.$this->rest_base.'/posttypes', [[
            'callback' => [$this, 'getPostTypes'],
            'methods' => Server::READABLE,
            'permission_callback' => [$this, 'getPermissionCheck'],
        ]]);
        register_rest_route($this->namespace, '/'.$this->rest_base.'/roles', [[
            'callback' => [$this, 'getRoles'],
            'methods' => Server::READABLE,
            'permission_callback' => [$this, 'getPermissionCheck'],
        ]]);
        register_rest_route($this->namespace, '/'.$this->rest_base.'/terms', [[
            'callback' => [$this, 'getTerms'],
            'methods' => Server::READABLE,
            'permission_callback' => [$this, 'getPermissionCheck'],
        ]]);
        register_rest_route($this->namespace, '/'.$this->rest_base.'/users', [[
            'callback' => [$this, 'getUsers'],
            'methods' => Server::READABLE,
            'permission_callback' => [$this, 'getPermissionCheck'],
        ]]);
        parent::register_routes();
    }

    /**
     * @return bool|\WP_Error
     */
    public function getPermissionCheck()
    {
        if (!is_user_logged_in()) {
            return new \WP_Error('rest_authentication', _x('You must be logged in to access this route.', 'admin-text', 'site-reviews-forms'), [
                'status' => rest_authorization_required_code(),
            ]);
        }
        return true;
    }

    /**
     * @return \WP_REST_Response|\WP_Error
     */
    public function getPostTypes()
    {
        $response = [];
        $types = wp_filter_object_list(get_post_types([], 'object'), [
            'show_in_menu' => true,
        ]);
        $types = wp_list_pluck($types, 'label', 'name');
        unset(
            $types['attachment'],
            $types[glsr()->post_type],
            $types[Application::POST_TYPE]
        );
        foreach ($types as $slug => $name) {
            $response[] = [
                'name' => $name,
                'slug' => esc_attr($slug),
            ];
        }
        $sortColumn = wp_list_pluck($response, 'name');
        array_multisort($sortColumn, SORT_ASC, $response);
        return rest_ensure_response($response);
    }

    /**
     * @return \WP_REST_Response|\WP_Error
     */
    public function getRoles()
    {
        $response = [];
        $roles = array_reverse(get_editable_roles());
        foreach ($roles as $slug => $role) {
            $response[] = [
                'name' => translate_user_role($role['name']),
                'slug' => esc_attr($slug),
            ];
        }
        $sortColumn = wp_list_pluck($response, 'name');
        array_multisort($sortColumn, SORT_ASC, $response);
        return rest_ensure_response($response);
    }

    /**
     * @return \WP_REST_Response|\WP_Error
     */
    public function getTerms()
    {
        $response = [];
        $terms = get_terms([
            'count' => false,
            'fields' => 'id=>name',
            'hide_empty' => false,
            'orderby' => 'name',
            'taxonomy' => glsr()->taxonomy,
        ]);
        foreach ($terms as $id => $term) {
            $response[] = [
                'name' => $term,
                'slug' => $id,
            ];
        }
        return rest_ensure_response($response);
    }

    /**
     * @return \WP_REST_Response|\WP_Error
     */
    public function getUsers()
    {
        $response = [];
        $users = [];
        if (current_user_can('edit_posts')) {
            $users = get_users([
                'fields' => ['ID', 'display_name'],
                'orderby' => 'display_name',
            ]);
            $users = wp_list_pluck($users, 'display_name', 'ID');
        }
        $users = Arr::prepend($users, _x('Logged In User', 'admin-text', 'site-reviews-forms'), 'user_id');
        foreach ($users as $slug => $name) {
            $response[] = [
                'name' => $name,
                'slug' => esc_attr($slug),
            ];
        }
        return rest_ensure_response($response);
    }
}

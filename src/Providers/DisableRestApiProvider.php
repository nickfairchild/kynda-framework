<?php

namespace Kynda\Providers;

use Kynda\Support\ServiceProvider;
use WP_Error;

class DisableRestApiProvider extends ServiceProvider
{
    public function boot()
    {
        if ((! is_admin() || wp_doing_ajax())) {
            remove_action('xmlrpc_rsd_apis', 'rest_output_rsd');
            remove_action('template_redirect', 'rest_output_link_header', 11);
            remove_action('wp_head', 'rest_output_link_wp_head', 10);

            add_filter('rest_authentication_errors', [$this, 'restAuthenticationError'], 15);
        }
    }

    public function restAuthenticationError(): WP_Error
    {
        return new WP_Error(
            'rest_forbidden',
            __('REST API forbidden.', 'soil'),
            ['status' => rest_authorization_required_code()]
        );
    }
}

<?php

namespace Kynda\Providers;

use Kynda\Support\ServiceProvider;

class JsToFooterProvider extends ServiceProvider
{
    public function boot()
    {
        if ((! is_admin() || wp_doing_ajax())) {
            add_action('wp_enqueue_scripts', function () {
                remove_action('wp_head', 'wp_print_scripts');
                remove_action('wp_head', 'wp_print_head_scripts', 9);
                remove_action('wp_head', 'wp_enqueue_scripts', 1);
            });
        }
    }
}

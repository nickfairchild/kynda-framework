<?php

namespace Kynda\Providers;

use Kynda\Support\ServiceProvider;

class ThemeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot()
    {
        $this->loadACF();
    }

    protected function loadACF()
    {
        define('MY_ACF_PATH', get_stylesheet_directory().'/inc/plugins/acf-pro/');
        define('MY_ACF_URL', get_stylesheet_directory_uri().'/inc/plugins/acf-pro/');

        include_once(MY_ACF_PATH.'acf.php');

        add_filter('acf/settings/url', function ($url) {
            return MY_ACF_URL;
        });
    }
}

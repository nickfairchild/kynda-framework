<?php

namespace Kynda\Providers;

use Kynda\Support\ServiceProvider;

class DisableAssetVersioningProvider extends ServiceProvider
{
    public function boot()
    {
        if ((! is_admin() || wp_doing_ajax())) {
            add_filter('script_loader_src', [$this, 'removeVersionQueryVar'], 15, 1);
            add_filter('style_loader_src', [$this, 'removeVersionQueryVar'], 15, 1);
        }
    }

    public function removeVersionQueryVar($url)
    {
        return $url ? esc_url(remove_query_arg('ver', $url)) : false;
    }
}

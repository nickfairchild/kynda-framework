<?php

namespace Kynda\Providers;

use Kynda\Support\ServiceProvider;

class NiceSearchProvider extends ServiceProvider
{
    public function boot()
    {
        if ((! is_admin() || wp_doing_ajax())) {
            add_filter('template_redirect', [$this, 'redirect']);

            $this->compat();
        }
    }

    public function redirect(): void
    {
        global $wp_rewrite;

        if (! isset($wp_rewrite) || ! is_object($wp_rewrite) || ! $wp_rewrite->get_search_permastruct()) {
            return;
        }

        $search_base = $wp_rewrite->search_base;

        if (
            is_search()
            && strpos($_SERVER['REQUEST_URI'], "/{$search_base}/") === false
            && strpos($_SERVER['REQUEST_URI'], '&') === false
        ) {
            wp_redirect(get_search_link());
            exit;
        }
    }

    public function rewrite(string $url): string
    {
        return str_replace('/?s=', '/search/', $url);
    }

    protected function compat(): void
    {
        $this->compatYoastSeo();
    }

    protected function compatYoastSeo(): void
    {
        add_filter('wpseo_json_ld_search_url', [$this, 'rewrite']);
    }
}

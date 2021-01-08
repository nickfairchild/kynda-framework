<?php

namespace Kynda\Providers;

use Kynda\Support\ServiceProvider;

class DisableTrackbacksProvider extends ServiceProvider
{
    public function boot()
    {
        if ((! is_admin() || wp_doing_ajax())) {
            add_filter('xmlrpc_methods', [$this, 'disablePingback']);
            add_filter('wp_headers', [$this, 'removePingbackHeaders']);
            add_filter('bloginfo_url', [$this, 'removePingbackUrl'], 10, 2);
            add_filter('xmlrpc_call', [$this, 'removePingbackXmlrpc']);
            add_filter('rewrite_rules_array', [$this, 'removeTrackbackRewriteRules']);
        }
    }

    public function disablePingback(array $methods): array
    {
        unset($methods['pingback.ping']);

        return $methods;
    }

    public function removePingbackHeaders(array $headers): array
    {
        unset($headers['X-Pingback']);

        return $headers;
    }

    public function removePingbackUrl(string $output, string $show): string
    {
        return $show === 'pingback_url' ? '' : $output;
    }

    public function removePingbackXmlrpc(string $action): void
    {
        if ($action === 'pingback.ping') {
            wp_die('Pingbacks are not supported', 'Not Allowed!', ['response' => 403]);
        }
    }

    public function removeTrackbackRewriteRules(array $rules): array
    {
        foreach (array_keys($rules) as $rule) {
            if (preg_match('/trackback\/\?\$$/i', $rule)) {
                unset($rules[$rule]);
            }
        }

        return $rules;
    }
}

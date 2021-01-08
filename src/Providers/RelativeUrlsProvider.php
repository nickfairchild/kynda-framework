<?php

namespace Kynda\Providers;

use Kynda\Support\ServiceProvider;

class RelativeUrlsProvider extends ServiceProvider
{
    public function boot()
    {
        if (
            (! is_admin() || wp_doing_ajax())
            && ! isset($_GET['sitemap'])
            && ! in_array($GLOBALS['pagenow'], ['wp-login.php', 'wp-register.php'])
        ) {
            add_filter('bloginfo_url', [$this, 'relativeUrl']);
            add_filter('the_permalink', [$this, 'relativeUrl']);
            add_filter('wp_list_pages', [$this, 'relativeUrl']);
            add_filter('wp_list_categories', [$this, 'relativeUrl']);
            add_filter('wp_get_attachment_url', [$this, 'relativeUrl']);
            add_filter('the_content_more_link', [$this, 'relativeUrl']);
            add_filter('the_tags', [$this, 'relativeUrl']);
            add_filter('get_pagenum_link', [$this, 'relativeUrl']);
            add_filter('get_comment_link', [$this, 'relativeUrl']);
            add_filter('month_link', [$this, 'relativeUrl']);
            add_filter('day_link', [$this, 'relativeUrl']);
            add_filter('year_link', [$this, 'relativeUrl']);
            add_filter('term_link', [$this, 'relativeUrl']);
            add_filter('the_author_posts_link', [$this, 'relativeUrl']);
            add_filter('script_loader_src', [$this, 'relativeUrl']);
            add_filter('style_loader_src', [$this, 'relativeUrl']);
            add_filter('theme_file_uri', [$this, 'relativeUrl']);
            add_filter('parent_theme_file_uri', [$this, 'relativeUrl']);
            add_filter('wp_calculate_image_srcset', [$this, 'imageSrcset']);
        }
    }

    public function relativeUrl(string $url): string
    {
        if (is_feed()) {
            return $url;
        }

        if (compare_base_url(network_home_url(), $url)) {
            return wp_make_link_relative($url);
        }

        return $url;
    }

    public function imageSrcset(array $sources): array
    {
        if (! is_array($sources)) {
            return $sources;
        }

        return array_map(function ($source) {
            $source['url'] = $this->relativeUrl($source['url']);

            return $source;
        }, $sources);
    }
}

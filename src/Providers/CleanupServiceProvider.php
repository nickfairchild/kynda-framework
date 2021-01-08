<?php

namespace Kynda\Providers;

use Kynda\Dom\Dom;
use Kynda\Support\ServiceProvider;
use DOMDocument;

class CleanupServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ((! is_admin() || wp_doing_ajax())) {
            collect([
                'wp_obscurity' => 'wpObscurity',
                'disable_emojis' => 'disableEmojis',
                'disable_gutenberg_block_css' => 'disableGutenbergBlockCss',
                'disable_extra_rss' => 'disableExtraRss',
                'disable_recent_comments_css' => 'disableRecentCommentsCss',
                'disable_gallery_css' => 'disableGalleryCss',
                'clean_html5_markup' => 'cleanHtmlMarkup',
            ])->each(fn($task) => $this->$task());
        }
    }

    protected function wpObscurity(): void
    {
        add_filter('get_bloginfo_rss', [$this, 'removeDefaultSiteTagline']);
        add_filter('the_generator', '__return_false');
        remove_action('wp_head', 'rsd_link');
        remove_action('wp_head', 'wlwmanifest_link');
        remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);
        remove_action('wp_head', 'wp_generator');
        remove_action('wp_head', 'wp_shortlink_wp_head', 10);
        remove_action('wp_head', 'rest_output_link_wp_head', 10);
        remove_action('wp_head', 'wp_oembed_add_discovery_links');
        remove_action('wp_head', 'wp_oembed_add_host_js');
    }

    protected function disableEmojis(): void
    {
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_action('admin_print_styles', 'print_emoji_styles');
        remove_filter('the_content_feed', 'wp_staticize_emoji');
        remove_filter('comment_text_rss', 'wp_staticize_emoji');
        remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
        add_filter('emoji_svg_url', '__return_false');
    }

    protected function disableGutenbergBlockCss(): void
    {
        add_action('wp_enqueue_scripts', function () {
            wp_dequeue_style('wp-block-library');
        }, 200);
    }

    protected function disableExtraRss(): void
    {
        add_filter('feed_links_show_comments_feed', '__return_false');
    }

    protected function disableRecentCommentsCss(): void
    {
        add_filter('show_recent_comments_widget_style', '__return_false');
    }

    protected function disableGalleryCss(): void
    {
        add_filter('use_default_gallery_style', '__return_false');
    }

    protected function cleanHtmlMarkup(): void
    {
        add_filter('body_class', [$this, 'bodyClass']);
        add_filter('language_attributes', [$this, 'languageAttributes']);

        if (class_exists(DOMDocument::class)) {
            add_filter('style_loader_tag', [$this, 'cleanStylesheetLinks']);
            add_filter('script_loader_tag', [$this, 'cleanScriptTags']);
        }

        add_filter('get_avatar', [$this, 'removeSelfClosingTags']); // <img />
        add_filter('comment_id_fields', [$this, 'removeSelfClosingTags']); // <input />
        add_filter('post_thumbnail_html', [$this, 'removeSelfClosingTags']); // <img />

        add_filter('site_icon_meta_tags', function ($meta_tags) {
            return array_map([$this, 'removeSelfClosingTags'], $meta_tags);
        }, 20);
    }

    public function languageAttributes(): string
    {
        $attributes = [];

        if (is_rtl()) {
            $attributes[] = 'dir="rtl"';
        }

        $lang = get_bloginfo('language');

        if ($lang) {
            $attributes[] = "lang=\"{$lang}\"";
        }

        return implode(' ', $attributes);
    }

    public function cleanStylesheetLinks(string $html): string
    {
        return (new DOM($html))->each(static function ($link) {
            $link->removeAttribute('type');
            $link->removeAttribute('id');

            if (($media = $link->getAttribute('media')) && $media !== 'all') {
                return;
            }

            $link->removeAttribute('media');
        })->html();
    }

    public function cleanScriptTags(string $html): string
    {
        return (new DOM($html))->each(static function ($script) {
            $script->removeAttribute('type');
            $script->removeAttribute('id');
        })->html();
    }

    public function bodyClass(array $classes): array
    {
        $remove_classes = [
            'page-template-default',
        ];

        // Add post/page slug if not present
        if (is_single() || is_page() && ! is_front_page()) {
            if (! in_array($slug = basename(get_permalink()), $classes)) {
                $classes[] = $slug;
            }
        }

        if (is_front_page()) {
            $remove_classes[] = 'page-id-'.get_option('page_on_front');
        }

        $classes = array_values(array_diff($classes, $remove_classes));

        return $classes;
    }

    public function removeDefaultSiteTagline(string $bloginfo): string
    {
        $default_tagline = __('Just another WordPress site');

        return ($bloginfo === $default_tagline) ? '' : $bloginfo;
    }

    public function removeSelfClosingTags(string $html): string
    {
        return str_replace(' />', '>', $html);
    }
}

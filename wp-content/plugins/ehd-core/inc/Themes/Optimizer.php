<?php

namespace EHD\Plugins\Themes;

use EHD\Plugins\Core\Helper;

\defined('ABSPATH') || die;

/**
 * Optimizer Class
 *
 * @author eHD
 */
class Optimizer
{
    public function __construct()
    {
        $this->_cleanup();

        // filter post search only by title
        add_filter("posts_search", [&$this, 'post_search_by_title'], 500, 2);

        // remove id li navigation
        add_filter('nav_menu_item_id', '__return_null', 10, 3);

        add_filter('script_loader_tag', [&$this, 'script_loader_tag'], 11, 3);
        add_filter('style_loader_tag', [&$this, 'style_loader_tag'], 11, 2);

        // Adding Shortcode in WordPress Using Custom HTML Widget
        add_filter('widget_text', 'do_shortcode');
        add_filter('widget_text', 'shortcode_unautop');

        // Hooks the wp action to insert some cache control max-age headers.
        add_action('wp', function ($wp) {
            if (is_feed()) {

                // Set the max age for feeds to 5 minutes.
                if (!is_user_logged_in()) {
                    header('Cache-Control: max-age=' . (5 * MINUTE_IN_SECONDS));
                }
            }
        });

        // normalize upload filename
        add_filter('sanitize_file_name', function (string $filename) {
            return remove_accents($filename);
        }, 10, 1);

        // Disable XML-RPC authentication
        // Filter whether XML-RPC methods requiring authentication, such as for publishing purposes, are enabled.
        add_filter('xmlrpc_enabled', '__return_false');
        add_filter('pre_update_option_enable_xmlrpc', '__return_false');
        add_filter('pre_option_enable_xmlrpc', '__return_zero');
        add_filter('pings_open', '__return_false', 9999);

        add_filter('wp_headers', function ($headers) {
            unset($headers['X-Pingback'], $headers['x-pingback']);
            return $headers;
        });

        //...
        add_filter('excerpt_more', function () {
            return ' ' . '&hellip;';
        });

        //...
        if (!WP_DEBUG) {

            // Remove WP version from RSS.
            add_filter('the_generator', '__return_empty_string');

            add_filter('style_loader_src', [&$this, 'remove_version_scripts_styles'], 11, 1);
            add_filter('script_loader_src', [&$this, 'remove_version_scripts_styles'], 11, 1);
        }
    }

    /** ---------------------------------------- */
    /** ---------------------------------------- */

    /**
     * Launching operation cleanup.
     *
     * @return void
     */
    protected function _cleanup()
    {
        // Xóa widget mặc định "Welcome to WordPress".
        remove_action('welcome_panel', 'wp_welcome_panel');

        // wp_head
        remove_action('wp_head', 'rsd_link'); // Remove the EditURI/RSD link
        remove_action('wp_head', 'wlwmanifest_link'); // Remove Windows Live Writer Manifest link
        remove_action('wp_head', 'wp_shortlink_wp_head'); // Remove the shortlink
        remove_action('wp_head', 'wp_generator'); // remove WordPress Generator
        remove_action('wp_head', 'feed_links_extra', 3); //remove comments feed.
        remove_action('wp_head', 'adjacent_posts_rel_link'); // Remove relational links for the posts adjacent to the current post.
        remove_action('wp_head', 'adjacent_posts_rel_link_wp_head'); // Remove prev and next links
        remove_action('wp_head', 'parent_post_rel_link');
        remove_action('wp_head', 'start_post_rel_link');
        remove_action('wp_head', 'index_rel_link');
        remove_action('wp_head', 'feed_links', 2);
        remove_action('wp_head', 'print_emoji_detection_script', 7); // Emoji detection script.

        // all actions related to emojis
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_action('admin_print_styles', 'print_emoji_styles');
        remove_action('admin_print_scripts', 'print_emoji_detection_script');

        /**
         * Remove wp-json header from WordPress
         * Note that the REST API functionality will still be working as it used to;
         * this only removes the header code that is being inserted.
         */
        remove_action('wp_head', 'rest_output_link_wp_head');
        remove_action('wp_head', 'wp_oembed_add_discovery_links');
        remove_action('template_redirect', 'rest_output_link_header', 11);

        // staticize_emoji
        remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
        remove_filter('the_content_feed', 'wp_staticize_emoji');
        remove_filter('comment_text_rss', 'wp_staticize_emoji');
    }

    // ------------------------------------------------------

    /**
     * @param string $tag
     * @param string $handle
     * @param string $src
     *
     * @return string
     */
    public function script_loader_tag(string $tag, string $handle, string $src)
    {
        $str_parsed = [];
        $str_parsed = apply_filters('defer_script_loader_tag', $str_parsed);

        return Helper::lazyScriptTag($str_parsed, $tag, $handle, $src);
    }

    /** ---------------------------------------- */

    /**
     * @param string $html
     * @param string $handle
     *
     * @return string
     */
    public function style_loader_tag(string $html, string $handle) : string
    {
        // add style handles to the array below
        $styles = [];
        $styles = apply_filters('defer_style_loader_tag', $styles);

        return Helper::lazyStyleTag($styles, $html, $handle);
    }

    // ------------------------------------------------------

    /**
     * Remove version from scripts and styles
     *
     * @param $src
     * @return bool|string
     */
    public function remove_version_scripts_styles($src)
    {
        if ($src && str_contains($src, 'ver=')) {
            $src = remove_query_arg('ver', $src);
        }

        return $src;
    }

    // ------------------------------------------------------

    /**
     * @param $search
     * @param $wp_query
     *
     * @return string
     */
    public function post_search_by_title($search, $wp_query)
    {
        global $wpdb;

        if (empty($search)) {
            return $search; // skip processing – no search term in query
        }

        $q = $wp_query->query_vars;
        $n = !empty($q['exact']) ? '' : '%';

        $search = $searchand = '';
        foreach (Helper::toArray($q['search_terms']) as $term) {
            $term = esc_sql($wpdb->esc_like($term));
            $search .= "{$searchand}($wpdb->posts.post_title LIKE '{$n}{$term}{$n}')";
            $searchand = " AND ";
        }

        if (!empty($search)) {
            $search = " AND ({$search}) ";
            if (!is_user_logged_in()) {
                $search .= " AND ($wpdb->posts.post_password = '') ";
            }
        }

        return $search;
    }
}
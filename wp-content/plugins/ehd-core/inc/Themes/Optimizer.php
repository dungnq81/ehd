<?php

namespace EHD\Themes;

use EHD\Cores\Helper;

\defined('ABSPATH') || die;

/**
 * Optimizer Class
 *
 * @author eHD
 */
final class Optimizer
{
    public function __construct()
    {
        $this->_cleanup();

        //...
        if (!WP_DEBUG) {

            // Remove WP version from RSS.
            add_filter('the_generator', '__return_empty_string');

            add_filter('style_loader_src', [&$this, 'remove_version_scripts_styles'], 11, 1);
            add_filter('script_loader_src', [&$this, 'remove_version_scripts_styles'], 11, 1);
        }

        // wp_print_footer_scripts
        add_action('wp_print_footer_scripts', [&$this, 'print_footer_scripts'], 99);

        // fixed canonical
        add_action('wp_head', [&$this, 'fixed_archive_canonical'], 10);
        add_action('wp_head', [&$this, 'rel_next_prev'], 10);

        // filter post search only by title
        add_filter("posts_search", [&$this, 'post_search_by_title'], 500, 2);

        // remove id li navigation
        add_filter('nav_menu_item_id', '__return_null', 10, 3);

        //...
        add_filter('script_loader_tag', [&$this, 'script_loader_tag'], 12, 3);
        add_filter('style_loader_tag', [&$this, 'style_loader_tag'], 12, 2);

        // Adding Shortcode in WordPress Using Custom HTML Widget
        add_filter('widget_text', 'do_shortcode');
        add_filter('widget_text', 'shortcode_unautop');

        // Hooks the wp action to insert some cache control max-age headers.
        add_action('wp', function ($wp) {
            if (is_feed()) {
                if (!is_user_logged_in()) {

                    // Set the max age for feeds to 5 minutes.
                    header('Cache-Control: max-age=' . (5 * MINUTE_IN_SECONDS));
                }
            }
        });

        // normalize upload filename
        add_filter('sanitize_file_name', function (string $filename) {
            return sanitize_title($filename, '', 'save');
        }, 10, 1);

        // Disable XML-RPC authentication
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

        // Remove admin bar
        add_action('wp_before_admin_bar_render', function () {
            global $wp_admin_bar;
            $wp_admin_bar->remove_menu('wp-logo');
        });

        // Prevent Specific Plugins from Deactivation
        add_filter('plugin_action_links', function ($actions, $plugin_file, $plugin_data, $context) {

            $keys = ['deactivate', 'delete'];
            foreach ($keys as $key) {

                if (array_key_exists($key, $actions)
                    && in_array(
                        $plugin_file,
                        [
                            'ehd-core/ehd-core.php',
                            'advanced-custom-fields-pro/acf.php',
                        ])
                ) {
                    unset($actions[$key]);
                }
            }

            return $actions;

        }, 10, 4);
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
        remove_action('welcome_panel', 'wp_welcome_panel');

        // wp_head
        remove_action('wp_head', 'rsd_link');                        // Remove the EditURI/RSD link
        remove_action('wp_head', 'wlwmanifest_link');                // Remove Windows Live Writer Manifest link
        remove_action('wp_head', 'wp_shortlink_wp_head');            // Remove the shortlink
        remove_action('wp_head', 'wp_generator');                    // remove WordPress Generator
        remove_action('wp_head', 'feed_links_extra', 3);             //remove comments feed.
        remove_action('wp_head', 'adjacent_posts_rel_link');         // Remove relational links for the posts adjacent to the current post.
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
     * This does not enqueue the script because it is tiny and because it is only for IE11,
     * thus it does not warrant having an entire dedicated blocking script being loaded.
     *
     * @link https://git.io/vWdr2
     */
    public function print_footer_scripts()
    {
        ?>
        <script>document.documentElement.classList.remove("no-js");
            if (-1 !== navigator.userAgent.indexOf('MSIE') || -1 !== navigator.appVersion.indexOf('Trident/')) {
                document.documentElement.classList.add('is-IE');
            }</script>
        <?php

        if (file_exists($passive_events = EHD_PLUGIN_PATH . 'assets/js/plugins/passive-events-fix.js')) {
            echo '<script>';
            include $passive_events;
            echo '</script>';
        }

        if (file_exists($skip_link = EHD_PLUGIN_PATH . 'assets/js/plugins/skip-link-focus-fix.js')) {
            echo '<script>';
            include $skip_link;
            echo '</script>';
        }

        if (file_exists($flex_gap = EHD_PLUGIN_PATH . 'assets/js/plugins/flex-gap.js')) {
            echo '<script>';
            include $flex_gap;
            echo '</script>';
        }

        // The following is minified via `npx terser --compress --mangle -- assets/js/skip-link-focus-fix.js`.
    }

    // ------------------------------------------------------

    /**
     * @return void
     */
    public function fixed_archive_canonical()
    {
        if (is_archive()) {
            echo '<link rel="canonical" href="' . get_pagenum_link() . '" />';
        }
    }

    // ------------------------------------------------------

    /**
     * Add rel="next" and rel="prev" to paginated page
     *
     * @return void
     */
    public function rel_next_prev()
    {
        global $paged;

        if (get_previous_posts_link()) { ?>
            <link rel="prev" href="<?php echo get_pagenum_link($paged - 1); ?>" /><?php
        }

        if (get_next_posts_link()) { ?>
            <link rel="next" href="<?php echo get_pagenum_link($paged + 1); ?>" /><?php
        }
    }

    // ------------------------------------------------------

    /**
     * @param string $tag
     * @param string $handle
     * @param string $src
     *
     * @return string
     */
    public function script_loader_tag(string $tag, string $handle, string $src) : string
    {
        // Adds `async`, `defer` and attribute support for scripts registered or enqueued by the theme.
        foreach (['async', 'defer'] as $attr) {
            if (!wp_scripts()->get_data($handle, $attr)) {
                continue;
            }

            // Prevent adding attribute when already added in #12009.
            if (!preg_match(":\s$attr(=|>|\s):", $tag)) {
                $tag = preg_replace(':(?=></script>):', " $attr", $tag, 1);
            }

            // Only allow async or defer, not both.
            break;
        }

        // custom filter which adds proper attributes

        // fontawesome kit
        if (('fontawesome-kit' == $handle) && !preg_match(":\scrossorigin(=|>|\s):", $tag)) {
            $tag = preg_replace(':(?=></script>):', " crossorigin='anonymous'", $tag, 1);
        }

        //...
        // add script handles to the array
        $str_parsed = apply_filters('defer_script_loader_tag', []);
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
        $styles = apply_filters('defer_style_loader_tag', []);
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

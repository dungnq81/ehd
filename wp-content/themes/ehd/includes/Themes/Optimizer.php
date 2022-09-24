<?php

namespace EHD\Themes;

use EHD\Helpers\Cast;

\defined('\WPINC') || die;

/**
 * Optimizer Class
 *
 * @author EHD
 */

if (!class_exists('Optimizer')) {
    class Optimizer
    {
        public function __construct()
        {
            $this->_cleanup();

            $this->_doActions();
            $this->_doFilters();
        }

        // ------------------------------------------------------
        // Actions hook
        // ------------------------------------------------------

        protected function _doActions()
        {
            add_action('wp_default_scripts', [&$this, 'default_scripts']);
            add_action('wp_enqueue_scripts', [&$this, 'enqueue_scripts'], 1001);

            add_action('wp_footer', [&$this, 'back_to_top'], 98);
            add_action('wp_footer', [&$this, 'deferred_scripts'], 999);

            // wp_print_footer_scripts
            add_action('wp_print_footer_scripts', [&$this, 'print_footer_scripts'], 99);

            // hide admin bar
            add_action("user_register", function ($user_id) {
                update_user_meta($user_id, 'show_admin_bar_front', false);
                update_user_meta($user_id, 'show_admin_bar_admin', false);
            }, 10, 1);
        }

        // ------------------------------------------------------
        // Filters hook
        // ------------------------------------------------------

        protected function _doFilters()
        {
            add_filter('body_class', [&$this, 'body_classes'], 11, 1);
            add_filter('post_class', [&$this, 'post_classes'], 11, 1);

            add_filter('nav_menu_css_class', [&$this, 'nav_menu_css_classes'], 11, 2);

            add_filter('script_loader_tag', [&$this, 'script_loader_tag'], 11, 3);
            add_filter('style_loader_tag', [&$this, 'style_loader_tag'], 11, 2);

            // filter post search only by title
            add_filter("posts_search", [&$this, 'post_search_by_title'], 500, 2);

            // Adding Shortcode in WordPress Using Custom HTML Widget
            add_filter('widget_text', 'do_shortcode');
            add_filter('widget_text', 'shortcode_unautop');

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
            if (!WP_DEBUG) {

                // Remove WP version from RSS.
                add_filter('the_generator', '__return_empty_string');

                add_filter('style_loader_src', [&$this, 'remove_version_scripts_styles'], 11, 1);
                add_filter('script_loader_src', [&$this, 'remove_version_scripts_styles'], 11, 1);
            }

            // Changing the alt text on the logo to show your site name
            add_filter('login_headertext', function () {
                return get_option('blogname');
            });

            // Changing the logo link from wordpress.org to your site
            add_filter('login_headerurl', function () {
                return esc_url(site_url('/'));
            });

            // comment off default
            add_filter('wp_insert_post_data', function ($data) {
                if ($data['post_status'] == 'auto-draft') {
                    $data['comment_status'] = 0;
                    $data['ping_status'] = 0;
                }
                return $data;
            }, 11, 1);

            // Add support for buttons in the top-bar menu
            add_filter('wp_nav_menu', function ($ulclass) {
                $find = ['/<a rel="button"/', '/<a title=".*?" rel="button"/'];
                $replace = ['<a rel="button" class="button"', '<a rel="button" class="button"'];
                return preg_replace($find, $replace, $ulclass, 1);
            });

            // normalize upload filename
            add_filter('sanitize_file_name', function (string $filename) {
                return remove_accents($filename);
            }, 10, 1);

            //...
            add_filter('excerpt_more', function () {
                return ' ' . '&hellip;';
            });

            // remove id li navigation
            add_filter('nav_menu_item_id', '__return_null', 10, 3);

            /**
             * Use the is-active class of ZURB Foundation on wp_list_pages output.
             * From required+ Foundation http://themes.required.ch.
             */
            add_filter('wp_list_pages', function ($input) {
                $pattern = '/current_page_item/';
                $replace = 'current_page_item is-active';
                return preg_replace($pattern, $replace, $input);
            }, 10, 2);

            // add multiple for category dropdown
            add_filter('wp_dropdown_cats', [&$this, 'dropdown_cats_multiple'], 10, 2);
        }

        /** ---------------------------------------- */
        /** ---------------------------------------- */

        /**
         * Launching operation cleanup.
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
            if (file_exists($skip_link = get_stylesheet_directory() . '/assets/js/plugins/skip-link-focus-fix.js')) {
                echo '<script>';
                include $skip_link;
                echo '</script>';
            }

            if (file_exists($flex_gap = get_stylesheet_directory() . '/assets/js/plugins/flex-gap.js')) {
                echo '<script>';
                include $flex_gap;
                echo '</script>';
            }

            // The following is minified via `npx terser --compress --mangle -- assets/js/skip-link-focus-fix.js`.
        }

        // ------------------------------------------------------

        /**
         * Build the back to top button
         *
         * - GeneratePress
         * - @since 1.3.24
         */
        public function back_to_top()
        {
            $back_to_top = apply_filters('back_to_top', true);
            if (!$back_to_top) {
                return;
            }

            echo apply_filters( // phpcs:ignore
                'back_to_top_output',
                sprintf(
                    '<a title="%1$s" aria-label="%1$s" rel="nofollow" href="#" class="back-to-top toTop o_draggable" style="opacity:0;visibility:hidden;" data-scroll-speed="%2$s" data-start-scroll="%3$s" data-glyph=""></a>',
                    esc_attr__('Scroll back to top', 'ehd'),
                    absint(apply_filters('back_to_top_scroll_speed', 400)),
                    absint(apply_filters('back_to_top_start_scroll', 300)),
                )
            );
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
            // Adds `async`, `defer` and attribute support for scripts registered or enqueued by the theme.
            $loader = new ScriptLoader;
            $tag = $loader->filterScriptTag($tag, $handle, $src);

            $str_parsed = [];
            $str_parsed = apply_filters('defer_script_loader_tag', $str_parsed);

            return Func::lazyScriptTag($str_parsed, $tag, $handle, $src);
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

            return Func::lazyStyleTag($styles, $html, $handle);
        }

        /** ---------------------------------------- */

        public function enqueue_scripts()
        {
            /*extra scripts*/
            wp_enqueue_script("draggable", get_stylesheet_directory_uri() . "/assets/js/plugins/draggable.js", [], false, true);
            wp_enqueue_script("backtop", get_stylesheet_directory_uri() . "/assets/js/plugins/backtop.js", [], false, true);
            wp_enqueue_script("shares", get_stylesheet_directory_uri() . "/assets/js/plugins/shares.min.js", ["jquery"], false, true);

            //$widgets_block_editor_off = get_theme_mod_ssl( 'use_widgets_block_editor_setting' );
            $gutenberg_widgets_off = Func::getThemeMod('gutenberg_use_widgets_block_editor_setting');
            $gutenberg_off = Func::getThemeMod('use_block_editor_for_post_type_setting');
            if ($gutenberg_widgets_off && $gutenberg_off) {
                wp_dequeue_style('wp-block-library');
                wp_dequeue_style('wp-block-library-theme');
            }
        }

        /** ---------------------------------------- */

        /**
         * @param $scripts
         */
        public function default_scripts($scripts)
        {
            if (!is_admin() && isset($scripts->registered['jquery'])) {
                $script = $scripts->registered['jquery'];
                if ($script->deps) {
                    // Check whether the script has any dependencies
                    $script->deps = array_diff($script->deps, ['jquery-migrate']);
                }
            }
        }

        /** ---------------------------------------- */

        /**
         * Adds custom classes to the array of body classes.
         *
         * @param array $classes Classes for the body element.
         *
         * @return array
         */
        public function body_classes($classes)
        {
            // Check whether we're in the customizer preview.
            if (is_customize_preview()) {
                $classes[] = 'customizer-preview';
            }

            foreach ($classes as $class) {
                if (
                    str_contains($class, 'page-template-templates')
                    || str_contains($class, 'page-template-templatespage-homepage-php')
                    || str_contains($class, 'wp-custom-logo')
                    || str_contains($class, 'no-customize-support')
                    || str_contains($class, 'theme-hello-elementor')
                    || str_contains($class, 'elementor-kit-')
                ) {
                    $classes = array_diff($classes, [$class]);
                }
            }

            if ((is_home() || is_front_page() && class_exists('\WooCommerce'))) {
                $classes[] = 'woocommerce';
            }

            // dark mode func
            //$classes[] = 'light-mode';

            return $classes;
        }

        /** ---------------------------------------- */

        /**
         * Adds custom classes to the array of post classes.
         *
         * @param array $classes Classes for the post element.
         *
         * @return array
         */
        public function post_classes($classes)
        {
            // remove_sticky_class
            if (in_array('sticky', $classes)) {
                $classes = array_diff($classes, ["sticky"]);
                $classes[] = 'wp-sticky';
            }

            // remove tag-, category- classes
            foreach ($classes as $class) {
                if (
                    str_contains($class, 'tag-')
                    || str_contains($class, 'category-')
                ) {
                    $classes = array_diff($classes, [$class]);
                }
            }

            return $classes;
        }

        /** ---------------------------------------- */

        /**
         * @param $classes
         * @param $item
         *
         * @return array
         */
        public function nav_menu_css_classes($classes, $item)
        {
            if (!is_array($classes)) {
                $classes = [];
            }

            // remove menu-item-type-, menu-item-object- classes
            foreach ($classes as $class) {
                if (str_contains($class, 'menu-item-type-')
                    || str_contains($class, 'menu-item-object-')
                ) {
                    $classes = array_diff($classes, [$class]);
                }
            }

            if (1 == $item->current
                || $item->current_item_ancestor
                || $item->current_item_parent
            ) {
                //$classes[] = 'is-active';
                $classes[] = 'active';
            }

            return $classes;
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
            foreach (Cast::toArray($q['search_terms']) as $term) {
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

        // ------------------------------------------------------

        /**
         * @param $output
         * @param $r
         *
         * @return mixed|string|string[]
         */
        public function dropdown_cats_multiple($output, $r)
        {
            if (isset($r['multiple']) && $r['multiple']) {
                $output = preg_replace('/^<select/i', '<select multiple', $output);
                $output = str_replace("name='{$r['name']}'", "name='{$r['name']}[]'", $output);
                foreach (array_map('trim', explode(",", $r['selected'])) as $value) {
                    $output = str_replace("value=\"{$value}\"", "value=\"{$value}\" selected", $output);
                }
            }

            return $output;
        }

        /** ---------------------------------------- */

        /**
         * @return void
         */
        public function deferred_scripts()
        {
            // Facebook
            $fb_appid = Func::getThemeMod('fb_menu_setting');
            if ($fb_appid) {
                echo "<script>";
                echo "window.fbAsyncInit = function() {FB.init({appId:'" . $fb_appid . "',status:true,xfbml:true,autoLogAppEvents:true,version:'v15.0'});};";
                echo "</script>";
                echo "<script async defer crossorigin=\"anonymous\" data-type='lazy' data-src=\"https://connect.facebook.net/en_US/sdk.js\"></script>";
            }

            $fb_pageid = Func::getThemeMod('fbpage_menu_setting');
            $fb_livechat = Func::getThemeMod('fb_chat_setting');
            if ($fb_appid && $fb_pageid && $fb_livechat && !is_customize_preview()) {
                if ($fb_pageid) {
                    echo '<script async defer data-type="lazy" data-src="https://connect.facebook.net/en_US/sdk/xfbml.customerchat.js"></script>';
                    $_fb_message = __('If you need assistance, please leave a message here. Thanks', 'ehd');
                    echo '<div class="fb-customerchat" attribution="setup_tool" page_id="' . $fb_pageid . '" theme_color="#CC3366" logged_in_greeting="' . esc_attr($_fb_message) . '" logged_out_greeting="' . esc_attr($_fb_message) . '"></div>';
                }
            }

            // Zalo
            $zalo_oaid = Func::getThemeMod('zalo_oa_menu_setting');
            $zalo_livechat = Func::getThemeMod('zalo_chat_setting');
            if ($zalo_oaid) {
                if ($zalo_livechat) {
                    echo '<div class="zalo-chat-widget" data-oaid="' . $zalo_oaid . '" data-welcome-message="' . __('Rất vui khi được hỗ trợ bạn.', 'ehd') . '" data-autopopup="0" data-width="350" data-height="420"></div>';
                }

                echo "<script defer data-type='lazy' data-src=\"https://sp.zalo.me/plugins/sdk.js\"></script>";
            }

            /** Set delay timeout milisecond **/
            $timeout = 5000;
            $inline_js = 'const loadScriptsTimer=setTimeout(loadScripts,' . $timeout . ');const userInteractionEvents=["mouseover","keydown","touchstart","touchmove","wheel"];userInteractionEvents.forEach(function(event){window.addEventListener(event,triggerScriptLoader,{passive:!0})});function triggerScriptLoader(){loadScripts();clearTimeout(loadScriptsTimer);userInteractionEvents.forEach(function(event){window.removeEventListener(event,triggerScriptLoader,{passive:!0})})}';
            $inline_js .= "function loadScripts(){document.querySelectorAll(\"script[data-type='lazy']\").forEach(function(elem){elem.setAttribute(\"src\",elem.getAttribute(\"data-src\"));elem.removeAttribute(\"data-src\");})}";
            //echo "\n";
            echo '<script src="data:text/javascript;base64,' . base64_encode($inline_js) . '"></script>';
            //echo "\n";
        }
    }
}
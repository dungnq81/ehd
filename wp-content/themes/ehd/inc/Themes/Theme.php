<?php

namespace EHD\Sites\Themes;

use EHD\Cores\Helper;
use EHD\Sites\Plugins\Elementor;
use EHD\Sites\Plugins\Woocommerce;

\defined( 'ABSPATH' ) || die;

/**
 * Theme Class
 *
 * @author EHD
 */
final class Theme
{
    public function __construct()
    {
        add_action('init', [&$this, 'init']);
        add_action('after_setup_theme', [&$this, 'after_setup_theme'], 11);

        add_action('wp_default_scripts', [&$this, 'wp_default_scripts']);
        add_action('wp_enqueue_scripts', [&$this, 'wp_enqueue_scripts'], 99);
        add_action('wp_footer', [&$this, 'wp_footer'], 999);

        /** ---------------------------------------- */

        // hide user's admin bar
        add_action("user_register", function ($user_id) {
            update_user_meta($user_id, 'show_admin_bar_front', false);
            update_user_meta($user_id, 'show_admin_bar_admin', false);
        }, 11, 1);

        // comment off default
        add_filter('wp_insert_post_data', function ($data) {
            if ($data['post_status'] == 'auto-draft') {
                $data['comment_status'] = 0;
                $data['ping_status'] = 0;
            }
            return $data;
        }, 11, 1);
    }

    /** ---------------------------------------- */
    /** ---------------------------------------- */

    /**
     * Sets up theme defaults and registers support for various WordPress features.
     *
     * Note that this function is hooked into the after_setup_theme hook, which
     * runs before the init hook. The init hook is too late for some features, such
     * as indicating support for post thumbnails.
     */
    public function after_setup_theme() : void
    {
        /**
         * Make theme available for translation.
         * Translations can be filed at WordPress.org.
         * See: https://translate.wordpress.org/projects/wp-themes/hello-elementor
         */
        load_theme_textdomain(EHD_TEXT_DOMAIN, trailingslashit(WP_LANG_DIR) . 'themes/');
        load_theme_textdomain(EHD_TEXT_DOMAIN, get_template_directory() . '/languages');
        load_theme_textdomain(EHD_TEXT_DOMAIN, get_stylesheet_directory() . '/languages');

        /** Add theme support for various features. */
        add_theme_support('automatic-feed-links');
        add_theme_support('post-thumbnails');
        add_theme_support('title-tag');
        add_theme_support('html5', [
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'script',
        ]);

        add_theme_support('customize-selective-refresh-widgets');

        /** Gutenberg wide images. */
        add_theme_support('align-wide');

        /** Add support for block styles. */
        add_theme_support('wp-block-styles');

        /** This theme styles the visual editor to resemble the theme style. */
        add_editor_style();

        /** Remove Template Editor support until WP 5.9 since more Theme Blocks are going to be introduced. */
        remove_theme_support('block-templates');

        /** Enable excerpt to page */
        add_post_type_support('page', 'excerpt');

        /** Set default values for the upload media box */
        update_option('image_default_align', 'center');
        update_option('image_default_size', 'large');

        /**
         * Add support for core custom logo.
         *
         * @link https://codex.wordpress.org/Theme_Logo
         */
        $logo_height = 120;
        $logo_width = 240;

        add_theme_support(
            'custom-logo',
            apply_filters(
                'custom_logo_args',
                [
                    'height'               => $logo_height,
                    'width'                => $logo_width,
                    'flex-height'          => true,
                    'flex-width'           => true,
                    'unlink-homepage-logo' => false,
                ]
            )
        );
    }

    /** ---------------------------------------- */

    /**
     * Enqueue scripts and styles
     *
     * @return void
     */
    public function wp_enqueue_scripts() : void
    {
        /** stylesheet. */
        wp_register_style("plugin-style", get_template_directory_uri() . '/assets/css/plugins.css', [], EHD_THEME_VERSION);
        wp_enqueue_style("app-style", get_template_directory_uri() . '/assets/css/app.css', ["ehd-core-style", "plugin-style"], EHD_THEME_VERSION);

        /** scripts. */
        wp_enqueue_script("app", get_template_directory_uri() . "/assets/js/app.js", ["ehd-core"], EHD_THEME_VERSION, true);
        wp_script_add_data("app", "defer", true);

        /** extra scripts */
        wp_enqueue_script("back-to-top", get_template_directory_uri() . "/assets/js/plugins/back-to-top.js", [], false, true);
        wp_enqueue_script("o-draggable", get_template_directory_uri() . "/assets/js/plugins/draggable.js", [], false, true);
        wp_enqueue_script("social-share", get_template_directory_uri() . "/assets/js/plugins/social-share.js", [], false, true);

        /** inline js */
        $l10n = [
            'ajaxUrl'  => esc_url(admin_url('admin-ajax.php')),
            'baseUrl'  => trailingslashit(site_url()),
            'themeUrl' => trailingslashit(get_template_directory_uri()),
            'smoothScroll' => !0,
            'locale'   => get_locale(),
            'lang'     => Helper::getLang(),
            'lg'       => [
                'view_more'   => __('View more', EHD_TEXT_DOMAIN),
                'view_detail' => __('Detail', EHD_TEXT_DOMAIN),
            ],
        ];
        wp_localize_script('jquery-core', EHD_TEXT_DOMAIN, $l10n);

        /** customize */
        $gutenberg_widgets_off = Helper::getThemeMod('gutenberg_use_widgets_block_editor_setting');
        $gutenberg_off = Helper::getThemeMod('use_block_editor_for_post_type_setting');
        if ($gutenberg_widgets_off && $gutenberg_off) {
            wp_dequeue_style('wp-block-library');
            wp_dequeue_style('wp-block-library-theme');
        }

        /** comments */
        if (is_singular() && comments_open() && get_option('thread_comments')) {
            wp_enqueue_script('comment-reply');
        }
    }

    /** ---------------------------------------- */

    /**
     * Init function
     *
     * @return void
     */
    public function init() : void
    {
        if (!is_admin()) {
            (new Fonts());
        }

        //(new Hooks());
        (new Shortcode())::init();

        /** WooCommerce */
        class_exists('\WooCommerce') && (new WooCommerce());

        /** Elementor */
        did_action('elementor/loaded') && (new Elementor());
    }

    /** ---------------------------------------- */
    /** ---------------------------------------- */

    /**
     * @param $scripts
     * @return void
     */
    public function wp_default_scripts($scripts) : void
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

    public function wp_footer()
    {
        /** Build the back to top button */
        $back_to_top = apply_filters('back_to_top', true);
        if ($back_to_top) {
            echo apply_filters( // phpcs:ignore
                'back_to_top_output',
                sprintf(
                    '<a title="%1$s" aria-label="%1$s" rel="nofollow" href="#" class="back-to-top toTop o_draggable" data-scroll-speed="%2$s" data-start-scroll="%3$s" data-glyph=""></a>',
                    esc_attr__('Scroll back to top', EHD_TEXT_DOMAIN),
                    absint(apply_filters('back_to_top_scroll_speed', 400)),
                    absint(apply_filters('back_to_top_start_scroll', 300)),
                )
            );
        }

        /** deferred_scripts */

        /** Facebook */
        $fb_appid = Helper::getThemeMod('fb_menu_setting');
        if ($fb_appid) {
            echo "<script>";
            echo "window.fbAsyncInit = function() {FB.init({appId:'" . $fb_appid . "',status:true,xfbml:true,autoLogAppEvents:true,version:'v16.0'});};";
            echo "</script>";
            echo "<script async defer crossorigin=\"anonymous\" data-type='lazy' data-src=\"https://connect.facebook.net/en_US/sdk.js\"></script>";
        }

        $fb_pageid = Helper::getThemeMod('fbpage_menu_setting');
        $fb_livechat = Helper::getThemeMod('fb_chat_setting');
        if ($fb_appid && $fb_pageid && $fb_livechat && !is_customize_preview()) {
            if ($fb_pageid) {
                echo '<script async defer data-type="lazy" data-src="https://connect.facebook.net/en_US/sdk/xfbml.customerchat.js"></script>';
                $_fb_message = __('If you need assistance, please leave a message here. Thanks', EHD_TEXT_DOMAIN);
                echo '<div class="fb-customerchat" attribution="setup_tool" page_id="' . $fb_pageid . '" theme_color="#CC3366" logged_in_greeting="' . esc_attr($_fb_message) . '" logged_out_greeting="' . esc_attr($_fb_message) . '"></div>';
            }
        }

        /** Zalo */
        $zalo_oaid = Helper::getThemeMod('zalo_oa_menu_setting');
        $zalo_livechat = Helper::getThemeMod('zalo_chat_setting');
        if ($zalo_oaid) {
            if ($zalo_livechat) {
                echo '<div class="zalo-chat-widget" data-oaid="' . $zalo_oaid . '" data-welcome-message="' . __('Rất vui khi được hỗ trợ bạn.', EHD_TEXT_DOMAIN) . '" data-autopopup="0" data-width="350" data-height="420"></div>';
            }

            echo "<script defer data-type='lazy' data-src=\"https://sp.zalo.me/plugins/sdk.js\"></script>";
        }

        /** Set delay timeout milisecond **/
        $timeout = 5000;
        $inline_js = 'const loadScriptsTimer=setTimeout(loadScripts,' . $timeout . ');const userInteractionEvents=["mouseover","keydown","touchstart","touchmove","wheel"];userInteractionEvents.forEach(function(event){window.addEventListener(event,triggerScriptLoader,{passive:!0})});function triggerScriptLoader(){loadScripts();clearTimeout(loadScriptsTimer);userInteractionEvents.forEach(function(event){window.removeEventListener(event,triggerScriptLoader,{passive:!0})})}';
        $inline_js .= "function loadScripts(){document.querySelectorAll(\"script[data-type='lazy']\").forEach(function(elem){elem.setAttribute(\"src\",elem.getAttribute(\"data-src\"));elem.removeAttribute(\"data-src\");})}";
        echo '<script src="data:text/javascript;base64,' . base64_encode($inline_js) . '"></script>';
    }
}

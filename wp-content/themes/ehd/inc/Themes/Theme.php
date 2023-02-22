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

        add_filter('body_class', [&$this, 'body_classes'], 11, 1);
        add_filter('post_class', [&$this, 'post_classes'], 11, 1);
        add_filter('nav_menu_css_class', [&$this, 'nav_menu_css_classes'], 11, 2);

        /** ---------------------------------------- */

        // add multiple for category dropdown
        add_filter('wp_dropdown_cats', [&$this, 'dropdown_cats_multiple'], 10, 2);

        /** ---------------------------------------- */

        /** template hooks */
        $this->_hooks();
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

    /**
     * @return void
     */
    public function wp_footer() : void
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

        /** Set delay timeout milisecond */
        $timeout = 5000;
        $inline_js = 'const loadScriptsTimer=setTimeout(loadScripts,' . $timeout . ');const userInteractionEvents=["mouseover","keydown","touchstart","touchmove","wheel"];userInteractionEvents.forEach(function(event){window.addEventListener(event,triggerScriptLoader,{passive:!0})});function triggerScriptLoader(){loadScripts();clearTimeout(loadScriptsTimer);userInteractionEvents.forEach(function(event){window.removeEventListener(event,triggerScriptLoader,{passive:!0})})}';
        $inline_js .= "function loadScripts(){document.querySelectorAll(\"script[data-type='lazy']\").forEach(function(elem){elem.setAttribute(\"src\",elem.getAttribute(\"data-src\"));elem.removeAttribute(\"data-src\");elem.removeAttribute(\"data-type\");})}";
        echo '<script src="data:text/javascript;base64,' . base64_encode($inline_js) . '"></script>';
    }

    // ------------------------------------------------------
    // ------------------------------------------------------

    /**
     * Adds custom classes to the array of body classes.
     *
     * @param array $classes Classes for the body element.
     *
     * @return array
     */
    public function body_classes(array $classes) : array
    {
        // Check whether we're in the customizer preview.
        if (is_customize_preview()) {
            $classes[] = 'customizer-preview';
        }

        foreach ($classes as $class) {
            if (
                str_contains($class, 'wp-custom-logo')
                || str_contains($class, 'page-template-templates')
                || str_contains($class, 'page-template-default')
                || str_contains($class, 'no-customize-support')
                || str_contains($class, 'page-id-')
                || str_contains($class, 'wvs-theme-')
            ) {
                $classes = array_diff($classes, [$class]);
            }
        }

        if ((is_home() || is_front_page()) && class_exists('\WooCommerce')) {
            $classes[] = 'woocommerce';
        }

        // ...
        $classes[] = 'default-mode';

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
    public function post_classes(array $classes) : array
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
    public function nav_menu_css_classes($classes, $item) : array
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
     * @param $output
     * @param $r
     *
     * @return mixed|string|string[]
     */
    public function dropdown_cats_multiple($output, $r) : mixed
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
    /** ---------------------------------------- */

    protected function _hooks() : void
    {
        /**
         * Use the is-active class of ZURB Foundation on wp_list_pages output.
         * From required+ Foundation http://themes.required.ch.
         */
        add_filter('wp_list_pages', function ($input) {
            $pattern = '/current_page_item/';
            $replace = 'current_page_item is-active';
            return preg_replace($pattern, $replace, $input);
        }, 10, 2);

        // ------------------------------------------

        /** Add support for buttons in the top-bar menu */
        add_filter('wp_nav_menu', function ($ulclass) {
            $find = ['/<a rel="button"/', '/<a title=".*?" rel="button"/'];
            $replace = ['<a rel="button" class="button"', '<a rel="button" class="button"'];
            return preg_replace($find, $replace, $ulclass, 1);
        });

        // ------------------------------------------

        /** add class to anchor link */
        add_filter('nav_menu_link_attributes', function ($atts) {
            //$atts['class'] = "nav-link";
            return $atts;
        }, 100, 1);

        // ------------------------------------------

        /** comment off default */
        add_filter('wp_insert_post_data', function ($data) {
            if ($data['post_status'] == 'auto-draft') {
                $data['comment_status'] = 0;
                $data['ping_status'] = 0;
            }
            return $data;
        }, 11, 1);

        // ------------------------------------------

        // tag clound font sizes
        add_filter('widget_tag_cloud_args', function (array $args) {
            $args['smallest'] = '10';
            $args['largest'] = '19';
            $args['unit'] = 'px';
            $args['number'] = 12;

            return $args;
        });

        // ------------------------------------------

        /** SMTP Settings **/
        add_action('phpmailer_init', function ($phpmailer) {
            if (!is_object($phpmailer)) {
                $phpmailer = Helper::toObject($phpmailer);
            }

            $phpmailer->isSMTP();
            $phpmailer->Host = 'smtp.gmail.com';
            $phpmailer->SMTPAuth = true;
            $phpmailer->Username = 'official.webhd@gmail.com';
            $phpmailer->Password = 'obvyigyczmcbxgji';

            // Additional settings
            $phpmailer->SMTPSecure = 'tls';
            $phpmailer->Port = 587;
            $phpmailer->From = 'official.webhd@gmail.com';
            $phpmailer->FromName = get_bloginfo('name');

        }, 11);

        // -------------------------------------------------------------
        // optimize load
        // -------------------------------------------------------------

        add_filter('defer_script_loader_tag', function ($arr) {
            $arr = [
                'woo-variation-swatches' => 'defer',
                'wc-single-product'      => 'defer',
                'wc-add-to-cart'         => 'defer',
                'contact-form-7'         => 'defer',

                'comment-reply' => 'delay',
                'wp-embed'      => 'delay',
                'admin-bar'     => 'delay',
                'fixedtoc-js'   => 'delay',
                'back-to-top'   => 'delay',
                'social-share'  => 'delay',
                'o-draggable'   => 'delay',
            ];

            return $arr;

        }, 11, 1);

        // ------------------------------------------

        add_filter('defer_style_loader_tag', function ($arr) {
            $arr = [
                'dashicons',
                'fixedtoc-style',
                'contact-form-7',
                'rank-math',
            ];

            return $arr;

        }, 11, 1);

        // -------------------------------------------------------------
        // images sizes
        // -------------------------------------------------------------

        /**
         * thumbnail (480x0)
         * medium (768x0)
         * large (1024x0)
         *
         * widescreen (1920x9999)
         * post-thumbnail (1200x9999)
         */

        // custom thumb
        add_image_size('widescreen', 1920, 9999, false);
        add_image_size('post-thumbnail', 1200, 9999, false);

        // ------------------------------------------

        /**
         * Disable unwanted image sizes
         */
        add_filter('intermediate_image_sizes_advanced', function ($sizes) {

            unset($sizes['medium_large']);

            unset($sizes['1536x1536']); // disable 2x medium-large size
            unset($sizes['2048x2048']); // disable 2x large size

            return $sizes;
        });

        // ------------------------------------------

        // Disable Scaled
        add_filter('big_image_size_threshold', '__return_false');

        // ------------------------------------------

        /**
         * Disable Other Sizes
         */
        add_action('init', function () {
            remove_image_size('1536x1536'); // disable 2x medium-large size
            remove_image_size('2048x2048'); // disable 2x large size
        });

        // ------------------------------------------

        add_filter('post_thumbnail_html', function ($html) {
            return preg_replace('/(<img[^>]+)(style=\"[^\"]+\")([^>]+)(>)/', '${1}${3}${4}', $html);
        }, 10, 1);

        add_filter('image_send_to_editor', function ($html) {
            return preg_replace('/(<img[^>]+)(style=\"[^\"]+\")([^>]+)(>)/', '${1}${3}${4}', $html);
        }, 10, 1);

        add_filter('the_content', function ($html) {
            return preg_replace('/(<img[^>]+)(style=\"[^\"]+\")([^>]+)(>)/', '${1}${3}${4}', $html);
        }, 10, 1);
    }
}

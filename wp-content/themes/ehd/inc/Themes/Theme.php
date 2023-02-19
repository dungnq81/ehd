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
        add_action('wp_enqueue_scripts', [&$this, 'wp_enqueue_scripts'], 99);
    }

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
        // stylesheet.
        wp_register_style("plugin-style", get_template_directory_uri() . '/assets/css/plugins.css', [], EHD_THEME_VERSION);
        wp_enqueue_style("app-style", get_template_directory_uri() . '/assets/css/app.css', ["ehd-core-style", "plugin-style"], EHD_THEME_VERSION);

        // scripts.
        wp_enqueue_script("app", get_template_directory_uri() . "/assets/js/app.js", ["ehd-core"], EHD_THEME_VERSION, true);
        wp_script_add_data("app", "defer", true);

        // inline js
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

        /*comments*/
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

        (new Hooks());
        (new Shortcode())::init();

        /** WooCommerce */
        class_exists('\WooCommerce') && (new WooCommerce());

        /** Elementor */
        did_action('elementor/loaded') && (new Elementor());
    }
}

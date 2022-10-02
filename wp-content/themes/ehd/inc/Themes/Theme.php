<?php

namespace EHD\Themes;

use EHD\Plugins\Core\Helper;

\defined('\WPINC') || die;

/**
 * Theme Class
 *
 * @author EHD
 */
class Theme
{
    public function __construct()
    {
        //...
        remove_action('after_setup_theme', 'hello_elementor_content_width', 0);

        //...
        add_filter('hello_elementor_add_woocommerce_support', '__return_false');
        add_filter('hello_elementor_load_textdomain', '__return_false');
        add_filter('hello_elementor_enqueue_style', '__return_false');
        add_filter('hello_elementor_enqueue_theme_style', '__return_false');

        //...
        add_filter('hello_elementor_add_theme_support', [&$this, 'ehd_add_theme_support']);
        add_filter('hello_elementor_register_menus', [&$this, 'ehd_register_menus']);

        //...
        add_action('wp_enqueue_scripts', [&$this, 'ehd_enqueue_scripts'], 99);

        //...
        add_action('widgets_init', [&$this, 'ehd_register_sidebars'], 10);
    }

    /** ---------------------------------------- */

    /**
     * Init function
     *
     * @return void
     */
    public function init()
    {
        if (!is_admin()) {
            (new Fonts());
        }

        (new Optimizer());
        (new Shortcode());
    }

    /** ---------------------------------------- */

    public function ehd_register_menus()
    {
        /**
         * Register Menus
         *
         * @link http://codex.wordpress.org/Function_Reference/register_nav_menus#Examples
         */
        register_nav_menus(
            [
                'main-nav' => __('Primary Menu', 'ehd'),
                'second-nav' => __('Secondary Menu', 'ehd'),
                'mobile-nav' => __('Handheld Menu', 'ehd'),
                'social-nav' => __('Social menu', 'ehd'),
                'policy-nav' => __('Terms menu', 'ehd'),
            ]
        );
    }

    /** ---------------------------------------- */

    /**
     * Register widget area.
     *
     * @link https://codex.wordpress.org/Function_Reference/register_sidebar
     */
    public function ehd_register_sidebars()
    {
        /** header sidebar */
        register_sidebar(
            [
                'container' => false,
                'id' => 'w-header-sidebar',
                'name' => __('Header', 'ehd'),
                'description' => __('Widgets added here will appear in right header.', 'ehd'),
                'before_widget' => '<div class="header-widgets %2$s">',
                'after_widget' => '</div>',
                'before_title' => '<span>',
                'after_title' => '</span>',
            ]
        );

        /***************************************/

        // footer columns
        $footer_args = [];

        $rows = (int) Helper::getThemeMod('footer_row_setting');
        $regions = (int) Helper::getThemeMod('footer_col_setting');

        for ($row = 1; $row <= $rows; $row++) {
            for ($region = 1; $region <= $regions; $region++) {
                $footer_n = $region + $regions * ($row - 1); // Defines footer sidebar ID.
                $footer = sprintf('footer_%d', $footer_n);
                if (1 === $rows) {

                    /* translators: 1: column number */
                    $footer_region_name = sprintf(__('Footer Column %1$d', 'ehd'), $region);

                    /* translators: 1: column number */
                    $footer_region_description = sprintf(__('Widgets added here will appear in column %1$d of the footer.', 'ehd'), $region);
                } else {

                    /* translators: 1: row number, 2: column number */
                    $footer_region_name = sprintf(__('Footer Row %1$d - Column %2$d', 'ehd'), $row, $region);

                    /* translators: 1: column number, 2: row number */
                    $footer_region_description = sprintf(__('Widgets added here will appear in column %1$d of footer row %2$d.', 'ehd'), $region, $row);
                }

                $footer_args[$footer] = [
                    'name' => $footer_region_name,
                    'id' => sprintf('w-footer-%d', $footer_n),
                    'description' => $footer_region_description,
                ];
            }
        }

        foreach ($footer_args as $args) {
            $footer_tags = [
                'container' => false,
                'before_widget' => '<div class="widget %2$s">',
                'after_widget' => '</div>',
                'before_title' => '<p class="widget-title h6">',
                'after_title' => '</p>',
            ];

            register_sidebar($args + $footer_tags);
        }
    }

    /** ---------------------------------------- */

    /**
     * Sets up theme defaults and registers support for various WordPress features.
     *
     * Note that this function is hooked into the after_setup_theme hook, which
     * runs before the init hook. The init hook is too late for some features, such
     * as indicating support for post thumbnails.
     */
    public function ehd_add_theme_support()
    {
        /**
         * Make theme available for translation.
         * Translations can be filed at WordPress.org.
         * See: https://translate.wordpress.org/projects/wp-themes/hello-elementor
         */
        load_theme_textdomain('hello-elementor', get_template_directory() . '/languages');
        load_theme_textdomain('ehd', trailingslashit(WP_LANG_DIR) . 'themes');
        load_theme_textdomain('ehd', get_stylesheet_directory() . '/languages');

        // Add theme support for various features.
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

        // Gutenberg wide images.
        add_theme_support('align-wide');

        // Add support for block styles.
        add_theme_support('wp-block-styles');

        // This theme styles the visual editor to resemble the theme style.
        add_editor_style();

        // Remove Template Editor support until WP 5.9 since more Theme Blocks are going to be introduced.
        remove_theme_support('block-templates');

        // Enable excerpt to page
        add_post_type_support('page', 'excerpt');

        // Set default values for the upload media box
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
                    'height' => $logo_height,
                    'width' => $logo_width,
                    'flex-height' => true,
                    'flex-width' => true,
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
    public function ehd_enqueue_scripts()
    {
        // stylesheet.
        wp_register_style("plugin-style", get_stylesheet_directory_uri() . '/assets/css/plugins.css', [], EHD_THEME_VERSION);
        wp_register_style("layout-style", get_stylesheet_directory_uri() . '/assets/css/layout.css', ["plugin-style"], EHD_THEME_VERSION);

        wp_enqueue_style("app-style", get_stylesheet_directory_uri() . '/assets/css/app.css', ["layout-style"], EHD_THEME_VERSION);

        // scripts.
        wp_enqueue_script("app", get_stylesheet_directory_uri() . "/assets/js/app.js", ["jquery", "hello-theme-frontend"], EHD_THEME_VERSION, true);
        wp_script_add_data("app", "defer", true);

        // inline js
        $l10n = [
            'ajaxUrl' => esc_url(admin_url('admin-ajax.php')),
            'baseUrl' => trailingslashit(site_url()),
            'themeUrl' => trailingslashit(get_template_directory_uri()),
            'locale' => get_locale(),
            'lang' => Helper::getLang(),
            //'domain'    => DOMAIN_CURRENT_SITE,
            'lg' => [
                'view_more' => __('View more', 'ehd'),
                'view_detail' => __('Detail', 'ehd')
            ]
        ];

        wp_localize_script('jquery-core', 'eHD', $l10n);

        /*comments*/
        if (is_singular() && comments_open() && get_option('thread_comments')) {
            wp_enqueue_script('comment-reply');
        }

        // Adds `async`, `defer` and attribute support for scripts registered or enqueued by the theme.
        $loader = new ScriptLoader;
        add_filter('script_loader_tag', [&$loader, 'filterScriptTag'], 10, 3);
    }
}
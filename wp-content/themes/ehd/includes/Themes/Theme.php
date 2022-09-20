<?php

namespace EHD\Themes;

\defined( '\WPINC' ) || die;

/**
 * Theme Class
 *
 * @author eHD
 */

if ( ! class_exists( 'Theme' ) ) {
    class Theme {
        public function __construct() {

            //...
            remove_action( 'after_setup_theme', 'hello_elementor_content_width', 0 );

            //...
            add_filter( 'hello_elementor_add_woocommerce_support', '__return_false' );
            add_filter( 'hello_elementor_load_textdomain', '__return_false' );
            add_filter( 'hello_elementor_enqueue_style', '__return_false' );
            add_filter( 'hello_elementor_enqueue_theme_style', '__return_false' );

            //...
            add_filter( 'hello_elementor_add_theme_support', [ &$this, 'ehd_add_theme_support' ] );
            add_filter( 'hello_elementor_register_menus', [ &$this, 'ehd_register_menus' ] );

            //...
            add_action( 'login_enqueue_scripts', [ &$this, 'ehd_login_enqueue_script' ], 30 );
            add_action( 'enqueue_block_editor_assets', [ &$this, 'ehd_block_editor_assets' ] );

            add_action( 'wp_enqueue_scripts', [ &$this, 'ehd_enqueue_scripts' ], 10 );
            add_action( 'wp_enqueue_scripts', [ &$this, 'ehd_enqueue_inline_css' ], 30 ); // After WooCommerce.
        }

        /** ---------------------------------------- */

        /**
         * Init function
         *
         * @return void
         */
        public function init() {

            if ( is_admin() ) {
                //( new Admin );
            } else {
                //( new Fonts );
            }
        }

        /** ---------------------------------------- */

        public function ehd_register_menus() {
            /**
             * Register Menus
             *
             * @link http://codex.wordpress.org/Function_Reference/register_nav_menus#Examples
             */
            register_nav_menus(
                [
                    'main-nav'   => __( 'Primary Menu', 'ehd' ),
                    'second-nav' => __( 'Secondary Menu', 'ehd' ),
                    'mobile-nav' => __( 'Handheld Menu', 'ehd' ),
                    'social-nav' => __( 'Social menu', 'ehd' ),
                    'policy-nav' => __( 'Terms menu', 'ehd' ),
                ]
            );
        }

        /** ---------------------------------------- */

        /**
         * Sets up theme defaults and registers support for various WordPress features.
         *
         * Note that this function is hooked into the after_setup_theme hook, which
         * runs before the init hook. The init hook is too late for some features, such
         * as indicating support for post thumbnails.
         */
        public function ehd_add_theme_support() {

            /**
             * Make theme available for translation.
             * Translations can be filed at WordPress.org.
             * See: https://translate.wordpress.org/projects/wp-themes/hello-elementor
             */
            load_theme_textdomain( 'hello-elementor', get_template_directory() . '/languages' );
            load_theme_textdomain( 'ehd', trailingslashit( WP_LANG_DIR ) . 'themes/' );
            load_theme_textdomain( 'ehd', get_stylesheet_directory() . '/languages' );

            // Add theme support for various features.
            add_theme_support( 'automatic-feed-links' );
            add_theme_support( 'post-thumbnails' );
            add_theme_support( 'title-tag' );
            add_theme_support( 'html5', [
                'search-form',
                'comment-form',
                'comment-list',
                'gallery',
                'caption',
                'script',
            ] );

            add_theme_support( 'customize-selective-refresh-widgets' );

            // Gutenberg wide images.
            add_theme_support( 'align-wide' );

            // Add support for block styles.
            add_theme_support( 'wp-block-styles' );

            // This theme styles the visual editor to resemble the theme style.
            add_theme_support( 'editor-styles' );
            add_editor_style( get_stylesheet_directory_uri() . "/assets/css/editor-style.css" );

            // Remove Template Editor support until WP 5.9 since more Theme Blocks are going to be introduced.
            remove_theme_support( 'block-templates' );

            // Enable excerpt to page
            add_post_type_support( 'page', 'excerpt' );

            // Set default values for the upload media box
            update_option( 'image_default_align', 'center' );
            update_option( 'image_default_size', 'large' );

            /**
             * Add support for core custom logo.
             *
             * @link https://codex.wordpress.org/Theme_Logo
             */
            $logo_height = 120;
            $logo_width  = 240;

            add_theme_support(
                'custom-logo',
                apply_filters(
                    'custom_logo_args',
                    [
                        'height'      => $logo_height,
                        'width'       => $logo_width,
                        'flex-height' => true,
                        'flex-width'  => true,
                        'unlink-homepage-logo' => false,
                    ]
                )
            );

            // Adds `async`, `defer` and attribute support for scripts registered or enqueued by the theme.
            $loader = new ScriptLoader;
            add_filter( 'script_loader_tag', [ &$loader, 'filterScriptTag' ], 10, 3 );
        }

        /** ---------------------------------------- */

        /**
         * Gutenberg editor
         *
         * @return void
         */
        public function ehd_block_editor_assets() {
            wp_enqueue_style( 'editor-style', get_stylesheet_directory_uri() . "/assets/css/editor-style.css" );
        }

        /** ---------------------------------------- */

        /**
         * @retun void
         */
        public function ehd_login_enqueue_script() {
            wp_enqueue_style( "login-style", get_stylesheet_directory_uri() . "/assets/css/admin.css", [], EHD_THEME_VERSION );
            wp_enqueue_script( "login", get_stylesheet_directory_uri() . "/assets/js/login.js", [ "jquery" ], EHD_THEME_VERSION, true );

            // custom script/style
            $logo    = get_theme_file_uri( "/assets/img/logo.png" );
            $logo_bg = get_theme_file_uri( "/assets/img/login-bg.jpg" );

            $css = new Css;
            if ( $logo_bg ) {
                $css->set_selector( 'body.login' );
                $css->add_property( 'background-image', 'url(' . $logo_bg . ')' );
            }

            $css->set_selector( 'body.login #login h1 a' );
            if ( $logo ) {
                $css->add_property( 'background-image', 'url(' . $logo . ')' );
            } else {
                $css->add_property( 'background-image', 'unset' );
            }

            if ( $css->css_output() ) {
                wp_add_inline_style( 'login-style', $css->css_output() );
            }
        }

        /** ---------------------------------------- */

        /**
         * Add CSS for third-party plugins.
         *
         * @return void
         */
        public function enqueue_inline_css() {
            $css = new Css;

            //...

            if ( $css->css_output() ) {
                wp_add_inline_style( 'app-style', $css->css_output() );
            }
        }
    }
}
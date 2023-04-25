<?php

namespace EHD\Sites\Themes;

use EHD\Cores\Helper;
use EHD\Sites\Plugins\Elementor;
use EHD\Sites\Plugins\Woocommerce;

use MatthiasMullie\Minify;

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
		add_action( 'init', [ &$this, 'init' ] );
		add_action( 'after_setup_theme', [ &$this, 'after_setup_theme' ], 11 );
		add_action( 'wp_enqueue_scripts', [ &$this, 'wp_enqueue_scripts' ], 98 );

		// add multiple for category dropdown
		add_filter( 'wp_dropdown_cats', [ &$this, 'dropdown_cats_multiple' ], 10, 2 );

		/** template hooks */
		$this->_hooks();
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
	    load_theme_textdomain( EHD_TEXT_DOMAIN, trailingslashit( WP_LANG_DIR ) . 'themes/' );
	    load_theme_textdomain( EHD_TEXT_DOMAIN, get_template_directory() . '/languages' );
	    load_theme_textdomain( EHD_TEXT_DOMAIN, get_stylesheet_directory() . '/languages' );

	    /** Add theme support for various features. */
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

	    /** Gutenberg wide images. */
	    add_theme_support( 'align-wide' );

	    /** Add support for block styles. */
	    add_theme_support( 'wp-block-styles' );

	    /** This theme styles the visual editor to resemble the theme style. */
	    add_editor_style();

	    /** Remove Template Editor support until WP 5.9 since more Theme Blocks are going to be introduced. */
	    remove_theme_support( 'block-templates' );

	    /** Enable excerpt to page */
	    add_post_type_support( 'page', 'excerpt' );

	    /** Set default values for the upload media box */
	    update_option( 'image_default_align', 'center' );
	    update_option( 'image_default_size', 'large' );

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
	    /** Stylesheet. */
	    wp_register_style( "plugin-style", get_template_directory_uri() . '/assets/css/plugins.css', [], EHD_THEME_VERSION );
	    wp_enqueue_style( "app-style", get_template_directory_uri() . '/assets/css/app.css', [ "ehd-core-style", "plugin-style" ], EHD_THEME_VERSION );

	    /** Scripts. */
	    wp_enqueue_script( "app", get_template_directory_uri() . "/assets/js/app.js", [ "ehd-core" ], EHD_THEME_VERSION, true );
	    wp_script_add_data( "app", "defer", true );

	    /** Extra Scripts */
	    wp_enqueue_script( "back-to-top", get_template_directory_uri() . "/assets/js/plugins/back-to-top.js", [], EHD_THEME_VERSION, true );
	    wp_enqueue_script( "o-draggable", get_template_directory_uri() . "/assets/js/plugins/draggable.js", [], EHD_THEME_VERSION, true );
	    wp_enqueue_script( "social-share", get_template_directory_uri() . "/assets/js/plugins/social-share.js", [], '0.0.2', true );

	    /** Inline JS */
	    $l10n = [
		    'ajaxUrl'      => esc_url( admin_url( 'admin-ajax.php' ) ),
		    'baseUrl'      => trailingslashit( site_url() ),
		    'themeUrl'     => trailingslashit( get_template_directory_uri() ),
		    'smoothScroll' => ! 0,
		    'locale'       => get_locale(),
		    'lang'         => Helper::getLang(),
		    'lg'           => [
			    'view_more'   => __( 'View more', EHD_TEXT_DOMAIN ),
			    'view_detail' => __( 'Detail', EHD_TEXT_DOMAIN ),
		    ],
	    ];

	    wp_localize_script( 'jquery-core', EHD_TEXT_DOMAIN, $l10n );

	    /** Custom CSS */
	    $css = Helper::getCustomPostContent( 'ehd_css', false );
	    if ( $css ) {
		    if ( ! WP_DEBUG ) {
			    $minifier = new Minify\CSS();
			    $minifier->add( $css );
			    $css = $minifier->minify();
		    }

		    wp_add_inline_style( 'app-style', $css );
	    }

	    /** Comments */
	    if ( is_singular() && comments_open() && Helper::getOption( 'thread_comments' ) ) {
		    wp_enqueue_script( 'comment-reply' );
	    } else {
		    wp_dequeue_script( 'comment-reply' );
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
	    if ( ! is_admin() ) {
		    ( new Fonts() );
	    }

	    ( new Shortcode() )::init();

	    /** WooCommerce */
	    class_exists( '\WooCommerce' ) && ( new WooCommerce() );

	    /** Elementor */
	    did_action( 'elementor/loaded' ) && ( new Elementor() );
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
	    if ( isset( $r['multiple'] ) && $r['multiple'] ) {
		    $output = preg_replace( '/^<select/i', '<select multiple', $output );
		    $output = str_replace( "name='{$r['name']}'", "name='{$r['name']}[]'", $output );
		    foreach ( array_map( 'trim', explode( ",", $r['selected'] ) ) as $value ) {
			    $output = str_replace( "value=\"{$value}\"", "value=\"{$value}\" selected", $output );
		    }
	    }

	    return $output;
    }

    /** ---------------------------------------- */

    protected function _hooks() : void
    {
        /**
         * Use the is-active class of ZURB Foundation on wp_list_pages output.
         * From required+ Foundation http://themes.required.ch.
         */
	    add_filter( 'wp_list_pages', function ( $input ) {
		    $pattern = '/current_page_item/';
		    $replace = 'current_page_item is-active';

		    return preg_replace( $pattern, $replace, $input );
	    }, 10, 2 );

        /** Add support for buttons in the top-bar menu */
	    add_filter( 'wp_nav_menu', function ( $ul_class ) {
		    $find    = [ '/<a rel="button"/', '/<a title=".*?" rel="button"/' ];
		    $replace = [ '<a rel="button" class="button"', '<a rel="button" class="button"' ];

		    return preg_replace( $find, $replace, $ul_class, 1 );
	    } );

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

	    /** Custom thumb */
	    add_image_size( 'widescreen', 1920, 9999, false );
	    add_image_size( 'post-thumbnail', 1200, 9999, false );

	    /** Disable unwanted image sizes */
	    add_filter( 'intermediate_image_sizes_advanced', function ( $sizes ) {

		    unset( $sizes['medium_large'] );

		    unset( $sizes['1536x1536'] ); // disable 2x medium-large size
		    unset( $sizes['2048x2048'] ); // disable 2x large size

		    return $sizes;
	    } );

	    /** Disable Scaled */
	    add_filter( 'big_image_size_threshold', '__return_false' );

	    /** Disable Other Sizes */
	    add_action( 'init', function () {
		    remove_image_size( '1536x1536' ); // disable 2x medium-large size
		    remove_image_size( '2048x2048' ); // disable 2x large size
	    } );

	    // ------------------------------------------

	    add_filter( 'post_thumbnail_html', function ( $html ) {
		    return preg_replace( '/(<img[^>]+)(style=\"[^\"]+\")([^>]+)(>)/', '${1}${3}${4}', $html );
	    }, 10, 1 );

	    add_filter( 'image_send_to_editor', function ( $html ) {
		    return preg_replace( '/(<img[^>]+)(style=\"[^\"]+\")([^>]+)(>)/', '${1}${3}${4}', $html );
	    }, 10, 1 );

	    add_filter( 'the_content', function ( $html ) {
		    return preg_replace( '/(<img[^>]+)(style=\"[^\"]+\")([^>]+)(>)/', '${1}${3}${4}', $html );
	    }, 10, 1 );
    }
}

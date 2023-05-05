<?php

namespace EHD;

use EHD_Cores\Helper;
use EHD_Cores\Shortcode;

use EHD_Themes\Admin;
use EHD_Themes\Customizer;
use EHD_Themes\Login;
use EHD_Themes\Optimizer;
use EHD_Themes\Options;

use EHD_Plugins\ACF;
use EHD_Plugins\CF7;
use EHD_Plugins\Elementor\Elementor;
use EHD_Plugins\LiteSpeed;
use EHD_Plugins\RankMath;
use EHD_Plugins\WooCommerce\WooCommerce;
use EHD_Plugins\WpRocket;

use EHD_Widgets\DropdownSearch_Widget;
use EHD_Widgets\offCanvas_Widget;
use EHD_Widgets\Posts_Widget;
use EHD_Widgets\PostsCarousel_Widget;
use EHD_Widgets\RecentPosts_Widget;
use EHD_Widgets\Search_Widget;
use EHD_Widgets\Shortcode_Widget;

\defined( 'ABSPATH' ) || die;

final class Plugin {
	public function __construct() {
		add_action( 'init', [ &$this, 'i18n' ] );
		add_action( 'init', [ &$this, 'init' ] );

		add_action( 'plugins_loaded', [ &$this, 'plugins_loaded' ] );
		add_action( 'wp_enqueue_scripts', [ &$this, 'enqueue' ], 11 );
	}

	/**
	 * @return void
	 */
	public function plugins_loaded(): void {

		/** Widgets wordpress */
		add_action( 'widgets_init', [ &$this, 'unregister_default_widgets' ], 11 );
		add_action( 'widgets_init', [ &$this, 'register_widgets' ], 11 );

		/** WooCommerce */
		class_exists( '\WooCommerce' ) && ( new WooCommerce() );

		/** Advanced Custom Fields */
		class_exists( '\ACF' ) && ( new ACF() );

//        if (!class_exists('\ACF')) {
//            add_action('admin_notices', [$this, 'admin_notice_missing_acf']);
//        } else {
//            (new ACF());
//        }

		/** Elementor */
		did_action( 'elementor/loaded' ) && ( new Elementor() );

		/** WpRocket */
		defined( 'WP_ROCKET_VERSION' ) && ( new WpRocket() );

		/** Litespeed */
		defined( 'LSCWP_BASENAME' ) && ( new LiteSpeed() );

		/** RankMath */
		class_exists( '\RankMath' ) && ( new RankMath() );

		/** Contact form 7 */
		class_exists( '\WPCF7' ) && ( new CF7() );
	}

	/**
	 * Unregisters a WP_Widget widget
	 *
	 * @return void
	 */
	public function unregister_default_widgets(): void {
		unregister_widget( 'WP_Widget_Search' );
		unregister_widget( 'WP_Widget_Recent_Posts' );

		// Removes the styling added to the header for recent comments
		global $wp_widget_factory;
		remove_action( 'wp_head', [
			$wp_widget_factory->widgets['WP_Widget_Recent_Comments'],
			'recent_comments_style'
		] );
	}

	/**
	 * Registers a WP_Widget widget
	 *
	 * @return void
	 */
	public function register_widgets(): void {
		class_exists( offCanvas_Widget::class ) && register_widget( new offCanvas_Widget() );

		class_exists( Search_Widget::class ) && register_widget( new Search_Widget() );
		class_exists( DropdownSearch_Widget::class ) && register_widget( new DropdownSearch_Widget() );
		class_exists( Shortcode_Widget::class ) && register_widget( new Shortcode_Widget() );

		class_exists( RecentPosts_Widget::class ) && register_widget( new RecentPosts_Widget() );
		class_exists( Posts_Widget::class ) && register_widget( new Posts_Widget() );
		class_exists( PostsCarousel_Widget::class ) && register_widget( new PostsCarousel_Widget() );
	}

	/**
	 * Load localization file
	 *
	 * @return void
	 */
	public function i18n(): void {
		load_plugin_textdomain( EHD_PLUGIN_TEXT_DOMAIN );
		load_plugin_textdomain( EHD_PLUGIN_TEXT_DOMAIN, false, EHD_PLUGIN_PATH . 'languages' );
	}

	/**
	 * Handles admin notice for non-active
	 *
	 * @return void
	 */
	public function admin_notice_missing_acf(): void {
		$class   = 'notice notice-error';
		$message = sprintf( __( 'You need %1$s"Advanced Custom Fields"%2$s for the %1$s"eHD-core"%2$s plugin to work and updated.', EHD_PLUGIN_TEXT_DOMAIN ), '<strong>', '</strong>' );

		printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message );
	}

	/**
	 * Handles admin notice for non-active
	 *
	 * Elementor plugin situations
	 *
	 * @return void
	 */
	public function admin_notice_missing_elementor(): void {
		$class   = 'notice notice-error';
		$message = sprintf( __( 'You need to have %1$s"Elementor"%2$s installed and updated for the %1$s"eHD-core"%2$s plugin to function properly.', EHD_PLUGIN_TEXT_DOMAIN ), '<strong>', '</strong>' );

		printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message );
	}

	/**
	 * @return void
	 */
	public function enqueue(): void {
		wp_register_style( 'ehd-swiper-style', EHD_PLUGIN_URL . 'assets/css/swiper.css', [], EHD_PLUGIN_VERSION );
		wp_register_script( 'ehd-swiper', EHD_PLUGIN_URL . 'assets/js/plugins/swiper.js', [], EHD_PLUGIN_VERSION, true );

		wp_register_style( 'ehd-core-style', EHD_PLUGIN_URL . 'assets/css/ehd.css', [], EHD_PLUGIN_VERSION );
		wp_register_script( 'ehd-core', EHD_PLUGIN_URL . 'assets/js/ehd.js', [ "jquery" ], EHD_PLUGIN_VERSION, true );
		wp_script_add_data( 'ehd-core', 'defer', true );

		/** Dequeue classic theme styles */
		wp_dequeue_style( 'classic-theme-styles' );

		/** Customize */
		$block_editor_options = Helper::getOption( 'block_editor__options' );
		$block_style_off      = $block_editor_options['block_style_off'] ?? '';

		/** Remove block CSS */
		if ( $block_style_off ) {
			wp_dequeue_style( 'wp-block-library' );
			wp_dequeue_style( 'wp-block-library-theme' );
		}
	}

	/**
	 * @return void
	 */
	public function init(): void {
		if ( is_admin() ) {
			( new Admin() );
		}

		( new Login() );
		( new Customizer() );
		( new Optimizer() );
		( new Options() );

		( new Shortcode() )::init();
	}
}
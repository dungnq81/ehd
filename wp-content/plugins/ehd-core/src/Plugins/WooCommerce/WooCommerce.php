<?php

namespace EHD_Plugins\WooCommerce;

use EHD_Cores\Helper;
use EHD_Plugins\WooCommerce\Widgets\MiniCart_Widget;
use EHD_Plugins\WooCommerce\Widgets\Products_Widget;
use EHD_Plugins\WooCommerce\Widgets\ProductsCarousel_Widget;
use EHD_Plugins\WooCommerce\Widgets\RecentProducts_Widget;

\defined( 'ABSPATH' ) || die;

/**
 * WooCommerce Plugin
 *
 * @author   WEBHD
 */
final class WooCommerce {

	/**
	 * @var array|false|mixed
	 */
	public $woocommerce_options = [];

	// ------------------------------------------------------

	public function __construct() {

		$this->woocommerce_options = Helper::getOption( 'woocommerce__options', false, false );

		$woocommerce_jsonld = $this->woocommerce_options['woocommerce_jsonld'] ?? '';
		if ( $woocommerce_jsonld ) {

			// Remove the default WooCommerce 3 JSON/LD structured data format
			add_action( 'init', [ &$this, 'remove_woocommerce_jsonld' ], 10 );
		}

		// hooks
		$this->_hooks();

		add_action( 'after_setup_theme', [ &$this, 'after_setup_theme' ], 31 );
		add_action( 'wp_enqueue_scripts', [ &$this, 'enqueue_scripts' ], 98 );

		add_action( 'widgets_init', [ &$this, 'unregister_default_widgets' ], 31 );
		add_action( 'widgets_init', [ &$this, 'register_widgets' ], 31 );
	}

	// ------------------------------------------------------

	/**
	 * @return void
	 */
	public function remove_woocommerce_jsonld() {
		remove_action( 'wp_footer', [ WC()->structured_data, 'output_structured_data' ], 10 );
		remove_action( 'woocommerce_email_order_details', [ WC()->structured_data, 'output_email_structured_data' ], 30 );
	}

	// ------------------------------------------------------

	/**
	 * Registers a WP_Widget widget
	 *
	 * @return void
	 */
	public function register_widgets(): void {
		class_exists( MiniCart_Widget::class ) && register_widget( new MiniCart_Widget() );
		class_exists( Products_Widget::class ) && register_widget( new Products_Widget() );
		class_exists( RecentProducts_Widget::class ) && register_widget( new RecentProducts_Widget() );
		class_exists( ProductsCarousel_Widget::class ) && register_widget( new ProductsCarousel_Widget() );
	}

	// ------------------------------------------------------

	/**
	 * Unregisters a WP_Widget widget
	 *
	 * @return void
	 */
	public function unregister_default_widgets(): void {
		unregister_widget( 'WC_Widget_Product_Search' );
		unregister_widget( 'WC_Widget_Products' );
	}

	// ------------------------------------------------------

	/**
	 * @return void
	 */
	public function enqueue_scripts(): void {
		wp_enqueue_style( 'ehd-core-woocommerce-style', EHD_PLUGIN_URL . "assets/css/woocommerce.css", [ "ehd-core-style" ], EHD_PLUGIN_VERSION );
	}

	// ------------------------------------------------------

	/**
	 * @return void
	 */
	public function after_setup_theme(): void {
		add_theme_support( 'woocommerce' );

		// Add support for WC features.
		//add_theme_support( 'wc-product-gallery-zoom' );
		//add_theme_support( 'wc-product-gallery-lightbox' );
		//add_theme_support( 'wc-product-gallery-slider' );

		// Remove woocommerce default styles
		add_filter( 'woocommerce_enqueue_styles', '__return_false' );
	}

	// ------------------------------------------------------

	/**
	 * @return void
	 */
	protected function _hooks(): void {

		// https://stackoverflow.com/questions/57321805/remove-header-from-the-woocommerce-administrator-panel
		add_action( 'admin_head', function () {
			//remove_action( 'in_admin_header', [ 'Automattic\WooCommerce\Internal\Admin\Loader', 'embed_page_header' ] );
			//remove_action( 'in_admin_header', [ 'Automattic\WooCommerce\Admin\Loader', 'embed_page_header' ] );

			echo '<style>#wpadminbar ~ #wpbody { margin-top: 0 !important; }.woocommerce-layout__header { display: none !important; }</style>';
		} );
	}
}

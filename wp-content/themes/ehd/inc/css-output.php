<?php

/**
 * CSS Output functions
 *
 * @author   WEBHD
 */

use EHD\Cores\Helper;
use EHD\Libs\CSS;

\defined( 'ABSPATH' ) || die;

// ------------------------------------------

/** inline css */
if ( ! function_exists( '__enqueue_inline_css' ) ) {
	add_action( 'wp_enqueue_scripts', '__enqueue_inline_css', 32 ); // After WooCommerce.

	/**
	 * Add CSS for third-party plugins.
	 *
	 * @return void
	 */
	function __enqueue_inline_css(): void {
		$css = new CSS();

		// breadcrumbs bg
		$breadcrumb_bg = Helper::getThemeMod( 'breadcrumb_bg_setting' );
		if ( $breadcrumb_bg ) {
			$css->set_selector( 'section.section-title>.title-bg' );
			$css->add_property( 'background-image', 'url(' . $breadcrumb_bg . ')' );
		}

		if ( $css->css_output() ) {
			wp_add_inline_style( 'app-style', $css->css_output() );
		}
	}
}

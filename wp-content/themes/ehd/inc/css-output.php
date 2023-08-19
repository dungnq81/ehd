<?php

/**
 * CSS Output functions
 *
 * @author   WEBHD
 */

use EHD_Cores\Helper;
use EHD_Libs\CSS;

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
	}
}

<?php

namespace EHD_Settings;

use EHD_Cores\Helper;
use MatthiasMullie\Minify;

/**
 * Custom CSS Class
 *
 * @author EHD
 */

\defined('ABSPATH') || die;

final class CustomCSS {
	public function __construct() {
		// add_action( 'wp_enqueue_scripts', [ &$this, 'header_custom_css' ], 99 );
		add_action( 'wp_head', [ &$this, 'header_custom_css' ], 98 );
	}

	/**
	 * @return void
	 */
	public function header_custom_css() {

		/** Custom CSS */
		$css = Helper::getCustomPostContent( 'ehd_css', false );

		if ( $css ) {
			if ( ! WP_DEBUG ) {
				$minifier = new Minify\CSS();
				$minifier->add( $css );
				$css = $minifier->minify();
			}

			echo "<style id='custom-style-inline-css'>" . $css . "</style>";
			// wp_add_inline_style( 'app-style', $css );
		}
	}
}
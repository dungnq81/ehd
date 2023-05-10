<?php

namespace EHD_Settings;

use EHD_Cores\Helper;

/**
 * Custom Scripts Class
 *
 * @author eHD
 */

\defined('ABSPATH') || die;

final class CustomScripts {
	public function __construct() {
		add_action( 'wp_head', [ &$this, 'header_scripts__hook' ], 99 ); // header scripts
		add_action( 'wp_body_open', [ &$this, 'body_scripts_top__hook' ], 99 ); // body scripts - TOP

		add_action( 'wp_footer', [ &$this, 'footer_scripts__hook' ], 1 ); // footer scripts
		add_action( 'wp_footer', [ &$this, 'body_scripts_bottom__hook' ], 998 ); // body scripts - BOTTOM
	}

	/**
	 * Header scripts
	 *
	 * @return void
	 */
	public function header_scripts__hook() : void {
		$html_header = Helper::getCustomPostContent( 'html_header', true );
		if ( $html_header ) {
			echo $html_header;
		}
	}

	/**
	 * Body scripts - TOP
	 *
	 * @return void
	 */
	public function body_scripts_top__hook() {
		$html_body_top = Helper::getCustomPostContent( 'html_body_top', true );
		if ( $html_body_top ) {
			echo $html_body_top;
		}
	}

	/**
	 * Footer scripts
	 *
	 * @return void
	 */
	public function footer_scripts__hook() {
		$html_footer = Helper::getCustomPostContent( 'html_footer', true );
		if ( $html_footer ) {
			echo $html_footer;
		}
	}

	/**
	 * Body scripts - BOTTOM
	 *
	 * @return void
	 */
	public function body_scripts_bottom__hook() {
		$html_body_bottom = Helper::getCustomPostContent( 'html_body_bottom', true );
		if ( $html_body_bottom ) {
			echo $html_body_bottom;
		}
	}
}

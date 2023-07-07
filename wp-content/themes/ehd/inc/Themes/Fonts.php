<?php

namespace EHD_Sites\Themes;

\defined( 'ABSPATH' ) || die;

/**
 * Font Class
 *
 * @author EHD
 */
final class Fonts {
	public function __construct() {
		add_action( 'wp_head', [ &$this, 'pre_connect' ], 2 );
		add_action( 'wp_enqueue_scripts', [ &$this, 'enqueue_scripts' ], 101 );
	}

	/** ---------------------------------------- */

	/**
	 * @return void
	 */
	public function pre_connect(): void {
		echo '<link rel="preconnect" href="https://fonts.googleapis.com" />';
		echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />';
	}

	/** ---------------------------------------- */

	public function enqueue_scripts(): void {
		wp_enqueue_style( "fonts-style", get_template_directory_uri() . '/assets/css/fonts.css', [], EHD_THEME_VERSION );

		//wp_register_script("fontawesome-kit", "https://kit.fontawesome.com/870d5b0bdf.js", [], false, true);
		//wp_script_add_data("fontawesome-kit", "defer", true);
		//wp_enqueue_script('fontawesome-kit');
	}
}

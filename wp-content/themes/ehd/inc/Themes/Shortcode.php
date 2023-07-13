<?php

namespace EHD\Themes;

\defined( 'ABSPATH' ) || die;

final class Shortcode {
	/**
	 * @return void
	 */
	public static function init(): void {
		$shortcodes = [
			'demo_shortcode' => __CLASS__ . '::demo_shortcode',
		];

		foreach ( $shortcodes as $shortcode => $function ) {
			add_shortcode( apply_filters( "{$shortcode}_shortcode_tag", $shortcode ), $function );
		}
	}

	// ------------------------------------------------------

	/**
	 * @param array $atts
	 *
	 * @return false|string
	 */
	public static function demo_shortcode( array $atts = [] ) {
		$default_atts = [];
		$atts         = shortcode_atts(
			$default_atts,
			$atts,
			'demo_shortcode'
		);

		ob_start();

		//...
		echo 'demo_shortcode';

		return ob_get_clean();
	}
}

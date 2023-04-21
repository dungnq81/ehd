<?php
/**
 * Header elements.
 *
 * @package ehd
 */

use EHD\Cores\Helper;

\defined( 'ABSPATH' ) || die;

// -----------------------------------------------
// wp_footer
// -----------------------------------------------

if ( ! function_exists( '__wp_footer' ) ) {
	add_action( 'wp_footer', '__wp_footer', 98 );
	/**
	 * @return void
	 */
	function __wp_footer() : void
	{
		/** Build the back to top button */
		$back_to_top = apply_filters('ehd_back_to_top', true);
		if ($back_to_top) {
			echo "\n";
			echo apply_filters( // phpcs:ignore
				'end_back_to_top_output',
				sprintf(
					'<a title="%1$s" aria-label="%1$s" rel="nofollow" href="#" class="back-to-top toTop" data-scroll-speed="%2$s" data-start-scroll="%3$s" data-glyph=""></a>',
					esc_attr__('Scroll back to top', EHD_TEXT_DOMAIN),
					absint(apply_filters('ehd_back_to_top_scroll_speed', 400)),
					absint(apply_filters('ehd_back_to_top_start_scroll', 300)),
				)
			);
		}
	}
}

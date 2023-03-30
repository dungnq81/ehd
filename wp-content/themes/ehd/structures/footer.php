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

if ( ! function_exists( '__extra_wp_footer' ) ) {
	add_action( 'wp_footer', '__extra_wp_footer', 998 );
	/**
	 * @return void
	 */
	function __extra_wp_footer() : void
	{
		/** Body scripts - BOTTOM */
		echo "\n";
		$html_body_bottom = Helper::getCustomPostContent( 'html_body_bottom', true );
		if ($html_body_bottom) {
			echo $html_body_bottom;
		}
		echo "\n";

		/** Set delay timeout milisecond */
		//$timeout = 5000;
		//$inline_js = 'const loadScriptsTimer=setTimeout(loadScripts,' . $timeout . ');const userInteractionEvents=["mouseover","keydown","touchstart","touchmove","wheel"];userInteractionEvents.forEach(function(event){window.addEventListener(event,triggerScriptLoader,{passive:!0})});function triggerScriptLoader(){loadScripts();clearTimeout(loadScriptsTimer);userInteractionEvents.forEach(function(event){window.removeEventListener(event,triggerScriptLoader,{passive:!0})})}';
		//$inline_js .= "function loadScripts(){document.querySelectorAll(\"script[data-type='lazy']\").forEach(function(elem){elem.setAttribute(\"src\",elem.getAttribute(\"data-src\"));elem.removeAttribute(\"data-src\");elem.removeAttribute(\"data-type\");})}";
		//echo '<script src="data:text/javascript;base64,' . base64_encode($inline_js) . '"></script>';
	}
}

// -----------------------------------------------
// ehd_after_footer
// -----------------------------------------------

if ( ! function_exists( '__ehd_after_footer' ) ) {
	add_action( 'ehd_after_footer', '__ehd_after_footer', 10 );
	/**
	 * @return void
	 */
	function __ehd_after_footer() : void
	{
		/** Footer scripts */
		$html_footer = Helper::getCustomPostContent( 'html_footer', true );
		if ($html_footer) {
			echo $html_footer;
		}
		echo "\n";
	}
}

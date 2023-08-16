<?php
/**
 * Header elements
 *
 * @author WEBHD
 * @package ehd
 */

use EHD_Cores\Helper;

\defined( 'ABSPATH' ) || die;

// -----------------------------------------------
// wp_head hook
// -----------------------------------------------

if ( ! function_exists( '__wp_head' ) ) {
	add_action( 'wp_head', '__wp_head', 1 );

	/**
	 * @return void
	 */
	function __wp_head() : void {

		// Add viewport to wp_head
		echo apply_filters( 'ehd_meta_viewport', '<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0" />' );  // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		// Add a ping-back url auto-discovery header for singularly identifiable articles.
		if ( is_singular() && pings_open() ) {
			printf( '<link rel="pingback" href="%s" />', esc_url( get_bloginfo( 'pingback_url' ) ) );
		}

		// Theme color
		$theme_color = Helper::getThemeMod( 'theme_color_setting' );
		if ( $theme_color ) {
			echo '<meta name="theme-color" content="' . $theme_color . '" />';
		}
	}
}

// -----------------------------------------------
// ehd_before_header hook
// -----------------------------------------------

if ( ! function_exists( '__ehd_skip_to_content_link' ) ) {
	add_action( 'ehd_before_header', '__ehd_skip_to_content_link', 2 );

	/**
	 * Add skip to content link before the header.
	 *
	 * @return void
	 */
	function __ehd_skip_to_content_link(): void {
		printf(
			'<a class="screen-reader-text skip-link" href="#content" title="%1$s">%2$s</a>',
			esc_attr__( 'Skip to content', EHD_TEXT_DOMAIN ),
			esc_html__( 'Skip to content', EHD_TEXT_DOMAIN )
		);
	}
}

if ( ! function_exists( '__off_canvas_menu' ) ) {
	add_action( 'ehd_before_header', '__off_canvas_menu', 10 );

	/**
	 * Position canvas menu
	 *
	 * @return void
	 */
	function __off_canvas_menu(): void {

		$position = Helper::getThemeMod( 'offcanvas_menu_setting' );
		if ( ! in_array( $position, [ 'left', 'right', 'top', 'bottom' ] ) ) {
			$position = 'right';
		}

		// Check if offCanvas_Widget active
		if ( is_active_widget( false, false, 'w-offcanvas', true ) ) {
			get_template_part( 'template-parts/header/off-canvas/' . $position );
		}
	}
}

// -----------------------------------------------
// ehd_header hook
// -----------------------------------------------

if ( ! function_exists( '__ehd_construct_header' ) ) {
	add_action( 'ehd_header', '__ehd_construct_header', 10 );

	/**
	 * @return void
	 */
	function __ehd_construct_header(): void {
		?>
		<header class="site-header" <?php echo Helper::microdata( 'header' ); ?>>
			<div class="top-header"></div>
		</header>
		<?php
	}
}

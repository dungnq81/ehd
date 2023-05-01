<?php
/**
 * Footer elements
 *
 * @author WEBHD
 * @package ehd
 */

use EHD\Cores\Helper;

\defined( 'ABSPATH' ) || die;

// -----------------------------------------------
// wp_footer hook
// -----------------------------------------------

if ( ! function_exists( '__wp_footer' ) ) {
	add_action( 'wp_footer', '__wp_footer', 98 );

	/**
	 * Build the back to top button
	 *
	 * @return void
	 */
	function __wp_footer() : void {

		$back_to_top = apply_filters( 'ehd_back_to_top', true );
		if ( $back_to_top ) {
			echo "\n";
			echo apply_filters( // phpcs:ignore
				'end_back_to_top_output',
				sprintf(
					'<a title="%1$s" aria-label="%1$s" rel="nofollow" href="#" class="back-to-top toTop" data-scroll-speed="%2$s" data-start-scroll="%3$s" data-glyph=""></a>',
					esc_attr__( 'Scroll back to top', EHD_TEXT_DOMAIN ),
					absint( apply_filters( 'ehd_back_to_top_scroll_speed', 400 ) ),
					absint( apply_filters( 'ehd_back_to_top_start_scroll', 300 ) ),
				)
			);
		}
	}
}

// -----------------------------------------------
// ehd_footer hook
// -----------------------------------------------

if ( ! function_exists( '__construct_footer_widgets' ) ) {
	add_action( 'ehd_footer', '__construct_footer_widgets', 5 );

	/**
	 * Build our footer widgets
	 *
	 * @return void
	 */
	function __construct_footer_widgets() {

		$rows = (int) Helper::getThemeMod( 'footer_row_setting' );
		$regions = (int) Helper::getThemeMod( 'footer_col_setting' );

		// If no footer widgets exist, we don't need to continue
		if ( 1  > $rows || 1 > $regions) {
			return;
		}

		?>
		<div id="footer-widgets" class="footer-widgets">
			<?php
			for ( $row = 1; $row <= $rows; $row ++ ) :

				// Defines the number of active columns in this footer row.
				for ( $region = $regions; 0 < $region; $region -- ) {
					if ( is_active_sidebar( 'ehd-footer-' . esc_attr( $region + $regions * ( $row - 1 ) ) ) ) {
						$columns = $region;
						break;
					}
				}

				if ( isset( $columns ) ) :
			?>
			<div class="inside-footer-widgets footer-rows footer-row-<?php echo $row; ?>">
				<div class="grid-x">
					<?php
					for ( $column = 1; $column <= $columns; $column ++ ) :
						$footer_n = $column + $regions * ( $row - 1 );
						if ( is_active_sidebar( 'ehd-footer-' . esc_attr( $footer_n ) ) ) :

							echo sprintf( '<div class="cell footer-cell cell-%1$s">', esc_attr( $column ) );
							dynamic_sidebar( 'ehd-footer-' . esc_attr( $footer_n ) );
							echo "</div>";

						endif;
					endfor;

					?>
				</div>
			</div>
			<?php endif; endfor; ?>
		</div><!-- #footer-widgets-->
	<?php
	}
}

if ( ! function_exists( '__construct_footer' ) ) {
	add_action( 'ehd_footer', '__construct_footer', 10 );

	/**
	 * Build our footer
	 *
	 * @return void
	 */
	function __construct_footer() {

		?>
		<footer class="site-info" role="contentinfo">
			<div class="inside-site-info">
				<?php

				?>
			</div>
		</footer>
	<?php
	}
}

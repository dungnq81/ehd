<?php

namespace EHD_Cores\Traits;

\defined( 'ABSPATH' ) || die;

trait WooCommerce {
	/**
	 * Default loop columns on product archives
	 *
	 * @return integer products per row
	 */
	public static function wc_loop_columns(): int {
		$columns = 4; // 4 products per row

		if ( function_exists( 'wc_get_default_products_per_row' ) ) {
			$columns = wc_get_default_products_per_row();
		}

		return apply_filters( 'wc_loop_columns', $columns );
	}

	// -------------------------------------------------------------

	/**
	 * Validates whether the Woo Cart instance is available in the request
	 *
	 * @return bool
	 */
	public static function wc_cart_available(): bool {
		$woo = \WC();

		return $woo instanceof \WooCommerce && $woo->cart instanceof \WC_Cart;
	}

	// -------------------------------------------------------------

	/**
	 * Displayed a link to the cart including the number of items present and the cart total
	 *
	 * @return void
	 */
	public static function wc_cart_link(): void {
		if ( ! self::wc_cart_available() ) {
			return;
		}
		?>
        <a class="shopping-cart-contents" href="<?php echo esc_url( wc_get_cart_url() ); ?>"
           title="<?php echo esc_attr__( 'View your shopping cart', EHD_PLUGIN_TEXT_DOMAIN ); ?>">
			<?php echo wp_kses_post( WC()->cart->get_cart_subtotal() ); ?>
            <span class="icon" data-glyph=""></span>
            <span class="count"><?php echo wp_kses_data( sprintf( '%d', WC()->cart->get_cart_contents_count() ) ); ?></span>
            <span class="txt"><?php echo __( 'Cart', EHD_PLUGIN_TEXT_DOMAIN ) ?></span>
        </a>
		<?php
	}
}

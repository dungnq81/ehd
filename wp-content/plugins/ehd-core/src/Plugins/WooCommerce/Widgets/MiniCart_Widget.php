<?php

namespace EHD_Plugins\WooCommerce\Widgets;

use EHD_Cores\Helper;
use WC_Widget_Cart;

\defined( 'ABSPATH' ) || die;

class MiniCart_Widget extends WC_Widget_Cart {
	/**
	 * Creating widget front-end
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		if ( apply_filters( 'woocommerce_widget_cart_is_hidden', is_cart() || is_checkout() ) ) {
			return;
		}

		$hide_if_empty = empty( $instance['hide_if_empty'] ) ? 0 : 1;
		$title         = apply_filters( 'widget_title', $this->get_instance_title( $instance ), $instance, $this->id_base );

		echo $args['before_widget']; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
		if ( $title ) {
			echo '<span class="cart-title">' . $title . '</span>';
		}

		$class = 'menu-item';
		if ( is_cart() || is_checkout() ) {
			$class .= ' current-menu-item';
		}

		?>
		<ul id="shopping-cart" class="shopping-cart menu">
			<li class="<?php echo esc_attr( $class ); ?>">
				<?php Helper::wc_cart_link(); ?>
			</li>
			<li class="widget-menu-item menu-item">
				<?php
				if ( $hide_if_empty ) {
					echo '<div class="hide_cart_widget_if_empty">';
				}

				// Insert cart widget placeholder - code in woocommerce.js will update this on page load.
				echo '<div class="widget_shopping_cart_content"></div>';

				if ( $hide_if_empty ) {
					echo '</div>';
				}
				?>
			</li>
		</ul>
		<?php
		echo $args['after_widget']; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
	}
}

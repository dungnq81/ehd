<?php

namespace EHD\Plugins\Core\Traits;

\defined('ABSPATH') || die;

trait WooCommerce
{
    // -------------------------------------------------------------

    /**
     * Validates whether the Woo Cart instance is available in the request
     *
     * @return bool
     */
    public static function wc_cart_available()
    {
        $woo = \WC();
        return $woo instanceof \WooCommerce && $woo->cart instanceof \WC_Cart;
    }

    // -------------------------------------------------------------

    /**
     * Displayed a link to the cart including the number of items present and the cart total
     *
     * @return void
     */
    public static function wc_cart_link()
    {
        if (!self::wc_cart_available()) {
            return;
        }
        ?>
        <a class="shopping-cart-contents" href="<?php echo esc_url(wc_get_cart_url()); ?>"
           title="<?php echo esc_attr__('View your shopping cart', EHD_PLUGIN_TEXT_DOMAIN); ?>">
            <?php echo wp_kses_post(WC()->cart->get_cart_subtotal()); ?>
            <span class="icon" data-glyph=""></span>
            <span class="count"><?php echo wp_kses_data(sprintf('%d', WC()->cart->get_cart_contents_count())); ?></span>
            <span class="txt"><?php echo __('Cart', EHD_PLUGIN_TEXT_DOMAIN) ?></span>
        </a>
        <?php
    }
}

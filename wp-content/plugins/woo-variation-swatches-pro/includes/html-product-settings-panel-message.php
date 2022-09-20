<?php
    defined( 'ABSPATH' ) or die( 'Keep Quit' );
    
    /**
     * @var $post
     * @var $wpdb
     * @var $product_object
     * @var $attributes
     * @var $settings
     */
    $product_id = $product_object->get_id();

?>
<div data-product_id="<?php echo esc_attr( $product_id ) ?>" id="woo_variation_swatches_variation_product_options" class="woo-variation-swatches-variation-product-options-wrapper panel wc-metaboxes-wrapper hidden">

    <div class="inline notice woocommerce-message">
        <p><?php echo wp_kses_post( __( 'Please save this product and set custom settings for variation swatches.', 'woo-variation-swatches-pro' ) ); ?></p>
    </div>
</div>
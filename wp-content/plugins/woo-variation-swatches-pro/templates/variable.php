<?php
    
    defined( 'ABSPATH' ) || exit;
    
    /**
     * @var $available_variations
     * @var $attributes
     * @var $selected_attributes
     * @var $variation_threshold_min
     * @var $variation_threshold_max
     * @var $total_children
     */
    
    global $product, $woocommerce_loop;
    
    // Exclude Category
    $global_exclude_categories = map_deep( woo_variation_swatches()->get_option( 'exclude_categories', array() ), 'absint' );
    $product_cats_ids          = wc_get_product_term_ids( $product->get_id(), 'product_cat' );
    
    $exclude = array_filter( $global_exclude_categories, function ( $value ) use ( $product_cats_ids ) {
        return in_array( $value, $product_cats_ids );
    } );
    
    if ( ! empty( $exclude ) ) {
        return __return_empty_string();
    }
    
    
    $attribute_keys      = array_keys( $attributes );
    $variations_json     = wp_json_encode( $available_variations );
    $variations_attr     = wc_esc_json( $variations_json );
    $clear_on_archive    = wc_string_to_bool( woo_variation_swatches()->get_option( 'show_clear_on_archive', 'yes' ) );
    $enable_catalog_mode = wc_string_to_bool( woo_variation_swatches()->get_option( 'enable_catalog_mode', 'no' ) );
    
    $local_catalog_mode_attribute = sanitize_text_field( woo_variation_swatches()->get_product_settings( $product, 'catalog_mode_attribute' ) );
    
    $global_catalog_mode_attribute = sanitize_text_field( woo_variation_swatches()->get_option( 'catalog_mode_attribute', '' ) );
    
    $catalog_mode_attribute = empty( $local_catalog_mode_attribute ) ? $global_catalog_mode_attribute : $local_catalog_mode_attribute;
    
    $disable_catalog_mode_on_single_attribute = wc_string_to_bool( woo_variation_swatches()->get_option( 'disable_catalog_mode_on_single_attribute', 'no' ) );
    
    // wp_enqueue_script( 'wc-add-to-cart-variation' );
    
    do_action( 'woo_variation_swatches_archive_before_add_to_cart_form' );
    
    
    if ( $enable_catalog_mode ) {
        $have_catalog_attribute = false;
        
        if ( empty( $catalog_mode_attribute ) ) {
            $catalog_mode_attribute = wc_variation_attribute_name( array_key_first( $attributes ) );
        } else {
            $catalog_mode_attribute = wc_variation_attribute_name( $catalog_mode_attribute );
        }
        
        foreach ( $attributes as $attr_name => $opt ) {
            $attribute_name = wc_variation_attribute_name( $attr_name );
            
            if ( $attribute_name === $catalog_mode_attribute ) {
                $have_catalog_attribute = true;
            }
        }
        
        if ( ! $have_catalog_attribute ) {
            return;
        }
    }
    
    $total_attribute = count( array_keys( $attributes ) );
?>

    <div class="wvs-archive-variations-wrapper" data-threshold_min="<?php echo absint( $variation_threshold_min ) ?>" data-total_attribute="<?php echo absint( $total_attribute ) ?>" data-threshold_max="<?php echo absint( $variation_threshold_max ) ?>" data-total_children="<?php echo absint( $total_children ) ?>" data-product_id="<?php echo absint( $product->get_id() ); ?>" data-product_variations="<?php echo $variations_attr; // WPCS: XSS ok. ?>">
        
        <?php do_action( 'woo_variation_swatches_archive_before_variations_form' ); ?>
        
        <?php if ( empty( $available_variations ) && false !== $available_variations ) : ?>
            <p class="stock out-of-stock"><?php echo esc_html( apply_filters( 'woocommerce_out_of_stock_message', esc_html__( 'This product is currently out of stock and unavailable.', 'woo-variation-swatches-pro' ) ) ); ?></p>
        <?php else : ?>
            <ul class="variations">
                <?php
                    
                    foreach ( $attributes as $attribute => $options ) :
                        
                        $attribute_name = wc_variation_attribute_name( $attribute );
                        
                        if ( $enable_catalog_mode && $attribute_name !== $catalog_mode_attribute ) {
                            continue;
                        }
                        
                        ?>
                        <li class="woo-variation-items-wrapper">
                            <?php
                                wc_dropdown_variation_attribute_options( array(
                                                                             'options'    => $options,
                                                                             'attribute'  => $attribute,
                                                                             'product'    => $product,
                                                                             'is_archive' => true
                                                                         ) );
                            ?>
                        </li>
                    <?php endforeach; ?>
                
                <?php if ( $clear_on_archive && ! $enable_catalog_mode ): ?>
                    <li class=" wvs_archive_reset_variations">
                        <a class="wvs_archive_reset_variations_link" href="#"><?php esc_html_e( 'Clear', 'woo-variation-swatches-pro' ) ?></a>
                    </li>
                <?php endif; ?>
                
                <?php if ( $disable_catalog_mode_on_single_attribute && $clear_on_archive && $enable_catalog_mode && $total_attribute === 1 ): ?>
                    <li class=" wvs_archive_reset_variations">
                        <a class="wvs_archive_reset_variations_link" href="#"><?php esc_html_e( 'Clear', 'woo-variation-swatches-pro' ) ?></a>
                    </li>
                <?php endif; ?>
            </ul>

            <div class="wvs-archive-information"></div>
        
        <?php endif; ?>
        
        <?php do_action( 'woo_variation_swatches_archive_after_variations_form' ); ?>
    </div>

<?php do_action( 'woo_variation_swatches_archive_after_add_to_cart_form' );
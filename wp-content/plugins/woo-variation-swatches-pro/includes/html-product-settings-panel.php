<?php
    defined( 'ABSPATH' ) or die( 'Keep Quit' );
    
    /**
     * @var $post
     * @var $wpdb
     * @var $product_object
     * @var $attributes
     * @var $settings
     */
    
    // $attributes = $product_object->get_attributes();
    $product_id = $product_object->get_id();
    
    $product_swatches_data = array();
?>

<div data-product_id="<?php echo esc_attr( $product_id ) ?>" id="woo_variation_swatches_variation_product_options" class="woo-variation-swatches-variation-product-options-wrapper panel wc-metaboxes-wrapper hidden">

    <div id="woo_variation_swatches_variation_product_options_inner">
        
        <?php if ( empty( $attributes ) ): ?>
            <div class="inline notice woocommerce-message">
                <p><?php echo wp_kses_post( __( 'Before you can add a variation you need to add some variation attributes on the <strong>Attributes</strong> tab.', 'woo-variation-swatches-pro' ) ); ?></p>
                <p><a target="_blank" class="button-primary" href="<?php echo esc_url( apply_filters( 'woocommerce_docs_url', 'https://docs.woocommerce.com/document/variable-product/', 'product-variations' ) ); ?>"><?php esc_html_e( 'Learn more', 'woo-variation-swatches-pro' ); ?></a></p>
            </div>
        <?php else: ?>

            <div id="saved-message" class="inline notice woocommerce-message">
                <p><?php esc_html_e( 'Settings saved', 'woo-variation-swatches-pro' ); ?></p>
            </div>

            <div id="woo-variation-swatches-variation-product-option-settings-wrapper">

                <div class="product-label-settings">

                    <div class="form-settings-group">
                        
                        <?php
                            $id      = woo_variation_swatches()->get_backend()->get_edit_panel()->settings_id( 'default_to_button' );
                            $name    = woo_variation_swatches()->get_backend()->get_edit_panel()->settings_name( 'default_to_button' );
                            $local   = woo_variation_swatches()->get_backend()->get_edit_panel()->settings_value( $settings, 'default_to_button' );
                            $current = empty( $local ) ? '' : $local;
                        ?>

                        <div class="form-label">
                            <label for="<?php echo esc_attr( $id ) ?>">
                                <?php esc_html_e( 'Dropdowns to Button', 'woo-variation-swatches-pro' ) ?>
                            </label>
                            <?php echo wc_help_tip( esc_html__( 'Enable Dropdown to Button', 'woo-variation-swatches-pro' ) ) ?>
                        </div>

                        <div class="form-field">
                            <select style="width: 200px" class="wc-enhanced-select" id="<?php echo esc_attr( $id ) ?>" name="<?php echo esc_attr( $name ) ?>">
                                <option <?php selected( $current, '' ) ?> value=""><?php esc_html_e( 'Global', 'woo-variation-swatches-pro' ) ?></option>
                                <option <?php selected( $current, 'yes' ) ?> value="yes"><?php esc_html_e( 'Yes', 'woo-variation-swatches-pro' ) ?></option>
                                <option <?php selected( $current, 'no' ) ?> value="no"><?php esc_html_e( 'No', 'woo-variation-swatches-pro' ) ?></option>
                            </select>

                        </div>
                    </div>

                    <div class="form-settings-group">
                        
                        <?php
                            $id      = woo_variation_swatches()->get_backend()->get_edit_panel()->settings_id( 'default_to_image' );
                            $name    = woo_variation_swatches()->get_backend()->get_edit_panel()->settings_name( 'default_to_image' );
                            $local   = woo_variation_swatches()->get_backend()->get_edit_panel()->settings_value( $settings, 'default_to_image' );
                            $current = empty( $local ) ? '' : $local;
                        ?>

                        <div class="form-label">
                            <label for="<?php echo esc_attr( $id ) ?>">
                                <?php esc_html_e( 'Dropdowns to Image', 'woo-variation-swatches-pro' ) ?>
                            </label>
                            <?php echo wc_help_tip( esc_html__( 'Enable Dropdown to Image', 'woo-variation-swatches-pro' ) ) ?>
                        </div>

                        <div class="form-field">

                            <select style="width: 200px" class="wc-enhanced-select" id="<?php echo esc_attr( $id ) ?>" name="<?php echo esc_attr( $name ) ?>">
                                <option <?php selected( $current, '' ) ?> value=""><?php esc_html_e( 'Global', 'woo-variation-swatches-pro' ) ?></option>
                                <option <?php selected( $current, 'yes' ) ?> value="yes"><?php esc_html_e( 'Yes', 'woo-variation-swatches-pro' ) ?></option>
                                <option <?php selected( $current, 'no' ) ?> value="no"><?php esc_html_e( 'No', 'woo-variation-swatches-pro' ) ?></option>
                            </select>

                            <span class="description"><?php esc_html_e( 'If variation has an image', 'woo-variation-swatches-pro' ) ?></span>

                        </div>
                    </div>

                    <div class="form-settings-group">
                        
                        <?php
                            $id      = woo_variation_swatches()->get_backend()->get_edit_panel()->settings_id( 'default_image_type_attribute' );
                            $name    = woo_variation_swatches()->get_backend()->get_edit_panel()->settings_name( 'default_image_type_attribute' );
                            $local   = woo_variation_swatches()->get_backend()->get_edit_panel()->settings_value( $settings, 'default_image_type_attribute' );
                            $current = empty( $local ) ? '' : $local;
                        ?>

                        <div class="form-label">
                            <label for="<?php echo esc_attr( woo_variation_swatches()->get_backend()->get_edit_panel()->settings_id( 'default_image_type_attribute' ) ) ?>">
                                <?php esc_html_e( 'Dropdown to Image Attribute', 'woo-variation-swatches-pro' ) ?>
                            </label>
                            <?php echo wc_help_tip( esc_html__( 'Dropdown to Image attribute', 'woo-variation-swatches-pro' ) ) ?>
                        </div>

                        <div class="form-field">
                            <select style="width: 300px" class="wc-enhanced-select" id="<?php echo esc_attr( $id ) ?>" name="<?php echo esc_attr( $name ) ?>">
                                <option <?php selected( $current, '' ) ?> value=""><?php esc_html_e( 'Global', 'woo-variation-swatches-pro' ) ?></option>
                                <?php foreach ( $attributes as $attribute_key => $attribute ): ?>
                                    <option <?php selected( $current, $attribute_key ) ?> value="<?php echo esc_attr( $attribute_key ) ?>"><?php echo esc_html( sprintf( '%s ( %s )', $attribute[ 'taxonomy' ][ 'attribute_label' ], $attribute[ 'taxonomy' ][ 'attribute_name' ] ) ); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <?php if ( wc_string_to_bool( woo_variation_swatches()->get_option( 'enable_catalog_mode', 'no' ) ) ): ?>
                        <div class="form-settings-group">
                            
                            <?php
                                $id    = woo_variation_swatches()->get_backend()->get_edit_panel()->settings_id( 'catalog_mode_attribute' );
                                $name  = woo_variation_swatches()->get_backend()->get_edit_panel()->settings_name( 'catalog_mode_attribute' );
                                $local = woo_variation_swatches()->get_backend()->get_edit_panel()->settings_value( $settings, 'catalog_mode_attribute' );
                                
                                $current = empty( $local ) ? '' : $local;
                            ?>
                            <div class="form-label">
                                <label for="<?php echo esc_attr( $id ) ?>"><?php esc_html_e( 'Catalog mode attribute', 'woo-variation-swatches-pro' ) ?></label>
                                <?php echo wc_help_tip( esc_html__( 'Catalog mode attribute', 'woo-variation-swatches-pro' ) ) ?>
                            </div>

                            <div class="form-field">

                                <select style="width: 300px" class="wc-enhanced-select" id="<?php echo esc_attr( $id ) ?>" name="<?php echo esc_attr( $name ) ?>">

                                    <option <?php selected( $current, '' ) ?> value=""><?php esc_html_e( 'Global', 'woo-variation-swatches-pro' ) ?></option>
                                    
                                    <?php foreach ( $attributes as $attribute_key => $attribute ): ?>
                                        <option <?php selected( $current, $attribute_key ) ?> value="<?php echo esc_attr( $attribute_key ) ?>"><?php echo esc_html( sprintf( '%s ( %s )', $attribute[ 'taxonomy' ][ 'attribute_label' ], $attribute[ 'taxonomy' ][ 'attribute_name' ] ) ); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ( wc_string_to_bool( woo_variation_swatches()->get_option( 'enable_single_variation_preview', 'no' ) ) ): ?>
                        <div class="form-settings-group">
                            <?php
                                $id      = woo_variation_swatches()->get_backend()->get_edit_panel()->settings_id( 'single_variation_preview_attribute' );
                                $name    = woo_variation_swatches()->get_backend()->get_edit_panel()->settings_name( 'single_variation_preview_attribute' );
                                $local   = woo_variation_swatches()->get_backend()->get_edit_panel()->settings_value( $settings, 'single_variation_preview_attribute' );
                                $current = empty( $local ) ? '' : $local;
                            ?>
                            <div class="form-label">
                                <label for="<?php echo esc_attr( $id ) ?>"><?php esc_html_e( 'Single Variation Image Preview Attribute', 'woo-variation-swatches-pro' ) ?></label>
                                <?php echo wc_help_tip( esc_html__( 'Single Variation Image Preview Attribute', 'woo-variation-swatches-pro' ) ) ?>
                            </div>

                            <div class="form-field">

                                <select style="width: 300px" class="wc-enhanced-select" id="<?php echo esc_attr( $id ) ?>" name="<?php echo esc_attr( $name ) ?>">

                                    <option <?php selected( $current, '' ) ?> value=""><?php esc_html_e( 'Global', 'woo-variation-swatches-pro' ) ?></option>
                                    
                                    <?php foreach ( $attributes as $attribute_key => $attribute ): ?>
                                        <option <?php selected( $current, $attribute_key ) ?> value="<?php echo esc_attr( $attribute_key ) ?>"><?php echo esc_html( sprintf( '%s ( %s )', $attribute[ 'taxonomy' ][ 'attribute_label' ], $attribute[ 'taxonomy' ][ 'attribute_name' ] ) ); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>

                <div class="product-attribute-label-settings">
                    
                    <?php
                        // Attribute label settings
                        include_once dirname( __FILE__ ) . '/html-product-attribute-settings-panel.php';
                    ?>
                </div>

            </div>

            <div class="toolbar">
                <button type="button" data-product_id="<?php echo esc_attr( $product_id ) ?>" class="button woo_variation_swatches_save_product_attributes button-primary"><?php esc_html_e( 'Save swatches settings', 'woo-variation-swatches-pro' ) ?></button>
                <button type="button" data-product_id="<?php echo esc_attr( $product_id ) ?>" class="button woo_variation_swatches_reset_product_attributes button"><?php esc_html_e( 'Reset to default', 'woo-variation-swatches-pro' ) ?></button>
            </div>
        <?php endif; ?>
    </div>
</div>

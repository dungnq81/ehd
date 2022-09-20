<?php
    
    defined( 'ABSPATH' ) or die( 'Keep Quit' );
    
    /**
     * @var array $attributes
     * @var array $attribute
     * @var       $settings
     * @var       $attribute_key
     * @var       $product_id
     * @var       $limit
     */
?>

<!--
<h2 class="woo-variation-swatches-settings-heading"><span class="dashicons dashicons-admin-settings"></span><?php /*esc_html_e( 'Attribute level settings', 'woo-variation-swatches-pro' ) */ ?></h2>
-->
<?php foreach ( $attributes as $attribute ) :
    
    
    $attribute_key = $attribute[ 'attribute_key' ];
    $attribute_name = $attribute[ 'attribute_name' ];
    $attribute_id = $attribute[ 'attribute_id' ];
    $attribute_type = $attribute[ 'attribute_type' ];
    
    $total      = count( $attribute[ 'terms' ] );
    $pages      = ceil( $total / $limit );
    $first_page = 1;
    $last_page  = ( $pages * $limit ) - $limit;
    
    ?>
    <div class="woo-variation-swatches-attribute-options-wrapper wc-metabox closed <?php echo( empty( $attribute[ 'is_taxonomy' ] ) ? 'not_a_taxonomy' : 'is_a_taxonomy' ) ?>">

        <h4 class="woo-variation-swatches-attribute-header">
            <strong class="attribute-label"><?php echo esc_html( $attribute[ 'taxonomy' ][ 'attribute_label' ] ) ?></strong>

            <div class="form-settings-group-inline">
                <?php
                    $required_id       = $id = woo_variation_swatches()->get_backend()->get_edit_panel()->settings_id( $attribute_key, 'type' );
                    $name              = woo_variation_swatches()->get_backend()->get_edit_panel()->settings_name( $attribute_key, 'type' );
                    $local             = woo_variation_swatches()->get_backend()->get_edit_panel()->settings_value( $settings, $attribute_key, 'type' );
                    $current           = is_null( $local ) ? $attribute_type : sanitize_text_field( $local );
                    $default_type_name = woo_variation_swatches()->get_backend()->get_edit_panel()->settings_name( $attribute_key, 'default_type' );
                    
                    $mode_class = empty( $local ) ? 'new-mode' : 'edit-mode';
                ?>
                <div class="form-label">
                    <label for="<?php echo esc_attr( $id ) ?>">
                        <?php esc_html_e( 'Attribute type', 'woo-variation-swatches-pro' ) ?>
                        <?php echo wc_help_tip( esc_html__( 'Change Attribute type', 'woo-variation-swatches-pro' ) ) ?>
                    </label>
                </div>

                <div class="form-field">

                    <input type="hidden" name="<?php echo esc_attr( $default_type_name ) ?>" value="<?php echo esc_attr( $attribute_type ) ?>">
                    
                    <?php
                        $extended_attribute_types = (array) woo_variation_swatches()->get_backend()->extended_attribute_types();
                    ?>
                    <select id="<?php echo esc_attr( $id ) ?>" style="width: 200px" class="wc-enhanced-select woo_variation_swatches_attribute_type_switch <?php echo esc_attr( $mode_class ) ?>" name="<?php echo esc_attr( $name ) ?>">
                        
                        <?php foreach ( $extended_attribute_types as $key => $value ): ?>
                            
                            <?php if ( $attribute_type === $key ): ?>
                                <option <?php selected( $current, $key ) ?> value="<?php echo esc_attr( $key ) ?>"><?php echo esc_html( $value ) ?> (<?php esc_html_e( 'Default', 'woo-variation-swatches-pro' ) ?>)</option>
                            <?php else: ?>
                                <option <?php selected( $current, $key ) ?> value="<?php echo esc_attr( $key ) ?>"><?php echo esc_html( $value ) ?></option>
                            <?php endif; ?>
                        
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

        </h4>

        <div class="woo-variation-swatches-attribute-data wc-metabox-content hidden">
            <div class="woo-variation-swatches-attribute-data-inner">

                <div class="product-attribute-label-settings-group">
                    
                    <?php
                        $required = woo_variation_swatches()->get_backend()->get_edit_panel()->normalize_required_attribute( array( $required_id => array( 'type' => 'equal', 'value' => array( 'color', 'image', 'button', 'custom', 'mixed' ) ) ) );
                    ?>

                    <div class="form-settings-group" data-gwp_dependency="<?php echo wc_esc_json( wp_json_encode( $required ) ) ?>">
                        
                        <?php
                            $id      = woo_variation_swatches()->get_backend()->get_edit_panel()->settings_id( $attribute_key, 'style' );
                            $name    = woo_variation_swatches()->get_backend()->get_edit_panel()->settings_name( $attribute_key, 'style' );
                            $local   = woo_variation_swatches()->get_backend()->get_edit_panel()->settings_value( $settings, $attribute_key, 'style' );
                            $current = empty( $local ) ? '' : $local;
                        ?>

                        <div class="form-label">
                            <label for="<?php echo esc_attr( $id ) ?>">
                                <?php esc_html_e( 'Shape Style', 'woo-variation-swatches-pro' ) ?>
                            </label>
                        </div>

                        <div class="form-field">

                            <select id="<?php echo esc_attr( $id ) ?>" style="width: 300px" class="wc-enhanced-select" name="<?php echo esc_attr( $name ) ?>">
                                <option <?php selected( $current, '' ) ?> value=""><?php esc_html_e( 'Global', 'woo-variation-swatches-pro' ) ?></option>
                                <option <?php selected( $current, 'rounded' ) ?> value="rounded"><?php esc_html_e( 'Rounded Shape', 'woo-variation-swatches-pro' ) ?></option>
                                <option <?php selected( $current, 'squared' ) ?> value="squared"><?php esc_html_e( 'Squared Shape', 'woo-variation-swatches-pro' ) ?></option>
                            </select>

                        </div>
                    </div>


                </div> <!-- .product-attribute-label-settings-group -->

                <div class="product-term-label-settings">

                    <div class="product-term-label-settings-contents" data-product_id="<?php echo esc_attr( $product_id ) ?>" data-attribute_id="<?php echo esc_attr( $attribute_id ) ?>" data-attribute_name="<?php echo esc_attr( $attribute_name ) ?>" data-current="1" data-pages="<?php echo esc_attr( $pages ) ?>" data-limit="<?php echo esc_attr( $limit ) ?>" data-total="<?php echo esc_attr( $total ) ?>">
                        <?php
                            
                            $terms = woo_variation_swatches()->get_backend()->get_edit_panel()->get_sliced_terms( $attribute[ 'terms' ], 0, $limit );
                            
                            // Attribute label settings
                            include dirname( __FILE__ ) . '/html-product-attribute-term-settings-panel.php';
                        ?>
                    </div>
                    
                    <?php if ( $total > $limit ): ?>
                        <div class="product-term-label-settings-pagination">
                            <div class="tablenav-pages">
                                <div class="displaying-num"><?php printf( /* translators: Number of items. */ _n( '%s item', '%s items', count( $attribute[ 'terms' ] ) ), number_format_i18n( count( $attribute[ 'terms' ] ) ) ) ?></div>
                                <div class="pagination-links">
                                    <a class="first-page button disabled" data-page="1" href="#"><span class="screen-reader-text"><?php esc_html_e( 'First page', 'woo-variation-swatches-pro' ) ?></span><span aria-hidden="true">&laquo;</span></a>
                                    <a class="prev-page button disabled" href="#">
                                        <span class="screen-reader-text"><?php esc_html_e( 'Previous page', 'woo-variation-swatches-pro' ) ?></span>
                                        <span aria-hidden="true">&lsaquo;</span>
                                    </a>
                                    <span class="screen-reader-text"><?php esc_html_e( 'Current Page', 'woo-variation-swatches-pro' ) ?></span>
                                    <span id="table-paging" class="paging-input">
                                        <span class="tablenav-paging-text">
                                            <span class="current-page"><?php echo number_format_i18n( '1' ) ?></span>
                                            <?php esc_html_e( 'of', 'woo-variation-swatches-pro' ) ?>
                                            <span class="total-pages"><?php echo number_format_i18n( $pages ) ?></span>
                                        </span>
                                    </span>
                                    <a class="next-page button" href="#">
                                        <span class="screen-reader-text"><?php esc_html_e( 'Next page', 'woo-variation-swatches-pro' ) ?></span>
                                        <span aria-hidden="true">&rsaquo;</span>
                                    </a>
                                    <a class="last-page button" data-page="<?php echo esc_attr( $pages ) ?>" href="#">
                                        <span class="screen-reader-text"><?php esc_html_e( 'Last page', 'woo-variation-swatches-pro' ) ?></span>
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                </div> <!-- .product-term-label-settings -->
            </div>
        </div>

    </div>

<?php endforeach; ?>

<?php
    
    defined( 'ABSPATH' ) or die( 'Keep Quit' );
    
    /**
     * @var array   $attribute
     * @var array   $terms
     * @var         $settings
     * @var         $term_id
     * @var         $attribute_key
     * @var   array $available_attribute_types
     */
    
    /*echo '<pre>';
    print_r( $attribute );
    echo '</pre>';*/
    
    $available_attribute_types = woo_variation_swatches()->get_backend()->filtered_attribute_types();
    
    $attribute_key  = $attribute[ 'attribute_key' ];
    $attribute_type = $attribute[ 'attribute_type' ];
    $required_id    = woo_variation_swatches()->get_backend()->get_edit_panel()->settings_id( $attribute_key, 'type' );
    
    $required_type         = woo_variation_swatches()->get_backend()->get_edit_panel()->normalize_required_attribute( array( $required_id => array( 'type' => 'equal', 'value' => array( 'color', 'image', 'button', 'mixed' ) ) ) );
    $tooltip_required_type = array( 'color', 'image', 'button', 'radio', 'mixed' );
    
    $available_terms = $attribute[ 'available_terms' ];
    
    foreach ( $terms as $term_id => $term ):
        // Attribute label settings
        
        $in_use = woo_variation_swatches()->get_backend()->get_edit_panel()->is_attribute_option_used_in_variation( $term[ 'variation' ], $available_terms );
        ?>

        <div class="woo-variation-swatches-attribute-term-options-wrapper wc-metabox closed">
            <h4 class="woo-variation-swatches-attribute-term-header">
                <strong class="term-label"><?php echo esc_html( $term[ 'name' ] ) ?> <?php if ( ! $in_use ): ?> - <small><?php esc_html_e( 'not used for variation', 'woo-variation-swatches-pro' ) ?></small><?php endif; ?></strong>
            </h4>

            <div class="woo-variation-swatches-attribute-term-data wc-metabox-content hidden">
                <div class="woo-variation-swatches-attribute-term-data-inner">
                    <div class="product-attribute-term-label-settings-group">

                        <div class="form-settings-group" data-gwp_dependency="<?php echo wc_esc_json( wp_json_encode( $required_type ) ) ?>">
                            
                            <?php
                                $term_type_id = $id = woo_variation_swatches()->get_backend()->get_edit_panel()->settings_id( $attribute_key, 'terms', $term_id, 'type' );
                                $name         = woo_variation_swatches()->get_backend()->get_edit_panel()->settings_name( $attribute_key, 'terms', $term_id, 'type' );
                                $local        = woo_variation_swatches()->get_backend()->get_edit_panel()->settings_value( $settings, $attribute_key, 'terms', $term_id, 'type' );
                                $current      = empty( $local ) ? $attribute_type : $local;
                                // $available_attribute_types = (array) woo_variation_swatches()->get_backend()->get_edit_panel()->available_attribute_types();
                                $mode_class = empty( $local ) ? 'new-mode' : 'edit-mode';
                            ?>

                            <div class="form-label">
                                <label for="<?php echo esc_attr( $id ) ?>">
                                    <?php esc_html_e( 'Type', 'woo-variation-swatches-pro' ) ?>
                                </label>
                            </div>

                            <div class="form-field">
                                <select id="<?php echo esc_attr( $id ) ?>" style="width: 300px" class="wc-enhanced-select woo_variation_swatches_attribute_term_type_switch <?php echo esc_attr( $mode_class ) ?>" name="<?php echo esc_attr( $name ) ?>">
                                    <option value=""><?php esc_html_e( '- Choose type -', 'woo-variation-swatches-pro' ) ?></option>
                                    <?php foreach ( $available_attribute_types as $key => $type ): ?>
                                        <?php if ( $attribute_type === $key ): ?>
                                            <option <?php selected( $current, $key ) ?> value="<?php echo esc_attr( $key ) ?>"><?php echo esc_html( $type ) ?> (<?php esc_html_e( 'Default', 'woo-variation-swatches-pro' ) ?>)</option>
                                        <?php else: ?>
                                            <option <?php selected( $current, $key ) ?> value="<?php echo esc_attr( $key ) ?>"><?php echo esc_html( $type ) ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <?php
                            $required = woo_variation_swatches()->get_backend()->get_edit_panel()->normalize_required_attribute( array( $term_type_id => array( 'type' => 'equal', 'value' => 'color' ), $required_id => array( 'type' => 'equal', 'value' => array( 'color', 'mixed' ) ) ) );
                        ?>

                        <div class="form-settings-group" data-gwp_dependency="<?php echo wc_esc_json( wp_json_encode( $required ) ) ?>">
                            
                            <?php
                                $id      = woo_variation_swatches()->get_backend()->get_edit_panel()->settings_id( $attribute_key, 'terms', $term_id, 'primary_color' );
                                $name    = woo_variation_swatches()->get_backend()->get_edit_panel()->settings_name( $attribute_key, 'terms', $term_id, 'primary_color' );
                                $local   = woo_variation_swatches()->get_backend()->get_edit_panel()->settings_value( $settings, $attribute_key, 'terms', $term_id, 'primary_color' );
                                $default = woo_variation_swatches()->get_frontend()->get_product_attribute_primary_color( $term_id );
                                $current = empty( $local ) ? $default : $local;
                            ?>

                            <div class="form-label">
                                <label for="<?php echo esc_attr( $id ) ?>">
                                    <?php esc_html_e( 'Primary Color', 'woo-variation-swatches-pro' ) ?>
                                </label>
                            </div>

                            <div class="form-field">
                                <input class="wvs-color-picker" value="<?php echo sanitize_hex_color( $current ) ?>" id="<?php echo esc_attr( $id ) ?>" type="text" name="<?php echo esc_attr( $name ) ?>">
                            </div>
                        </div>

                        <div class="form-settings-group" data-gwp_dependency="<?php echo wc_esc_json( wp_json_encode( $required ) ) ?>">
                            
                            <?php
                                $id      = woo_variation_swatches()->get_backend()->get_edit_panel()->settings_id( $attribute_key, 'terms', $term_id, 'is_duel_color' );
                                $name    = woo_variation_swatches()->get_backend()->get_edit_panel()->settings_name( $attribute_key, 'terms', $term_id, 'is_duel_color' );
                                $local   = woo_variation_swatches()->get_backend()->get_edit_panel()->settings_value( $settings, $attribute_key, 'terms', $term_id, 'is_duel_color' );
                                $default = woo_variation_swatches()->get_frontend()->get_product_attribute_is_dual_color( $term_id );
                                $current = empty( $local ) ? $default : $local;
                            ?>

                            <div class="form-label">
                                <label for="<?php echo esc_attr( $id ) ?>">
                                    <?php esc_html_e( 'Is dual color?', 'woo-variation-swatches-pro' ) ?>
                                </label>
                            </div>


                            <div class="form-field">
                                <select id="<?php echo esc_attr( $id ) ?>" style="width: 100px" class="wc-enhanced-select" name="<?php echo esc_attr( $name ) ?>">
                                    <option <?php selected( $current, '' ) ?> value=""><?php esc_html_e( 'Global', 'woo-variation-swatches-pro' ) ?></option>
                                    <option <?php selected( $current, 'yes' ) ?> value="yes"><?php esc_html_e( 'Yes', 'woo-variation-swatches-pro' ) ?></option>
                                    <option <?php selected( $current, 'no' ) ?> value="no"><?php esc_html_e( 'No', 'woo-variation-swatches-pro' ) ?></option>
                                </select>
                            </div>

                        </div>
                        
                        <?php
                            $required = woo_variation_swatches()->get_backend()->get_edit_panel()->normalize_required_attribute( array( $term_type_id => array( 'type' => 'equal', 'value' => 'color' ), $id => array( 'type' => 'equal', 'value' => 'yes' ) ) );
                        ?>

                        <div class="form-settings-group" data-gwp_dependency="<?php echo wc_esc_json( wp_json_encode( $required ) ) ?>">
                            
                            <?php
                                $id      = woo_variation_swatches()->get_backend()->get_edit_panel()->settings_id( $attribute_key, 'terms', $term_id, 'secondary_color' );
                                $name    = woo_variation_swatches()->get_backend()->get_edit_panel()->settings_name( $attribute_key, 'terms', $term_id, 'secondary_color' );
                                $local   = woo_variation_swatches()->get_backend()->get_edit_panel()->settings_value( $settings, $attribute_key, 'terms', $term_id, 'secondary_color' );
                                $default = woo_variation_swatches()->get_frontend()->get_product_attribute_secondary_color( $term_id );
                                $current = empty( $local ) ? $default : $local;
                            ?>

                            <div class="form-label">
                                <label for="<?php echo esc_attr( $id ) ?>">
                                    <?php esc_html_e( 'Secondary Color', 'woo-variation-swatches-pro' ) ?>
                                </label>
                            </div>

                            <div class="form-field">
                                <input class="wvs-color-picker" value="<?php echo sanitize_hex_color( $current ) ?>" id="<?php echo esc_attr( $id ) ?>" type="text" name="<?php echo esc_attr( $name ) ?>">
                            </div>
                        </div>
                        
                        <?php
                            $required = woo_variation_swatches()->get_backend()->get_edit_panel()->normalize_required_attribute( array( $term_type_id => array( 'type' => 'equal', 'value' => 'image' ), $required_id => array( 'type' => 'equal', 'value' => array( 'image', 'mixed' ) ) ) );
                        ?>

                        <div class="form-settings-group" data-gwp_dependency="<?php echo wc_esc_json( wp_json_encode( $required ) ) ?>">
                            
                            <?php
                                $id      = woo_variation_swatches()->get_backend()->get_edit_panel()->settings_id( $attribute_key, 'terms', $term_id, 'image_id' );
                                $name    = woo_variation_swatches()->get_backend()->get_edit_panel()->settings_name( $attribute_key, 'terms', $term_id, 'image_id' );
                                $local   = woo_variation_swatches()->get_backend()->get_edit_panel()->settings_value( $settings, $attribute_key, 'terms', $term_id, 'image_id' );
                                $default = woo_variation_swatches()->get_frontend()->get_product_attribute_image( $term_id );
                                $current = empty( $local ) ? $default : $local;
                            ?>

                            <div class="form-label">
                                <label for="<?php echo esc_attr( $id ) ?>">
                                    <?php esc_html_e( 'Image', 'woo-variation-swatches-pro' ) ?>
                                </label>
                            </div>

                            <div class="form-field">
                                <?php woo_variation_swatches()->get_backend()->get_edit_panel()->generate_image_upload_field( $id, $name, $current ) ?>
                            </div>
                        </div>
                        
                        
                        <?php
                            $required               = woo_variation_swatches()->get_backend()->get_edit_panel()->normalize_required_attribute( array( $required_id => array( 'type' => 'equal', 'value' => array( 'color', 'image', 'button', 'radio', 'mixed' ) ) ) );
                            $global_tooltip_enabled = wc_string_to_bool( woo_variation_swatches()->get_option( 'enable_tooltip', 'yes' ) );
                            
                            if ( $global_tooltip_enabled ):
                                ?>
                                <div class="form-settings-group" data-gwp_dependency="<?php echo wc_esc_json( wp_json_encode( $required ) ) ?>">
                                    
                                    <?php
                                        $show_tooltip_type_id = $id = woo_variation_swatches()->get_backend()->get_edit_panel()->settings_id( $attribute_key, 'terms', $term_id, 'show_tooltip' );
                                        $name                 = woo_variation_swatches()->get_backend()->get_edit_panel()->settings_name( $attribute_key, 'terms', $term_id, 'show_tooltip' );
                                        $local                = woo_variation_swatches()->get_backend()->get_edit_panel()->settings_value( $settings, $attribute_key, 'terms', $term_id, 'show_tooltip' );
                                        // $default              = woo_variation_swatches()->get_frontend()->get_product_attribute_tooltip_type( $term_id );
                                        $current = empty( $local ) ? '' : $local;
                                    ?>

                                    <div class="form-label">
                                        <label for="<?php echo esc_attr( $id ) ?>">
                                            <?php esc_html_e( 'Show Tooltip', 'woo-variation-swatches-pro' ) ?>
                                        </label>
                                    </div>

                                    <div class="form-field">

                                        <select id="<?php echo esc_attr( $id ) ?>" style="width: 100px" class="wc-enhanced-select" name="<?php echo esc_attr( $name ) ?>">
                                            <option <?php selected( $current, '' ) ?> value=""><?php esc_html_e( 'Global', 'woo-variation-swatches-pro' ) ?></option>
                                            <option <?php selected( $current, 'text' ) ?> value="text"><?php esc_html_e( 'Text', 'woo-variation-swatches-pro' ) ?></option>
                                            <option <?php selected( $current, 'image' ) ?> value="image"><?php esc_html_e( 'Image', 'woo-variation-swatches-pro' ) ?></option>
                                            <option <?php selected( $current, 'no' ) ?> value="no"><?php esc_html_e( 'No', 'woo-variation-swatches-pro' ) ?></option>
                                        </select>

                                    </div>
                                </div>
                                
                                <?php
                                $required = woo_variation_swatches()->get_backend()->get_edit_panel()->normalize_required_attribute( array( $show_tooltip_type_id => array( 'type' => 'equal', 'value' => 'text' ), $required_id => array( 'type' => 'equal', 'value' => array( 'color', 'image', 'button', 'radio', 'mixed' ) ) ) );
                                ?>
                                <div class="form-settings-group" data-gwp_dependency="<?php echo wc_esc_json( wp_json_encode( $required ) ) ?>">
                                    
                                    <?php
                                        $id                   = woo_variation_swatches()->get_backend()->get_edit_panel()->settings_id( $attribute_key, 'terms', $term_id, 'tooltip_text' );
                                        $name                 = woo_variation_swatches()->get_backend()->get_edit_panel()->settings_name( $attribute_key, 'terms', $term_id, 'tooltip_text' );
                                        $local                = woo_variation_swatches()->get_backend()->get_edit_panel()->settings_value( $settings, $attribute_key, 'terms', $term_id, 'tooltip_text' );
                                        $default              = woo_variation_swatches()->get_frontend()->get_product_attribute_tooltip_text( $term_id );
                                        $current              = is_null( $local ) ? $default : $local;
                                        $default_tooltip_text = empty( $current ) ? $term[ 'name' ] : $current;
                                    ?>

                                    <div class="form-label">
                                        <label for="<?php echo esc_attr( $id ) ?>">
                                            <?php esc_html_e( 'Tooltip Text', 'woo-variation-swatches-pro' ) ?>
                                        </label>
                                    </div>

                                    <div class="form-field">
                                        <input style="width: 300px" placeholder="<?php printf( esc_html__( 'Shows "%s" as default tooltip text', 'woo-variation-swatches-pro' ), $term[ 'name' ] ) ?>" value="<?php echo sanitize_text_field( $default_tooltip_text ) ?>" id="<?php echo esc_attr( $id ) ?>" type="text" name="<?php echo esc_attr( $name ) ?>">
                                    </div>
                                </div>
                                
                                <?php
                                $required = woo_variation_swatches()->get_backend()->get_edit_panel()->normalize_required_attribute( array( $show_tooltip_type_id => array( 'type' => 'equal', 'value' => 'image' ), $required_id => array( 'type' => 'equal', 'value' => array( 'color', 'image', 'button', 'radio', 'mixed' ) ) ) );
                                ?>

                                <div class="form-settings-group" data-gwp_dependency="<?php echo wc_esc_json( wp_json_encode( $required ) ) ?>">
                                    
                                    <?php
                                        $id      = woo_variation_swatches()->get_backend()->get_edit_panel()->settings_id( $attribute_key, 'terms', $term_id, 'tooltip_image_id' );
                                        $name    = woo_variation_swatches()->get_backend()->get_edit_panel()->settings_name( $attribute_key, 'terms', $term_id, 'tooltip_image_id' );
                                        $local   = woo_variation_swatches()->get_backend()->get_edit_panel()->settings_value( $settings, $attribute_key, 'terms', $term_id, 'tooltip_image_id' );
                                        $default = woo_variation_swatches()->get_frontend()->get_product_attribute_tooltip_image_id( $term_id );
                                        $current = empty( $local ) ? $default : $local;
                                    ?>

                                    <div class="form-label">
                                        <label for="<?php echo esc_attr( $id ) ?>">
                                            <?php esc_html_e( 'Tooltip Image', 'woo-variation-swatches-pro' ) ?>
                                        </label>
                                    </div>

                                    <div class="form-field">
                                        <?php woo_variation_swatches()->get_backend()->get_edit_panel()->generate_image_upload_field( $id, $name, $current ) ?>
                                    </div>
                                </div>
                            
                            <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
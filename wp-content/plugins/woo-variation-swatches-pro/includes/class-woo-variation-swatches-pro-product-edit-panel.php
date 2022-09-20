<?php
    defined( 'ABSPATH' ) || exit;
    
    if ( ! class_exists( 'Woo_Variation_Swatches_Pro_Product_Edit_Panel' ) ) :
        class Woo_Variation_Swatches_Pro_Product_Edit_Panel extends Woo_Variation_Swatches_Product_Edit_Panel {
            protected static $_instance = null;
            
            protected function __construct() {
                parent::__construct();
            }
            
            public static function instance() {
                if ( is_null( self::$_instance ) ) {
                    self::$_instance = new self();
                }
                
                return self::$_instance;
            }
            
            protected function hooks() {
                parent::hooks();
                
                add_action( 'wc_ajax_woo_variation_swatches_load_product_terms', array( $this, 'load_product_terms' ) );
                add_action( 'wc_ajax_woo_variation_swatches_save_product_options', array( $this, 'save_product_options' ) );
                add_action( 'wc_ajax_woo_variation_swatches_reset_product_options', array( $this, 'reset_product_options' ) );
                // add_action( 'wc_ajax_woo_variation_swatches_load_product_options', array( $this, 'load_product_options' ) );
                // woo_variation_swatches_load_product_terms
            }
            
            public function get_all_image_sizes() {
                
                $image_subsizes = wp_get_registered_image_subsizes();
                
                return apply_filters( 'woo_variation_swatches_get_all_image_sizes', array_reduce( array_keys( $image_subsizes ), function ( $carry, $item ) use ( $image_subsizes ) {
                    
                    $title  = ucwords( str_ireplace( array( '-', '_' ), ' ', $item ) );
                    $width  = $image_subsizes[ $item ][ 'width' ];
                    $height = $image_subsizes[ $item ][ 'height' ];
                    
                    $carry[ $item ] = sprintf( '%s (%d &times; %d)', $title, $width, $height );
                    
                    return $carry;
                },                                                                                array() ) );
            }
            
            public function product_data_tab( $tabs ) {
                
                $tabs[ 'woo_variation_swatches' ] = array(
                    'label'    => esc_html__( 'Swatches Settings', 'woo-variation-swatches-pro' ),
                    'target'   => 'woo_variation_swatches_variation_product_options',
                    'class'    => array( 'show_if_variable', 'variations_tab', 'pro-active' ),
                    'priority' => 65,
                );
                
                return $tabs;
            }
            
            public function product_settings( $product_id ) {
                
                $product_id     = sanitize_text_field( $product_id );
                $product_object = wc_get_product( $product_id );
                
                if ( ! $product_object->is_type( 'variable' ) ) {
                    include dirname( __FILE__ ) . '/html-product-settings-panel-message.php';
                    
                    return;
                }
                
                $attributes = $this->get_attributes_data( $product_object );
                $settings   = $this->get_product_options( $product_id );
                $limit      = $this->get_limit( $product_id );
                include dirname( __FILE__ ) . '/html-product-settings-panel.php';
            }
            
            public function product_data_panel() {
                global $post, $wpdb, $product_object;
                
                $product_id = $product_object->get_id();
                $this->product_settings( $product_id );
            }
            
            public function load_product_options() {
                if ( isset( $_POST[ 'nonce' ] ) && wp_verify_nonce( wp_unslash( $_POST[ 'nonce' ] ), 'woo_variation_swatches_admin' ) ) {
                    $product_id = sanitize_text_field( $_POST[ 'product_id' ] );
                    $this->product_settings( $product_id );
                }
            }
            
            public function get_limit( $product_id ) {
                // @TODO: Add Option to control limit
                return absint( apply_filters( 'woo_variation_swatches_product_edit_terms_display_limit', 10, $product_id ) );
            }
            
            public function get_sliced_terms( $terms, $offset, $limit ) {
                return array_slice( $terms, $offset, $limit, true );
            }
            
            public function get_product_options( $product_id ) {
                
                $old_options = get_post_meta( $product_id, '_wvs_product_attributes', true );
                $new_options = get_post_meta( $product_id, '_woo_variation_swatches_product_settings', true );
                
                // Backward Compatibility
                $options = empty( $new_options ) ? $old_options : $new_options;
                
                if ( empty( $options ) ) {
                    return array();
                }
                
                return $options;
            }
            
            public function array_merge_recursive( $new_data, $old_data = array() ) {
                $merged = $old_data;
                
                foreach ( $new_data as $key => $value ) {
                    
                    if ( is_array( $value ) && isset( $merged[ $key ] ) && is_array( $merged[ $key ] ) ) {
                        $merged[ $key ] = $this->array_merge_recursive( $value, $merged[ $key ] );
                    } else {
                        $merged[ $key ] = $value;
                    }
                }
                
                return $merged;
            }
            
            public function save( $product_id, $data ) {
                $new_data = map_deep( $data, 'sanitize_text_field' );
                $old_data = $this->get_product_options( $product_id );
                $data     = $this->array_merge_recursive( $new_data, $old_data );
                
                return update_post_meta( $product_id, '_woo_variation_swatches_product_settings', $data );
            }
            
            public function save_product_options() {
                if ( isset( $_POST[ 'nonce' ] ) && wp_verify_nonce( wp_unslash( $_POST[ 'nonce' ] ), 'woo_variation_swatches_admin' ) ) {
                    $product_id = sanitize_text_field( $_POST[ 'product_id' ] );
                    
                    $data = map_deep( $_POST[ 'data' ], 'sanitize_text_field' );
                    $this->save( $product_id, $data );
                }
            }
            
            public function delete( $product_id ) {
                delete_post_meta( $product_id, '_wvs_product_attributes' );
                
                return delete_post_meta( $product_id, '_woo_variation_swatches_product_settings' );
            }
            
            public function reset_product_options() {
                if ( isset( $_POST[ 'nonce' ] ) && wp_verify_nonce( wp_unslash( $_POST[ 'nonce' ] ), 'woo_variation_swatches_admin' ) ) {
                    $product_id = sanitize_text_field( $_POST[ 'product_id' ] );
                    $this->delete( $product_id );
                    $this->load_product_options();
                }
            }
            
            public function load_product_terms() {
                
                if ( isset( $_POST[ 'nonce' ] ) && wp_verify_nonce( wp_unslash( $_POST[ 'nonce' ] ), 'woo_variation_swatches_admin' ) ) {
                    $product_id     = sanitize_text_field( $_POST[ 'product_id' ] );
                    $offset         = absint( $_POST[ 'offset' ] );
                    $attribute_id   = sanitize_text_field( $_POST[ 'attribute_id' ] );
                    $attribute_name = sanitize_text_field( $_POST[ 'attribute_name' ] );
                    $limit          = absint( $this->get_limit( $product_id ) );
                    $product_object = wc_get_product( $product_id );
                    $settings       = $this->get_product_options( $product_id );
                    
                    $attribute_data = $this->get_attribute_data( $product_object, $attribute_id, $attribute_name );
                    
                    $attribute = $attribute_data[ $attribute_name ];
                    
                    $terms = $this->get_sliced_terms( $attribute[ 'terms' ], $offset, $limit );
                    
                    include dirname( __FILE__ ) . '/html-product-attribute-term-settings-panel.php';
                    
                }
            }
            
            public function sanitize_name( $value ) {
                return wc_clean( rawurldecode( sanitize_title( wp_unslash( $value ) ) ) );
            }
            
            public function settings_name( ...$params ) {
                
                $attribute_key    = 'woo_variation_swatches_product_options';
                $sanitized_params = map_deep( $params, array( $this, 'sanitize_name' ) );
                $parts            = implode( '][', $sanitized_params );
                
                return sprintf( '%s[%s]', $attribute_key, $parts );
            }
            
            public function settings_id( ...$params ) {
                $attribute_key = 'woo_variation_swatches_product_options';
                $parts         = implode( '_', $params );
                
                // $parts = sanitize_key( sanitize_title( $parts ) );
                
                $parts = $this->sanitize_name( $parts );
                
                return sprintf( '%s__%s', $attribute_key, $parts );
            }
            
            public function settings_value( $settings, ...$params ) {
                
                foreach ( $params as $index => $param ) {
                    
                    $key = $this->sanitize_name( $param );
                    
                    if ( isset( $settings[ $key ] ) ) {
                        
                        unset( $params[ $index ] );
                        
                        if ( count( $params ) > 0 ) {
                            return $this->settings_value( $settings[ $key ], ...$params );
                        } else {
                            return $settings[ $key ];
                        }
                    } else {
                        return null;
                    }
                }
            }
            
            public function normalize_required_attribute( $require = array() ) {
                
                $r = array();
                //   array( '#woo_variation_swatches_show_variation_label' => array( 'type' => '==', 'value' => '1' ) ),
                foreach ( $require as $id => $value ) {
                    $r[ sprintf( '#%s', $id ) ] = $value;
                }
                
                return array( $r );
                //return  $r;
            }
            
            public function is_attribute_option_used_in_variation( $term_id, $product_attribute ) {
                return in_array( $term_id, $product_attribute );
            }
            
            public function generate_image_upload_field( $id, $name, $value ) {
                
                $placeholder = $this->placeholder_img_src();
                $image       = $this->get_img_src( $value );
                ob_start();
                ?>

                <div class="meta-image-field-wrapper">
                    <div class="image-preview">
                        <img alt="" data-placeholder="<?php echo esc_url( $placeholder ) ?>" src="<?php echo esc_url( $image ) ?>" width="60px" height="60px">
                    </div>
                    <div class="button-wrapper">
                        <input type="hidden" id="<?php echo esc_attr( $id ) ?>" name="<?php echo esc_attr( $name ) ?>" value="<?php echo esc_attr( $value ) ?>">
                        <button type="button" class="wvs_upload_image_button button button-primary button-small"><?php esc_html_e( 'Upload / Add image', 'woo-variation-swatches-pro' ); ?></button>
                        <button type="button" style="<?php echo empty( $value ) ? 'display: none;' : '' ?>" class="wvs_remove_image_button button button-danger button-small"><?php esc_html_e( 'Remove image', 'woo-variation-swatches-pro' ); ?></button>
                    </div>
                </div>
                <?php
                echo ob_get_clean();
            }
            
            public function placeholder_img_src() {
                return woo_variation_swatches()->images_url( '/placeholder.png' );
            }
            
            public function get_img_src( $thumbnail_id = false ) {
                if ( ! empty( $thumbnail_id ) ) {
                    $image = wp_get_attachment_thumb_url( $thumbnail_id );
                } else {
                    $image = $this->placeholder_img_src();
                }
                
                return $image;
            }
            
            public function get_attribute_data( $product_object, $attribute_id, $attribute_name ) {
                $attribute = array_filter( $product_object->get_attributes(), function ( $attribute ) use ( $attribute_id, $attribute_name ) {
                    return true === $attribute->get_variation() && ( $attribute->get_name() == $attribute_name ) && ( $attribute->get_id() == $attribute_id );
                } );
                
                return $this->prepare_attribute_data( $attribute, $product_object );
            }
            
            public function get_attributes_data( $product_object ) {
                
                $attributes = array_filter( $product_object->get_attributes(), array( $this, 'filter_variation_attributes' ) );
                
                return $this->prepare_attribute_data( $attributes, $product_object );
            }
            
            public function prepare_attribute_data( $attributes, $product_object ) {
                $attribute_data = array();
                
                $product_variation_attributes = $product_object->get_variation_attributes();
                
                foreach ( $attributes as $attribute ) {
                    
                    $attribute_name = $attribute->get_name();
                    $attribute_id   = $attribute->get_id();
                    $attribute_key  = sanitize_title( $attribute->get_name() );
                    $options        = (array) $attribute->get_options();
                    
                    
                    if ( $attribute->is_taxonomy() && $attribute_taxonomy = $attribute->get_taxonomy_object() ) {
                        
                        $terms = array();
                        
                        $args = array(
                            'orderby'    => ! empty( $attribute_taxonomy->attribute_orderby ) ? $attribute_taxonomy->attribute_orderby : 'name',
                            'hide_empty' => 0,
                        );
                        
                        $attribute_data[ $attribute_name ][ 'is_taxonomy' ]    = true;
                        $attribute_data[ $attribute_name ][ 'attribute_id' ]   = $attribute_id;
                        $attribute_data[ $attribute_name ][ 'attribute_name' ] = $attribute_name;
                        $attribute_data[ $attribute_name ][ 'attribute_key' ]  = $attribute_key;
                        $attribute_data[ $attribute_name ][ 'attribute_type' ] = sanitize_text_field( $attribute_taxonomy->attribute_type );
                        $attribute_data[ $attribute_name ][ 'taxonomy' ]       = (array) $attribute_taxonomy;
                        $attribute_data[ $attribute_name ][ 'terms' ]          = array();
                        
                        $all_terms = get_terms( $attribute->get_taxonomy(), apply_filters( 'woocommerce_product_attribute_terms', $args ) );
                        if ( $all_terms ) {
                            foreach ( $all_terms as $term ) {
                                if ( in_array( $term->term_id, $options, true ) ) {
                                    // @TODO: Check multilingual support with term id or we have to change to term slug
                                    $terms[ esc_attr( $term->term_id ) ] = array(
                                        'term_id'   => $term->term_id,
                                        'variation' => $term->slug,
                                        'slug'      => sanitize_title( $term->slug ),
                                        'name'      => apply_filters( 'woocommerce_variation_option_name', $term->name, $term, $attribute_name, $product_object )
                                    );
                                }
                            }
                        }
                        $attribute_data[ $attribute_name ][ 'terms' ]           = $terms;
                        $attribute_data[ $attribute_name ][ 'available_terms' ] = isset( $product_variation_attributes[ $attribute_name ] ) ? $product_variation_attributes[ $attribute_name ] : array();
                        
                    } else {
                        
                        $terms = array();
                        
                        /**
                         * [attribute_id] => 0
                         * [attribute_name] => buttons
                         * [attribute_label] => Buttons
                         * [attribute_type] => select
                         * [attribute_orderby] => menu_order
                         * [attribute_public] => 1
                         */
                        
                        $attribute_data[ $attribute_name ][ 'is_taxonomy' ]    = false;
                        $attribute_data[ $attribute_name ][ 'attribute_id' ]   = $attribute_id;
                        $attribute_data[ $attribute_name ][ 'attribute_name' ] = $attribute_name;
                        $attribute_data[ $attribute_name ][ 'attribute_key' ]  = $attribute_key;
                        $attribute_data[ $attribute_name ][ 'attribute_type' ] = 'select';
                        $attribute_data[ $attribute_name ][ 'taxonomy' ]       = array(
                            'attribute_id'    => $attribute_key,
                            'attribute_name'  => $attribute_name,
                            'attribute_label' => $attribute->get_name(),
                            'attribute_type'  => 'select',
                        );
                        $attribute_data[ $attribute_name ][ 'terms' ]          = array();
                        
                        foreach ( $options as $option ) {
                            $terms[ esc_attr( $option ) ] = array(
                                'term_id'   => 0,
                                'variation' => $option,
                                'slug'      => sanitize_title( $option ),
                                'name'      => apply_filters( 'woocommerce_variation_option_name', $option, null, $attribute_name, $product_object ),
                            );
                        }
                        
                        $attribute_data[ $attribute_name ][ 'terms' ]           = $terms;
                        $attribute_data[ $attribute_name ][ 'available_terms' ] = isset( $product_variation_attributes[ $attribute_name ] ) ? $product_variation_attributes[ $attribute_name ] : array();
                        
                    }
                }
                
                return $attribute_data;
            }
            
            private function filter_variation_attributes( $attribute ) {
                return true === $attribute->get_variation();
            }
        }
    endif;
<?php
    
    defined( 'ABSPATH' ) || exit;
    
    
    if ( ! class_exists( 'Woo_Variation_Swatches_Pro' ) ) {
        
        class Woo_Variation_Swatches_Pro extends Woo_Variation_Swatches {
            
            protected static $_instance = null;
            
            public function __construct() {
                parent::__construct();
            }
            
            public static function instance() {
                if ( is_null( self::$_instance ) ) {
                    self::$_instance = new self();
                }
                
                return self::$_instance;
            }
            
            public function includes() {
                parent::includes();
                require_once dirname( __FILE__ ) . '/class-woo-variation-swatches-pro-frontend.php';
                require_once dirname( __FILE__ ) . '/class-woo-variation-swatches-pro-backend.php';
            }
            
            public function pro_version() {
                return esc_attr( WOO_VARIATION_SWATCHES_PRO_PLUGIN_VERSION );
            }
            
            public function get_product_options( $product_id ) {
                
                if ( is_object( $product_id ) ) {
                    $product_id = $product_id->get_id();
                }
                
                $old_options = get_post_meta( $product_id, '_wvs_product_attributes', true );
                $new_options = get_post_meta( $product_id, '_woo_variation_swatches_product_settings', true );
                
                // Backward Compatibility
                $options = empty( $new_options ) ? $old_options : $new_options;
                
                if ( empty( $options ) ) {
                    return array();
                }
                
                return $options;
            }
            
            private function product_settings_lookup( $settings, ...$params ) {
                
                foreach ( $params as $index => $param ) {
                    
                    $key = $this->sanitize_name( $param );
                    
                    if ( isset( $settings[ $key ] ) ) {
                        
                        unset( $params[ $index ] );
                        
                        if ( count( $params ) > 0 ) {
                            return $this->product_settings_lookup( $settings[ $key ], ...$params );
                        } else {
                            return $settings[ $key ];
                        }
                    } else {
                        return null;
                    }
                }
            }
            
            public function get_product_settings( $product_id, ...$params ) {
                
                $settings = $this->get_product_options( $product_id );
                
                return $this->product_settings_lookup( $settings, ...$params );
            }
            
            public function get_backend() {
                return Woo_Variation_Swatches_Pro_Backend::instance();
            }
            
            public function get_frontend() {
                return Woo_Variation_Swatches_Pro_Frontend::instance();
            }
            
            public function show_archive_page_swatches() {
                global $product;
                
                if ( is_object( $product ) ) {
                    woo_variation_swatches()->get_frontend()->get_archive_page()->display_swatches( $product );
                }
            }
            
            public function show_archive_page_swatches_by_id( $product_id ) {
                if ( is_object( $product_id ) ) {
                    $product_id = $product_id->get_id();
                }
                
                $product = wc_get_product( $product_id );
                if ( is_object( $product ) ) {
                    woo_variation_swatches()->get_frontend()->get_archive_page()->display_swatches( $product );
                }
            }
            
            public function show_archive_variation_shortcode( $raw_attributes = array() ) {
                global $product;
                
                // [wvs_show_archive_variation product_id="ID"]
                
                $attributes = shortcode_atts( array(
                                                  'product_id' => 0
                                              ), $raw_attributes );
                
                $current_product = absint( $attributes[ 'product_id' ] ) > 0 ? wc_get_product( $attributes[ 'product_id' ] ) : $product;
                
                if ( is_object( $current_product ) ) {
                    woo_variation_swatches()->get_frontend()->get_archive_page()->display_swatches( $product );
                }
            }
            
            public function pro_plugin_url() {
                return untrailingslashit( plugin_dir_url( WOO_VARIATION_SWATCHES_PRO_PLUGIN_FILE ) );
            }
            
            public function pro_plugin_path() {
                return untrailingslashit( plugin_dir_path( WOO_VARIATION_SWATCHES_PRO_PLUGIN_FILE ) );
            }
            
            public function pro_images_url( $file = '' ) {
                return untrailingslashit( plugin_dir_url( WOO_VARIATION_SWATCHES_PRO_PLUGIN_FILE ) . 'images' ) . $file;
            }
            
            public function pro_assets_url( $file = '' ) {
                return untrailingslashit( plugin_dir_url( WOO_VARIATION_SWATCHES_PRO_PLUGIN_FILE ) . 'assets' ) . $file;
            }
            
            public function pro_assets_path( $file = '' ) {
                return $this->pro_plugin_path() . '/assets' . $file;
            }
            
            public function pro_assets_version( $file ) {
                return filemtime( $this->pro_assets_path( $file ) );
            }
            
            public function pro_include_path( $file = '' ) {
                return untrailingslashit( plugin_dir_path( WOO_VARIATION_SWATCHES_PRO_PLUGIN_FILE ) . 'includes' ) . $file;
            }
            
            public function template_path() {
                return apply_filters( 'woo_variation_swatches_template_path', untrailingslashit( $this->pro_plugin_path() ) . '/templates' );
            }
            
            public function template_url() {
                return apply_filters( 'woo_variation_swatches_template_url', untrailingslashit( $this->pro_plugin_url() ) . '/templates' );
            }
            
            public function is_pro() {
                return true;
            }
            
            public function language() {
                parent::language();
                load_plugin_textdomain( 'woo-variation-swatches-pro', false, plugin_basename( dirname( WOO_VARIATION_SWATCHES_PRO_PLUGIN_FILE ) ) . '/languages' );
            }
        }
    }

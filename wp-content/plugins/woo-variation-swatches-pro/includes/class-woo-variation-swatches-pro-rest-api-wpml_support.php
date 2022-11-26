<?php
    defined( 'ABSPATH' ) || exit;
    
    if ( ! class_exists( 'Woo_Variation_Swatches_Pro_REST_API_WPML_Support' ) ) {
        class Woo_Variation_Swatches_Pro_REST_API_WPML_Support {
            
            protected static $_instance = null;
            
            protected function __construct() {
                $this->includes();
                $this->hooks();
                $this->init();
                
                do_action( 'woo_variation_swatches_pro_rest_api_wpml_support_loaded', $this );
            }
            
            public static function instance() {
                if ( is_null( self::$_instance ) ) {
                    self::$_instance = new self();
                }
                
                return self::$_instance;
            }
            
            protected function includes() {
            }
            
            protected function hooks() {
                add_filter( 'woo_variation_swatches_rest_add_extra_params', array( $this, 'add_language_args' ) );
                // add_action( 'woo_variation_swatches_rest_process_extra_params', array( $this, 'switch_language' ) );
            }
            
            protected function init() {
            
            }
            
            public function add_language_args( $args ) {
                
                $current_language = apply_filters( 'wpml_current_language', null );
                $default_language = apply_filters( 'wpml_default_language', null );
                
                if ( $current_language === $default_language ) {
                    return $args;
                }
                
                if ( $current_language ) {
                    $args[ 'lang' ] = $current_language;
                }
                
                return $args;
            }
            
            public function is_switch_language_triggered() {
                return isset( $GLOBALS[ 'icl_language_switched' ] ) ? true : false;
            }
            
            public function switch_language() {
                
                $current_language    = ! empty( $_GET[ 'lang' ] ) ? sanitize_key( $_GET[ 'lang' ] ) : null;
                $available_languages = apply_filters( 'wpml_active_languages', null, array( 'skip_missing' => 0 ) );
                
                if ( ! empty( $available_languages ) && ! $this->is_switch_language_triggered() ) {
                    
                    if ( in_array( $current_language, array_keys( $available_languages ) ) ) {
                        do_action( 'wpml_switch_language', $current_language );
                    }
                }
            }
        }
    }
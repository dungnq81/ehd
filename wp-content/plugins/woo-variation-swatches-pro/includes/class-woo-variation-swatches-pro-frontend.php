<?php
    defined( 'ABSPATH' ) || exit;
    
    if ( ! class_exists( 'Woo_Variation_Swatches_Pro_Frontend' ) ) {
        class Woo_Variation_Swatches_Pro_Frontend extends Woo_Variation_Swatches_Frontend {
            
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
            
            protected function includes() {
                
                parent::includes();
                require_once dirname( __FILE__ ) . '/class-woo-variation-swatches-pro-product-page.php';
                require_once dirname( __FILE__ ) . '/class-woo-variation-swatches-pro-archive-page.php';
                require_once dirname( __FILE__ ) . '/class-woo-variation-swatches-pro-rest-api.php';
                require_once dirname( __FILE__ ) . '/class-woo-variation-swatches-pro-widget-layered-nav.php';
            }
            
            protected function init() {
                $this->get_archive_page();
                $this->get_compatibility();
                $this->get_rest_api();
            }
            
            // Start
            
            public function get_compatibility() {
                return Woo_Variation_Swatches_Compatibility::instance();
            }
            
            public function get_archive_page() {
                return Woo_Variation_Swatches_Pro_Archive_Page::instance();
            }
    
            public function get_rest_api() {
                return Woo_Variation_Swatches_Pro_REST_API::instance();
            }
            
            public function get_product_attribute_is_dual_color( $term, $data = array() ) {
                $term_id = 0;
                if ( is_numeric( $term ) ) {
                    $term_id = $term;
                }
                
                if ( is_object( $term ) ) {
                    $term_id = $term->term_id;
                }
                
                return wc_string_to_bool( get_term_meta( $term_id, 'is_dual_color', true ) );
            }
            
            public function get_product_attribute_group_slug( $term, $data = array() ) {
                
                $term_id = 0;
                if ( is_numeric( $term ) ) {
                    $term_id = $term;
                }
                
                if ( is_object( $term ) ) {
                    $term_id = $term->term_id;
                }
                
                $slug = sanitize_text_field( get_term_meta( $term_id, 'group_name', true ) );
                
                return empty( $slug ) ? false : $slug;
            }
            
            public function get_product_attribute_group_name( $term, $data = array() ) {
                
                $slug = $this->get_product_attribute_group_slug( $term );
                if ( $slug ) {
                    return sanitize_text_field( woo_variation_swatches()->get_backend()->get_group()->get( $slug ) );
                }
                
                return false;
            }
            
            public function get_product_attribute_primary_color( $term, $data = array() ) {
                
                return $this->get_product_attribute_color( $term, $data );
            }
            
            public function get_product_attribute_secondary_color( $term, $data = array() ) {
                
                $term_id = 0;
                if ( is_numeric( $term ) ) {
                    $term_id = $term;
                }
                
                if ( is_object( $term ) ) {
                    $term_id = $term->term_id;
                }
                
                return get_term_meta( $term_id, 'secondary_color', true );
            }
            
            public function get_product_attribute_image( $term, $data = array() ) {
                
                $term_id = 0;
                if ( is_numeric( $term ) ) {
                    $term_id = $term;
                }
                
                if ( is_object( $term ) ) {
                    $term_id = $term->term_id;
                }
                
                return get_term_meta( $term_id, 'product_attribute_image', true );
            }
            
            public function get_product_attribute_show_tooltip( $term, $data = array() ) {
                
                $term_id = 0;
                if ( is_numeric( $term ) ) {
                    $term_id = $term;
                }
                
                if ( is_object( $term ) ) {
                    $term_id = $term->term_id;
                }
                
                return ! ( 'no' === get_term_meta( $term_id, 'show_tooltip', true ) );
            }
            
            public function get_product_attribute_tooltip_type( $term, $data = array() ) {
                
                $term_id = 0;
                if ( is_numeric( $term ) ) {
                    $term_id = $term;
                }
                
                if ( is_object( $term ) ) {
                    $term_id = $term->term_id;
                }
                
                return get_term_meta( $term_id, 'show_tooltip', true );
            }
            
            public function get_product_attribute_tooltip_image_id( $term, $data = array() ) {
                
                $term_id = 0;
                if ( is_numeric( $term ) ) {
                    $term_id = $term;
                }
                
                if ( is_object( $term ) ) {
                    $term_id = $term->term_id;
                }
                
                return get_term_meta( $term_id, 'tooltip_image_id', true );
            }
            
            public function get_product_attribute_tooltip_text( $term, $data = array() ) {
                
                $term_id = 0;
                if ( is_numeric( $term ) ) {
                    $term_id = $term;
                }
                
                if ( is_object( $term ) ) {
                    $term_id = $term->term_id;
                }
                
                return get_term_meta( $term_id, 'tooltip_text', true );
            }
        }
    }

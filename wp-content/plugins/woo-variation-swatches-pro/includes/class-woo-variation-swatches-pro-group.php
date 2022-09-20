<?php
    
    defined( 'ABSPATH' ) || exit;
    
    if ( ! class_exists( 'Woo_Variation_Swatches_Pro_Group' ) ) {
        
        class Woo_Variation_Swatches_Pro_Group {
            protected static $_instance = null;
            
            protected function __construct() {
            
            }
            
            public static function instance() {
                if ( is_null( self::$_instance ) ) {
                    self::$_instance = new self();
                }
                
                return self::$_instance;
            }
            
            
            // start
            
            public function get_id() {
                return 'woo_variation_swatches_groups';
            }
            
            public function delete_all() {
                return delete_option( $this->get_id() );
            }
            
            public function get_all() {
                return (array) get_option( $this->get_id(), array() );
            }
            
            public function save_all( $data ) {
                return update_option( $this->get_id(), $data );
            }
            
            public function get( $slug ) {
                $available_data = (array) get_option( $this->get_id(), array() );
                if ( array_key_exists( $slug, $available_data ) ) {
                    return $available_data[ $slug ];
                } else {
                    return false;
                }
            }
            
            public function save( $slug, $new_data ) {
                $available_data = (array) get_option( $this->get_id(), array() );
                
                if ( array_key_exists( $slug, $available_data ) ) {
                    return false;
                }
                
                $data = array_merge( $available_data, $new_data );
                
                return update_option( $this->get_id(), $data );
                
            }
            
            public function update( $slug, $name ) {
                $data = (array) get_option( $this->get_id(), array() );
                
                if ( array_key_exists( $slug, $data ) ) {
                    $data[ $slug ] = $name;
                    
                    return update_option( $this->get_id(), $data );
                } else {
                    return false;
                }
            }
            
            public function delete( $slug ) {
                
                $available_data = (array) get_option( $this->get_id(), array() );
                
                if ( array_key_exists( $slug, $available_data ) ) {
                    
                    unset( $available_data[ $slug ] );
                    
                    return update_option( $this->get_id(), $available_data );
                } else {
                    return false;
                }
            }
        }
    }
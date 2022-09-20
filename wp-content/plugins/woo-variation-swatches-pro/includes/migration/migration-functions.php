<?php
    defined( 'ABSPATH' ) || exit;
    
    function woo_variation_swatches_pro_migrate_200_product_attributes() {
        
        global $wpdb;
        
        $prepare = $wpdb->prepare( "SELECT term_id FROM $wpdb->termmeta WHERE meta_key = %s AND meta_value != '0'", 'tooltip_image' ); // phpcs:ignore
        
        $term_ids = $wpdb->get_col( $prepare );
        
        foreach ( $term_ids as $term_id ) {
            $meta_value = get_term_meta( $term_id, 'tooltip_image', true );
            add_term_meta( $term_id, 'tooltip_image_id', $meta_value );
            // delete_term_meta( $term_id, 'tooltip_image' );
        }
        
        return true;
    }
    
    function woo_variation_swatches_pro_migrate_200_old_to_new( $contents = array() ) {
        
        $key_maps = array(
            'catalog_attribute' => 'catalog_mode_attribute',
            'tooltip_type'      => 'show_tooltip',
            'color'             => 'primary_color',
            'tooltip_image'     => 'tooltip_image_id',
        );
        
        $data = array();
        
        foreach ( $contents as $old_key => $content ) {
            
            if ( in_array( $old_key, array_keys( $key_maps ) ) && ! is_array( $content ) ) {
                $new_key = $key_maps[ $old_key ];
            } else {
                $new_key = $old_key;
            }
            
            if ( $new_key === 'type' && $content === 'custom' ) {
                $content = 'mixed';
            }
            
            $new_key = woo_variation_swatches()->sanitize_name( $new_key );
            
            $data[ $new_key ] = is_array( $content ) ? woo_variation_swatches_pro_migrate_200_old_to_new( $content ) : $content;
            
        }
        
        return $data;
        
    }
    
    function woo_variation_swatches_pro_migrate_200_variable_products_swatches_settings() {
        
        global $wpdb;
        
        $prepare = $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = %s AND meta_value != ''", '_wvs_product_attributes' ); // phpcs:ignore
        
        $post_ids = $wpdb->get_col( $prepare );
        
        foreach ( $post_ids as $post_id ) {
            
            $old_data = (array) get_post_meta( $post_id, '_wvs_product_attributes', true );
            
            $new_data = woo_variation_swatches_pro_migrate_200_old_to_new( $old_data );
            
            update_post_meta( $post_id, '_woo_variation_swatches_product_settings', $new_data );
            // delete_post_meta( $post_id, '_wvs_product_attributes' );
            
        }
        
        return true;
    }
    
    function woo_variation_swatches_pro_migrate_200_global_settings() {
        
        $key_maps = array(
            'archive_add_to_cart_button_selector' => 'archive_cart_button_selector',
            'trigger_catalog_mode'                => 'catalog_mode_trigger',
            'stylesheet'                          => 'enable_stylesheet',
            'tooltip'                             => 'enable_tooltip',
            'style'                               => 'shape_style',
        );
        
        $maps_yes_no = array(
            'show_on_archive',
            'enable_tooltip',
            'enable_stylesheet',
            'show_variation_label',
            'enable_linkable_variation_url',
            'show_variation_stock_info',
            'show_clear_on_archive',
            'show_swatches_on_filter_widget',
            'enable_catalog_mode',
            'linkable_attribute',
            'enable_single_variation_preview',
            'enable_single_variation_preview_archive',
            'enable_large_size',
            'default_to_button',
            'default_to_image',
            'clear_on_reselect',
            'hide_out_of_stock_variation',
            'clickable_out_of_stock_variation',
        );
        
        $rgba_to_hex = array(
            'tooltip_background_color',
            'border_color',
        );
        
        
        $old_settings = (array) woo_variation_swatches()->get_options();
        $license_key  = woo_variation_swatches()->get_option( 'license_key' );
        
        if ( $license_key ) {
            update_option( 'woo_variation_swatches_license', sanitize_text_field( $license_key ) );
        }
        
        $new_settings = array();
        foreach ( $old_settings as $old_key => $data ) {
            
            if ( in_array( $old_key, array_keys( $key_maps ) ) ) {
                $key = $key_maps[ $old_key ];
            } else {
                $key = $old_key;
            }
            
            if ( in_array( $key, $maps_yes_no ) ) {
                $data = wc_bool_to_string( $data );
            }
            
            if ( in_array( $key, $rgba_to_hex ) ) {
                $data = woo_variation_swatches()->from_rgb_to_hex( $data );
            }
            
            if ( 'archive_product_wrapper' === $key ) {
                $data = '.wvs-archive-product-wrapper';
            }
            
            if ( 'archive_image_selector' === $key ) {
                $data = '.wvs-archive-product-image';
            }
            
            if ( 'archive_cart_button_selector' === $key ) {
                $data = '.wvs-add-to-cart-button';
            }
            
            $new_settings[ $key ] = $data;
        }
        
        $updated_settings = array_merge( $old_settings, $new_settings );
        
        update_option( 'woo_variation_swatches', $updated_settings );
        
        return true;
    }
    
    function woo_variation_swatches_pro_migrate_202_group_slugs() {
        $groups = woo_variation_swatches()->get_backend()->get_group()->get_all();
        $data   = array();
        
        foreach ( $groups as $slug => $group ) {
            $new_slug          = woo_variation_swatches()->sanitize_name( $slug );
            $data[ $new_slug ] = $group;
        }
        
        woo_variation_swatches()->get_backend()->get_group()->save_all( $data );
        
        return true;
        
    }


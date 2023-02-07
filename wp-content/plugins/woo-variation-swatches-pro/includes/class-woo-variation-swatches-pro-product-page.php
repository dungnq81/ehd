<?php
    defined( 'ABSPATH' ) || exit;
    
    if ( ! class_exists( 'Woo_Variation_Swatches_Pro_Product_Page' ) ) {
        class Woo_Variation_Swatches_Pro_Product_Page extends Woo_Variation_Swatches_Product_Page {
            
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
            
            // Start
            
            public function inline_style_declaration() {
                
                // Single Page
                $width     = absint( woo_variation_swatches()->get_option( 'width', 30 ) );
                $height    = absint( woo_variation_swatches()->get_option( 'height', 30 ) );
                $font_size = absint( woo_variation_swatches()->get_option( 'single_font_size', 16 ) );
                
                // Archive Page
                $archive_align     = sanitize_text_field( woo_variation_swatches()->get_option( 'archive_align', 'flex-start' ) );
                $archive_width     = absint( woo_variation_swatches()->get_option( 'archive_width', 30 ) );
                $archive_height    = absint( woo_variation_swatches()->get_option( 'archive_height', 30 ) );
                $archive_font_size = absint( woo_variation_swatches()->get_option( 'archive_font_size', 16 ) );
                
                // Tooltip
                $tooltip_background_color = sanitize_hex_color( woo_variation_swatches()->get_option( 'tooltip_background_color', '#333333' ) );
                $tooltip_text_color       = sanitize_hex_color( woo_variation_swatches()->get_option( 'tooltip_text_color', '#FFFFFF' ) );
                
                // Item styling
                $border_color     = sanitize_text_field( woo_variation_swatches()->get_option( 'border_color', '#a8a8a8' ) );
                $border_size      = absint( woo_variation_swatches()->get_option( 'border_size', 1 ) );
                $background_color = sanitize_hex_color( woo_variation_swatches()->get_option( 'background_color', '#FFFFFF' ) );
                $text_color       = sanitize_hex_color( woo_variation_swatches()->get_option( 'text_color', '#000000' ) );
                
                // Item hover styling
                $hover_border_color     = sanitize_text_field( woo_variation_swatches()->get_option( 'hover_border_color', '#DDDDDD' ) );
                $hover_border_size      = absint( woo_variation_swatches()->get_option( 'hover_border_size', 3 ) );
                $hover_text_color       = sanitize_hex_color( woo_variation_swatches()->get_option( 'hover_text_color', '#000000' ) );
                $hover_background_color = sanitize_hex_color( woo_variation_swatches()->get_option( 'hover_background_color', '#FFFFFF' ) );
                
                // Item selected styling
                $selected_border_color     = sanitize_text_field( woo_variation_swatches()->get_option( 'selected_border_color', '#000000' ) );
                $selected_border_size      = absint( woo_variation_swatches()->get_option( 'selected_border_size', 2 ) );
                $selected_text_color       = sanitize_hex_color( woo_variation_swatches()->get_option( 'selected_text_color', '#000000' ) );
                $selected_background_color = sanitize_hex_color( woo_variation_swatches()->get_option( 'selected_background_color', '#FFFFFF' ) );
                
                // Large size
                $large_attribute_width     = absint( woo_variation_swatches()->get_option( 'large_size_width', 40 ) );
                $large_attribute_height    = absint( woo_variation_swatches()->get_option( 'large_size_height', 40 ) );
                $large_attribute_font_size = absint( woo_variation_swatches()->get_option( 'large_size_font_size', 16 ) );
                
                
                $declaration = array(
                    '--wvs-position' => $archive_align,
                    
                    '--wvs-single-product-large-item-width'     => sprintf( '%spx', $large_attribute_width ),
                    '--wvs-single-product-large-item-height'    => sprintf( '%spx', $large_attribute_height ),
                    '--wvs-single-product-large-item-font-size' => sprintf( '%spx', $large_attribute_font_size ),
                    
                    '--wvs-single-product-item-width'      => sprintf( '%spx', $width ),
                    '--wvs-single-product-item-height'     => sprintf( '%spx', $height ),
                    '--wvs-single-product-item-font-size'  => sprintf( '%spx', $font_size ),
                    //
                    '--wvs-archive-product-item-width'     => sprintf( '%spx', $archive_width ),
                    '--wvs-archive-product-item-height'    => sprintf( '%spx', $archive_height ),
                    '--wvs-archive-product-item-font-size' => sprintf( '%spx', $archive_font_size ),
                    //
                    '--wvs-tooltip-background-color'       => $tooltip_background_color,
                    '--wvs-tooltip-text-color'             => $tooltip_text_color,
                    
                    '--wvs-item-box-shadow'       => sprintf( '0 0 0 %dpx %s', $border_size, $border_color ),
                    '--wvs-item-background-color' => $background_color,
                    '--wvs-item-text-color'       => $text_color,
                    
                    
                    '--wvs-hover-item-box-shadow'       => sprintf( '0 0 0 %dpx %s', $hover_border_size, $hover_border_color ),
                    '--wvs-hover-item-background-color' => $hover_background_color,
                    '--wvs-hover-item-text-color'       => $hover_text_color,
                    
                    '--wvs-selected-item-box-shadow'       => sprintf( '0 0 0 %dpx %s', $selected_border_size, $selected_border_color ),
                    '--wvs-selected-item-background-color' => $selected_background_color,
                    '--wvs-selected-item-text-color'       => $selected_text_color,
                );
                
                return apply_filters( 'woo_variation_swatches_inline_style_declaration', $declaration );
            }
            
            public function get_attribute_type( $data, $variation_data = array() ) {
                
                
                $args = $data[ 'args' ];
                
                $product        = $args[ 'product' ];
                $attribute_name = $data[ 'attribute_name' ];
                $term_id        = $data[ 'term_id' ];
                $slug           = $data[ 'slug' ];
                
                
                // Product label Option
                
                $product_attribute_type = woo_variation_swatches()->get_product_settings( $product, $data[ 'attribute_key' ], 'terms', $data[ 'term_id' ], 'type' );
                
                $attribute_label_type = woo_variation_swatches()->get_product_settings( $product, $data[ 'attribute_key' ], 'type' );
                
                if ( 'select' === $attribute_label_type ) {
                    $product_attribute_type = false;
                }
                
                // Global Option
                
                if ( ! empty( $variation_data ) && isset( $variation_data[ $attribute_name ] ) && isset( $variation_data[ $attribute_name ][ $slug ] ) ) {
                    $attribute_type = $variation_data[ $attribute_name ][ $slug ][ 'type' ];
                } else {
                    $attribute_type = 'button';
                }
                
                return empty( $product_attribute_type ) ? $attribute_type : $product_attribute_type;
            }
            
            public function get_item_css_classes( $data, $attribute_type, $variation_data = array() ) {
                
                $css_classes = parent::get_item_css_classes( $data, $attribute_type, $variation_data );
                
                $is_term        = wc_string_to_bool( $data[ 'is_term' ] );
                $term_or_option = $data[ 'item' ];
                $product        = $data[ 'product' ];
                $attribute      = $data[ 'attribute_key' ];
                $term_id        = $data[ 'term_id' ];
                
                // local
                $local_tooltip_type     = sanitize_text_field( woo_variation_swatches()->get_product_settings( $product, woo_variation_swatches()->sanitize_name( $attribute ), 'terms', $term_id, 'show_tooltip' ) );
                $local_tooltip_image_id = absint( woo_variation_swatches()->get_product_settings( $product, woo_variation_swatches()->sanitize_name( $attribute ), 'terms', $term_id, 'tooltip_image_id' ) );
                
                
                // global
                $global_tooltip_type = sanitize_text_field( woo_variation_swatches()->get_frontend()->get_product_attribute_tooltip_type( $term_or_option, $data ) );
                // $global_tooltip_image_id = absint( woo_variation_swatches()->get_frontend()->get_product_attribute_tooltip_image_id( $term_or_option, $data ) );
                
                $tooltip_type = empty( $local_tooltip_type ) ? $global_tooltip_type : $local_tooltip_type;
                // $tooltip_image_id = empty( $local_tooltip_image_id ) ? $global_tooltip_image_id : $local_tooltip_image_id;
                
                $tooltip_image_id = $this->get_tooltip_image_id( $data, $attribute_type, $variation_data );
                
                if ( 'image' === $tooltip_type && $tooltip_image_id > 0 ) {
                    $css_classes[] = 'wvs-has-image-tooltip';
                }
                
                return $css_classes;
            }
            
            public function get_tooltip_image_id( $data, $attribute_type, $variation_data = array() ) {
                
                $html_attributes = array();
                $product         = $data[ 'product' ];
                $attribute       = $data[ 'attribute_key' ];
                $term_id         = $data[ 'term_id' ];
                $option_name     = $data[ 'option_name' ];
                $term            = $data[ 'item' ];
                $is_term         = wc_string_to_bool( $data[ 'is_term' ] );
                $enable_tooltip  = wc_string_to_bool( woo_variation_swatches()->get_option( 'enable_tooltip', 'yes' ) );
                
                if ( ! $enable_tooltip ) {
                    return 0;
                }
                
                if ( 'mixed' === $attribute_type ) {
                    $attribute_type = $this->get_attribute_type( $data, $variation_data );
                }
                
                $_attribute_image_id = 0;
                if ( 'image' === $attribute_type ) {
                    $_attribute_image_id = $this->get_image_attribute_id( $data, $attribute_type, $variation_data );
                }
                
                // local
                $local_tooltip_type     = woo_variation_swatches()->get_product_settings( $product, woo_variation_swatches()->sanitize_name( $attribute ), 'terms', $term_id, 'show_tooltip' );
                $local_tooltip_image_id = absint( woo_variation_swatches()->get_product_settings( $product, woo_variation_swatches()->sanitize_name( $attribute ), 'terms', $term_id, 'tooltip_image_id' ) );
                
                // global
                
                $global_tooltip_type     = sanitize_text_field( woo_variation_swatches()->get_frontend()->get_product_attribute_tooltip_type( $term, $data ) );
                $global_tooltip_image_id = absint( woo_variation_swatches()->get_frontend()->get_product_attribute_tooltip_image_id( $term, $data ) );
                $tooltip_type            = empty( $local_tooltip_type ) ? $global_tooltip_type : $local_tooltip_type;
                
                if ( 'image' !== $tooltip_type ) {
                    return 0;
                }
                
                $_tooltip_image_id = empty( $local_tooltip_image_id ) ? $global_tooltip_image_id : $local_tooltip_image_id;
                
                return empty( $_tooltip_image_id ) ? $_attribute_image_id : $_tooltip_image_id;
            }
            
            public function get_item_tooltip_attribute( $data, $attribute_type, $variation_data = array() ) {
                
                $html_attributes = array();
                $product         = $data[ 'product' ];
                $attribute       = $data[ 'attribute_key' ];
                $term_id         = $data[ 'term_id' ];
                $option_name     = $data[ 'option_name' ];
                $term            = $data[ 'item' ];
                $is_term         = wc_string_to_bool( $data[ 'is_term' ] );
                $enable_tooltip  = wc_string_to_bool( woo_variation_swatches()->get_option( 'enable_tooltip', 'yes' ) );
                
                if ( ! $enable_tooltip ) {
                    return $html_attributes;
                }
                
                // local
                $local_tooltip_type = woo_variation_swatches()->get_product_settings( $product, woo_variation_swatches()->sanitize_name( $attribute ), 'terms', $term_id, 'show_tooltip' );
                // $local_tooltip_image_id = absint( woo_variation_swatches()->get_product_settings( $product, woo_variation_swatches()->sanitize_name( $attribute ), 'terms', $term_id, 'tooltip_image_id' ) );
                
                // global
                
                $global_tooltip_type = sanitize_text_field( woo_variation_swatches()->get_frontend()->get_product_attribute_tooltip_type( $term, $data ) );
                // $global_tooltip_image_id = absint( woo_variation_swatches()->get_frontend()->get_product_attribute_tooltip_image_id( $term, $data ) );
                $tooltip_type = empty( $local_tooltip_type ) ? $global_tooltip_type : $local_tooltip_type;
                
                if ( 'no' === $tooltip_type ) {
                    return $html_attributes;
                }
                
                $tooltip_image_id = $this->get_tooltip_image_id( $data, $attribute_type, $variation_data );
                
                if ( 'image' === $tooltip_type && $tooltip_image_id > 0 ) {
                    
                    $tooltip_image_size = sanitize_text_field( woo_variation_swatches()->get_option( 'tooltip_image_size', 'variation_swatches_tooltip_size' ) );
                    
                    $tooltip_image_src = wp_get_attachment_image_src( $tooltip_image_id, $tooltip_image_size );
                    
                    $html_attributes[ 'style' ] = sprintf( '--tooltip-background: url(\'%s\'); --tooltip-width: %spx; --tooltip-height: %spx;', $tooltip_image_src[ 0 ], $tooltip_image_src[ 1 ], $tooltip_image_src[ 2 ] );
                }
                
                // local
                $local_tooltip_text = sanitize_text_field( woo_variation_swatches()->get_product_settings( $product, woo_variation_swatches()->sanitize_name( $attribute ), 'terms', $term_id, 'tooltip_text' ) );
                
                // global
                $global_tooltip_text = sanitize_text_field( woo_variation_swatches()->get_frontend()->get_product_attribute_tooltip_text( $term, $data ) );
                
                $tooltip_text = empty( $local_tooltip_text ) ? $global_tooltip_text : $local_tooltip_text;
                
                $tooltip = apply_filters( 'woo_variation_swatches_global_variable_item_tooltip_text', $tooltip_text, $data );
                
                $tooltip = empty( $tooltip ) ? $option_name : $tooltip;
                
                $html_attributes[ 'data-wvstooltip' ] = $tooltip;
                
                return $html_attributes;
            }
            
            public function find_matching_product_variation( $product, $match_attributes = array() ) {
                global $wpdb;
                
                $product_id = $product->get_id();
                
                $uniq_key    = md5( $product_id . serialize( $match_attributes ) . woo_variation_swatches()->get_cache()->get_last_changed() );
                $cache_key   = woo_variation_swatches()->get_cache()->get_key_with_language_suffix( sprintf( 'matching_variation_attributes__%s', $uniq_key ) );
                $cache_group = 'woo_variation_swatches';
                
                if ( false === ( $attributes = wp_cache_get( $cache_key, $cache_group ) ) ) {
                    
                    $meta_attribute_names = array();
                    
                    // Get attributes to match in meta.
                    foreach ( $product->get_attributes() as $attribute ) {
                        if ( ! $attribute->get_variation() ) {
                            continue;
                        }
                        // $meta_attribute_names[] = 'attribute_' . sanitize_title( $attribute->get_name() );
                        $meta_attribute_names[] = wc_variation_attribute_name( $attribute->get_name() );
                    }
                    
                    // Get the attributes of the variations.
                    $query = $wpdb->prepare( "
			SELECT postmeta.post_id, postmeta.meta_key, postmeta.meta_value, posts.menu_order FROM {$wpdb->postmeta} as postmeta
			LEFT JOIN {$wpdb->posts} as posts ON postmeta.post_id=posts.ID
			WHERE postmeta.post_id IN (
				SELECT ID FROM {$wpdb->posts}
				WHERE {$wpdb->posts}.post_parent = %d
				AND {$wpdb->posts}.post_status = 'publish'
				AND {$wpdb->posts}.post_type = 'product_variation'
			)
			", $product->get_id() );
                    
                    $query .= " AND postmeta.meta_key IN ( '" . implode( "','", array_map( 'esc_sql', $meta_attribute_names ) ) . "' )";
                    
                    $query .= ' ORDER BY posts.menu_order ASC, postmeta.post_id ASC;';
                    
                    $attributes = $wpdb->get_results( $query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
                    
                    wp_cache_set( $cache_key, $attributes, $cache_group );
                }
                
                if ( ! $attributes ) {
                    return 0;
                }
                
                $sorted_meta = array();
                
                foreach ( $attributes as $m ) {
                    $sorted_meta[ $m->post_id ][ $m->meta_key ] = $m->meta_value; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
                }
                
                /**
                 * Check each variation to find the one that matches the $match_attributes.
                 *
                 * Note: Not all meta fields will be set which is why we check existance.
                 */
                foreach ( $sorted_meta as $variation_id => $variation ) {
                    $match = false;
                    
                    // Loop over the variation meta keys and values i.e. what is saved to the products. Note: $attribute_value is empty when 'any' is in use.
                    foreach ( $variation as $attribute_key => $attribute_value ) {
                        $match_any_value = '' === $attribute_value;
                        
                        if ( array_key_exists( $attribute_key, $match_attributes ) ) {
                            
                            if ( $match_any_value || $match_attributes[ $attribute_key ] === $attribute_value ) {
                                $match = true; // Provided value does match variation.
                                break;
                            }
                        }
                    }
                    
                    if ( true === $match ) {
                        return $variation_id;
                    }
                }
                
                return 0;
            }
            
            public function item_start( $data, $attribute_type, $variation_data = array() ) {
                
                $args           = $data[ 'args' ];
                $term_or_option = $data[ 'item' ];
                
                $options        = $args[ 'options' ];
                $product        = $args[ 'product' ];
                $attribute      = $args[ 'attribute' ];
                $attribute_name = $data[ 'attribute_name' ];
                
                $is_term     = wc_string_to_bool( $data[ 'is_term' ] );
                $is_selected = $data[ 'is_selected' ];
                $option_name = $data[ 'option_name' ];
                $option_slug = $data[ 'option_slug' ];
                $slug        = $data[ 'slug' ];
                
                $css_class = implode( ' ', array_unique( array_values( apply_filters( 'woo_variation_swatches_variable_item_css_class', $this->get_item_css_classes( $data, $attribute_type, $variation_data ), $data, $attribute_type, $variation_data ) ) ) );
                
                $html_attributes = array(
                    'aria-checked' => ( $is_selected ? 'true' : 'false' ),
                    'tabindex'     => ( wp_is_mobile() ? '2' : '0' ),
                );
                
                $enable_catalog_mode  = wc_string_to_bool( woo_variation_swatches()->get_option( 'enable_catalog_mode', 'no' ) );
                $catalog_mode_trigger = sanitize_text_field( woo_variation_swatches()->get_option( 'catalog_mode_trigger', 'click' ) );
                $linkable_attribute   = wc_string_to_bool( woo_variation_swatches()->get_option( 'linkable_attribute', 'no' ) );
                
                if ( $enable_catalog_mode && $linkable_attribute && $this->is_archive( $args ) ) {
                    
                    $variation_product_id = $this->find_matching_product_variation( $product, array( $attribute_name => $slug ) );
                    $variation_product    = wc_get_product( $variation_product_id );
                    
                    // Attribute link type
                    $link_type = sanitize_text_field( woo_variation_swatches()->get_option( 'linkable_attribute_link_type', 'variation' ) );
                    
                    if ( 'variation' === $link_type ) {
                        $url = is_object( $variation_product ) ? $variation_product->get_permalink() : add_query_arg( array( $attribute_name => $slug ), $product->get_permalink() );
                    } else {
                        $url = add_query_arg( array( $attribute_name => $slug ), $product->get_permalink() );
                    }
                    
                    $html_attributes[ 'data-url' ] = esc_url( $url );
                }
                
                $html_attributes = wp_parse_args( $this->get_item_tooltip_attribute( $data, $attribute_type, $variation_data ), $html_attributes );
                
                $html_attributes = apply_filters( 'woo_variation_swatches_variable_item_custom_attributes', $html_attributes, $data, $attribute_type, $variation_data );
                
                if ( 'mixed' === $attribute_type ) {
                    $attribute_type = $this->get_attribute_type( $data, $variation_data );
                }
                
                return sprintf( '<li %1$s class="variable-item %2$s-variable-item %2$s-variable-item-%3$s %4$s" title="%5$s" data-title="%5$s" data-value="%6$s" role="radio" tabindex="0"><div class="variable-item-contents">', wc_implode_html_attributes( $html_attributes ), esc_attr( $attribute_type ), esc_attr( $option_slug ), esc_attr( $css_class ), esc_html( $option_name ), esc_attr( $slug ) );
            }
            
            public function color_attribute( $data, $attribute_type, $variation_data = array() ) {
                // Color
                if ( 'color' === $attribute_type ) {
                    
                    $term    = $data[ 'item' ];
                    $product = $data[ 'product' ];
                    
                    // Product
                    $product_label_primary_color   = woo_variation_swatches()->get_product_settings( $product, $data[ 'attribute_key' ], 'terms', $data[ 'term_id' ], 'primary_color' );
                    $product_label_is_duel_color   = woo_variation_swatches()->get_product_settings( $product, $data[ 'attribute_key' ], 'terms', $data[ 'term_id' ], 'is_duel_color' );
                    $product_label_secondary_color = woo_variation_swatches()->get_product_settings( $product, $data[ 'attribute_key' ], 'terms', $data[ 'term_id' ], 'secondary_color' );
                    
                    // Global
                    $global_primary_color   = woo_variation_swatches()->get_frontend()->get_product_attribute_primary_color( $term, $data );
                    $global_is_duel_color   = woo_variation_swatches()->get_frontend()->get_product_attribute_is_dual_color( $term, $data );
                    $global_secondary_color = woo_variation_swatches()->get_frontend()->get_product_attribute_secondary_color( $term, $data );
                    $angle                  = woo_variation_swatches()->get_frontend()->get_dual_color_gradient_angle();
                    
                    /*if ( '' === $product_label_primary_color ) {
                        $product_label_primary_color = $global_primary_color;
                    }
                    
                    if ( '' === $product_label_is_duel_color ) {
                        $product_label_is_duel_color = $global_is_duel_color;
                    }
                    
                    if ( '' === $product_label_secondary_color ) {
                        $product_label_secondary_color = $global_secondary_color;
                    }*/
                    
                    // Options
                    $primary_color   = empty( $product_label_primary_color ) ? sanitize_hex_color( $global_primary_color ) : sanitize_hex_color( $product_label_primary_color );
                    $is_duel_color   = empty( $product_label_is_duel_color ) ? wc_string_to_bool( $global_is_duel_color ) : wc_string_to_bool( $product_label_is_duel_color );
                    $secondary_color = empty( $product_label_secondary_color ) ? sanitize_hex_color( $global_secondary_color ) : sanitize_hex_color( $product_label_secondary_color );
                    
                    if ( $is_duel_color ) {
                        return sprintf( '<span class="variable-item-span variable-item-span-color variable-item-span-color-dual" style="background: linear-gradient(%3$s, %1$s 0%%, %1$s 50%%, %2$s 50%%, %2$s 100%%);"></span>', esc_attr( $secondary_color ), esc_attr( $primary_color ), esc_attr( $angle ) );
                    } else {
                        return sprintf( '<span class="variable-item-span variable-item-span-color" style="background-color:%s;"></span>', esc_attr( $primary_color ) );
                    }
                }
            }
            
            public function get_image_attribute_id( $data, $attribute_type, $variation_data = array() ) {
                if ( 'image' === $attribute_type ) {
                    
                    $term           = $data[ 'item' ];
                    $attribute_name = $data[ 'attribute_name' ];
                    $product        = $data[ 'product' ];
                    
                    // Product
                    $product_label_attachment_id = woo_variation_swatches()->get_product_settings( $product, $data[ 'attribute_key' ], 'terms', $data[ 'term_id' ], 'image_id' );
                    
                    // Global
                    $global_attachment_id = apply_filters( 'woo_variation_swatches_global_product_attribute_image_id', woo_variation_swatches()->get_frontend()->get_product_attribute_image( $term, $data ), $data );
                    
                    // Options
                    $attachment_id = empty( $product_label_attachment_id ) ? $global_attachment_id : $product_label_attachment_id;
                    
                    $slug = $data[ 'slug' ];
                    
                    if ( ! empty( $variation_data ) ) {
                        
                        $attribute_type = $variation_data[ $attribute_name ][ $slug ][ 'type' ];
                        if ( 'image' !== $attribute_type ) {
                            return false;
                        }
                        $attachment_id = $variation_data[ $attribute_name ][ $slug ][ 'image_id' ];
                    }
                    
                    //$image_size = apply_filters( 'woo_variation_swatches_global_product_attribute_image_size', sanitize_text_field( woo_variation_swatches()->get_option( 'attribute_image_size', 'variation_swatches_image_size' ) ), $data );
                    
                    return $attachment_id;
                }
            }
            
            public function get_image_attribute( $data, $attribute_type, $variation_data = array() ) {
                if ( 'image' === $attribute_type ) {
                    
                    $term           = $data[ 'item' ];
                    $attribute_name = $data[ 'attribute_name' ];
                    $product        = $data[ 'product' ];
                    
                    // Product
                    $product_label_attachment_id = woo_variation_swatches()->get_product_settings( $product, $data[ 'attribute_key' ], 'terms', $data[ 'term_id' ], 'image_id' );
                    
                    // Global
                    $global_attachment_id = apply_filters( 'woo_variation_swatches_global_product_attribute_image_id', woo_variation_swatches()->get_frontend()->get_product_attribute_image( $term, $data ), $data );
                    
                    if ( empty( $global_attachment_id ) && $data[ 'total_attributes' ] === 1 && $data[ 'variation_image_id' ] > 0 ) {
                        $global_attachment_id = $data[ 'variation_image_id' ];
                    }
                    
                    // Options
                    $attachment_id = empty( $product_label_attachment_id ) ? $global_attachment_id : $product_label_attachment_id;
                    
                    $slug = $data[ 'slug' ];
                    
                    if ( ! empty( $variation_data ) ) {
                        
                        $attribute_type = $variation_data[ $attribute_name ][ $slug ][ 'type' ];
                        if ( 'image' !== $attribute_type ) {
                            return false;
                        }
                        $attachment_id = $variation_data[ $attribute_name ][ $slug ][ 'image_id' ];
                    }
                    
                    $image_size = apply_filters( 'woo_variation_swatches_global_product_attribute_image_size', sanitize_text_field( woo_variation_swatches()->get_option( 'attribute_image_size', 'variation_swatches_image_size' ) ), $data );
                    
                    return wp_get_attachment_image_src( $attachment_id, $image_size );
                }
            }
            
            public function image_attribute( $data, $attribute_type, $variation_data = array() ) {
                
                if ( 'image' === $attribute_type ) {
                    
                    $option_name = $data[ 'option_name' ];
                    $image       = $this->get_image_attribute( $data, $attribute_type, $variation_data );
                    
                    return sprintf( '<img class="variable-item-image" aria-hidden="true" alt="%s" src="%s" width="%d" height="%d" />', esc_attr( $option_name ), esc_url( $image[ 0 ] ), esc_attr( $image[ 1 ] ), esc_attr( $image[ 2 ] ) );
                }
            }
            
            public function button_attribute( $data, $attribute_type, $variation_data = array() ) {
                
                if ( 'button' === $attribute_type ) {
                    
                    $option_name = $data[ 'option_name' ];
                    
                    if ( ! empty( $variation_data ) ) {
                        
                        $attribute_type = $this->get_attribute_type( $data, $variation_data );
                        
                        if ( 'button' !== $attribute_type ) {
                            return;
                        }
                    }
                    
                    return sprintf( '<span class="variable-item-span variable-item-span-button">%s</span>', esc_html( $option_name ) );
                }
            }
            
            public function radio_attribute( $data, $attribute_type, $variation_data = array() ) {
                
                if ( 'radio' === $attribute_type ) {
                    
                    $attribute_name = $data[ 'attribute_name' ];
                    $product        = $data[ 'product' ];
                    
                    $attributes = $product->get_variation_attributes();
                    // $attributes  = $this->get_cached_variation_attributes( $product );
                    $slug        = $data[ 'slug' ];
                    $is_selected = wc_string_to_bool( $data[ 'is_selected' ] );
                    $option_name = $data[ 'option_name' ];
                    // $get_variations       = count( $product->get_children() ) <= apply_filters( 'woocommerce_ajax_variation_threshold', 30, $product );
                    // $available_variations = $get_variations ? $product->get_available_variations() : false;
                    
                    $name            = sprintf( 'wvs_radio_%s__%s', $attribute_name, $product->get_id() );
                    $attribute_value = $slug;
                    
                    $label          = esc_html( $option_name );
                    $label_template = apply_filters( 'woo_variation_swatches_variable_item_radio_label_template', '%image% - %variation% - %price% %stock%', $data, false );
                    
                    if ( $this->is_archive( $data[ 'args' ] ) ) {
                        $label_template = apply_filters( 'woo_variation_swatches_variable_item_radio_label_template', '%image% - %variation%', $data, true );
                    }
                    
                    if ( count( array_keys( $attributes ) ) === 1 ) {
                        
                        $available_variations = $this->get_available_variation_images( $product );
                        
                        $variation = $this->get_variation_by_attribute_name_value( $available_variations, $attribute_name, $attribute_value );
                        
                        $image_id   = $variation[ 'variation_image_id' ];
                        $image_size = sanitize_text_field( woo_variation_swatches()->get_option( 'attribute_image_size', 'variation_swatches_image_size' ) );
                        
                        $variation_image = $this->get_variation_img_src( $image_id, $image_size );
                        
                        $image = sprintf( '<img src="%1$s" title="%2$s" alt="%2$s" width="%3$s" height="%4$s" />', esc_url( $variation_image[ 'src' ] ), $label, absint( $variation_image[ 'width' ] ), absint( $variation_image[ 'height' ] ) );
                        $stock = wp_kses_post( $variation[ 'availability_html' ] );
                        $price = wp_kses_post( $variation[ 'price_html' ] );
                        $label = str_ireplace( array( '%image%', '%variation%', '%price%', '%stock%' ), array( $image, '<span class="variable-item-radio-value">' . $option_name . '</span>', $price, $stock ), $label_template );
                    }
                    
                    return sprintf( '<label class="variable-item-radio-input-wrapper"><input name="%1$s" class="variable-item-radio-input" %2$s  type="radio" value="%3$s" data-value="%3$s" /><span class="variable-item-radio-value-wrapper">%4$s</span></label>', $name, checked( $is_selected, true, false ), esc_attr( $slug ), $label );
                }
            }
            
            public function mixed_attribute( $data, $attribute_type, $variation_data = array() ) {
                
                if ( 'mixed' === $attribute_type ) {
                    
                    $attribute_type = $this->get_attribute_type( $data, $variation_data );
                    
                    if ( 'image' === $attribute_type ) {
                        return $this->image_attribute( $data, $attribute_type, $variation_data );
                    }
                    
                    if ( 'color' === $attribute_type ) {
                        return $this->color_attribute( $data, $attribute_type, $variation_data );
                    }
                    
                    return $this->button_attribute( $data, $attribute_type, $variation_data );
                    
                }
            }
            
            public function item_more( $product, $swatches_data, $incremented ) {
                
                $total_items = count( $swatches_data );
                
                if ( $total_items > $incremented ) {
                    $rest_items = absint( $total_items ) - $incremented;
                    
                    $more_text_string = apply_filters( 'woo_variation_swatches_pro_item_more_text', esc_html__( '+%s More', 'woo-variation-swatches-pro' ), $product );
                    
                    $more_text = sprintf( $more_text_string, number_format_i18n( $rest_items ) );
                    
                    $data = '<li class="woo-variation-swatches-variable-item-more">';
                    $data .= sprintf( '<a style="font-size: small" href="%s">%s</a>', esc_url( $product->get_permalink() ), $more_text );
                    $data .= '</li>';
                    
                    return $data;
                }
            }
            
            public function group_wrapper_start( $args, $group_slug ) {
                
                if ( ! $args[ 'has_group_attribute' ] ) {
                    return '';
                }
                
                $display_limit       = absint( woo_variation_swatches()->get_option( 'display_limit', 0 ) );
                $display_limit_class = ( $display_limit > 0 ) ? 'enabled-display-limit-mode' : '';
                
                if ( $group_slug ) {
                    $group_name = sanitize_text_field( woo_variation_swatches()->get_backend()->get_group()->get( $group_slug ) );
                    
                    return sprintf( '<li class="group-variable-items-wrapper"><div class="group-variable-items-name">%s</div><ul class="group-variable-item-wrapper %s">', $group_name, $display_limit_class );
                }
                
                return sprintf( '<li class="group-variable-items-wrapper no-group-variable-items-wrapper"><ul class="group-variable-item-wrapper %s">', $display_limit_class );
            }
            
            public function group_wrapper_end( $args, $group_slug ) {
                if ( $args[ 'has_group_attribute' ] ) {
                    return '</ul></li>';
                }
                
                return '';
            }
            
            public function found_group( $swatches_data ) {
                $groups = array_keys( $swatches_data );
                
                return count( $groups ) > 1;
            }
            
            public function wrapper_html_attribute( $args, $attribute, $product, $attribute_type, $options ) {
                
                $raw_html_attributes = parent::wrapper_html_attribute( $args, $attribute, $product, $attribute_type, $options );
                
                $enable_single_variation_preview = wc_string_to_bool( woo_variation_swatches()->get_option( 'enable_single_variation_preview', 'no' ) );
                
                if ( $enable_single_variation_preview ) {
                    $local_variation_preview_attribute                    = sanitize_text_field( woo_variation_swatches()->get_product_settings( $product, 'single_variation_preview_attribute' ) );
                    $global_variation_preview_attribute                   = sanitize_text_field( woo_variation_swatches()->get_option( 'single_variation_preview_attribute', '' ) );
                    $variation_preview_attribute                          = empty( $local_variation_preview_attribute ) ? $global_variation_preview_attribute : wc_variation_attribute_name( $local_variation_preview_attribute );
                    $raw_html_attributes[ 'data-preview_attribute_name' ] = $variation_preview_attribute;
                }
                
                return $raw_html_attributes;
            }
            
            public function wrapper_class_group( $args, $swatches_data ) {
                
                $args[ 'has_group_attribute' ] = $this->found_group( $swatches_data );
                
                return $args;
            }
            
            public function single_product_dropdown( $html, $args ) {
                
                if ( apply_filters( 'default_woo_variation_swatches_single_product_dropdown_html', false, $args, $html, $this ) ) {
                    return $html;
                }
                
                // Get selected value.
                if ( empty( $args[ 'selected' ] ) && $args[ 'attribute' ] && $args[ 'product' ] instanceof WC_Product ) {
                    $selected_key = wc_variation_attribute_name( $args[ 'attribute' ] );
                    // phpcs:disable WordPress.Security.NonceVerification.Recommended
                    // $args[ 'selected' ] = isset( $_REQUEST[ $selected_key ] ) ? wc_clean( wp_unslash( $_REQUEST[ $selected_key ] ) ) : $args[ 'product' ]->get_variation_default_attribute( $args[ 'attribute' ] );
                    // $args[ 'selected' ] = isset( $_REQUEST[ $selected_key ] ) ? wc_clean( rawurldecode( wp_unslash( $_REQUEST[ $selected_key ] ) ) ) : $args[ 'product' ]->get_variation_default_attribute( $args[ 'attribute' ] );
                    $args[ 'selected' ] = isset( $_REQUEST[ $selected_key ] ) ? woo_variation_swatches()->sanitize_name( $_REQUEST[ $selected_key ] ) : $args[ 'product' ]->get_variation_default_attribute( $args[ 'attribute' ] );
                    // phpcs:enable WordPress.Security.NonceVerification.Recommended
                }
                
                $options          = $args[ 'options' ];
                $product          = $args[ 'product' ];
                $attribute        = $args[ 'attribute' ];
                $name             = $args[ 'name' ] ? $args[ 'name' ] : wc_variation_attribute_name( $attribute );
                $id               = $args[ 'id' ] ? $args[ 'id' ] : sanitize_title( $attribute );
                $class            = $args[ 'class' ];
                $show_option_none = (bool) $args[ 'show_option_none' ];
                // $show_option_none      = true;
                $show_option_none_text = $args[ 'show_option_none' ] ? $args[ 'show_option_none' ] : esc_html__( 'Choose an option', 'woo-variation-swatches-pro' ); // We'll do our best to hide the placeholder, but we'll need to show something when resetting options.
                
                if ( empty( $options ) && ! empty( $product ) && ! empty( $attribute ) ) {
                    
                    // Variable Product Attribute from WC_Product_Variable
                    $attributes = $product->get_variation_attributes();
                    // $attributes = $this->get_cached_variation_attributes( $product );
                    $options = $attributes[ $attribute ];
                }
                
                
                // Product Settings
                $product_default_to_button    = woo_variation_swatches()->get_product_settings( $product, 'default_to_button' );
                $product_default_to_image     = woo_variation_swatches()->get_product_settings( $product, 'default_to_image' );
                $product_attribute_type       = woo_variation_swatches()->get_product_settings( $product, $attribute, 'type' );
                $product_image_type_attribute = woo_variation_swatches()->get_product_settings( $product, 'default_image_type_attribute' );
                
                
                // Global Settings
                $global_default_to_button = wc_string_to_bool( woo_variation_swatches()->get_option( 'default_to_button', 'yes' ) );
                $global_default_to_image  = wc_string_to_bool( woo_variation_swatches()->get_option( 'default_to_image', 'yes' ) );
                $display_limit            = absint( woo_variation_swatches()->get_option( 'display_limit', 0 ) );
                $get_attribute            = woo_variation_swatches()->get_frontend()->get_attribute_taxonomy_by_name( $attribute );
                $attribute_types          = array_keys( woo_variation_swatches()->get_backend()->extended_attribute_types() );
                $global_attribute_type    = ( $get_attribute ) ? $get_attribute->attribute_type : 'select';
                $swatches_data            = array();
                
                // Actual Settings
                $default_to_button = empty( $product_default_to_button ) ? wc_string_to_bool( $global_default_to_button ) : wc_string_to_bool( $product_default_to_button );
                $convert_to_image  = empty( $product_default_to_image ) ? wc_string_to_bool( $global_default_to_image ) : wc_string_to_bool( $product_default_to_image );
                $attribute_type    = empty( $product_attribute_type ) ? $global_attribute_type : $product_attribute_type;
                
                // Exclude Category
                $global_exclude_categories = map_deep( woo_variation_swatches()->get_option( 'exclude_categories', array() ), 'absint' );
                $product_cats_ids          = wc_get_product_term_ids( $product->get_id(), 'product_cat' );
                
                $exclude = array_filter( $global_exclude_categories, function ( $value ) use ( $product_cats_ids ) {
                    return in_array( $value, $product_cats_ids );
                } );
                
                if ( ! empty( $exclude ) ) {
                    return $html;
                }
                
                if ( ! in_array( $attribute_type, $attribute_types ) ) {
                    return $html;
                }
                
                $select_inline_style = '';
                
                $variation_data = array();
                
                if ( $convert_to_image && $attribute_type === 'select' ) {
                    
                    $attributes = $product->get_variation_attributes();
                    // $attributes           = $this->get_cached_variation_attributes( $product );
                    $first_attribute      = array_key_first( $attributes );
                    $image_type_attribute = empty( $product_image_type_attribute ) ? $first_attribute : sanitize_text_field( $product_image_type_attribute );
                    
                    if ( $image_type_attribute === $attribute ) {
                        $available_variations = $this->get_available_variation_images( $product );
                        // NOTE: Any variation value not work.
                        $variation_data = $this->get_variation_data_by_attribute_name( $available_variations, $image_type_attribute );
                        
                        //  $local_attribute_type = woo_variation_swatches()->get_product_settings( $product, woo_variation_swatches()->sanitize_name( $attribute ), 'type' );
                        $attribute_type = empty( $variation_data ) ? 'button' : 'mixed';
                    }
                }
                
                if ( $default_to_button && $attribute_type === 'select' ) {
                    $attribute_type = 'button';
                }
                
                if ( in_array( $attribute_type, array( 'mixed', 'custom', 'color', 'radio', 'image', 'button' ) ) ) {
                    $select_inline_style = 'style="display:none"';
                    $class               .= ' woo-variation-raw-select';
                }
                
                $html = '<select ' . $select_inline_style . ' id="' . esc_attr( $id ) . '" class="' . esc_attr( $class ) . '" name="' . esc_attr( $name ) . '" data-attribute_name="' . esc_attr( wc_variation_attribute_name( $attribute ) ) . '" data-show_option_none="' . ( $show_option_none ? 'yes' : 'no' ) . '">';
                $html .= '<option value="">' . esc_html( $show_option_none_text ) . '</option>';
                
                if ( ! empty( $options ) ) {
                    if ( $product && taxonomy_exists( $attribute ) ) {
                        // Get terms if this is a taxonomy - ordered. We need the names too.
                        $terms = wc_get_product_terms( $product->get_id(), $attribute, array(
                            'fields' => 'all',
                        ) );
                        
                        foreach ( $terms as $term ) {
                            if ( in_array( $term->slug, $options, true ) ) {
                                $swatch_data = $this->get_swatch_data( $args, $term );
                                if ( $swatch_data[ 'group_slug' ] ) {
                                    $swatches_data[ $swatch_data[ 'group_slug' ] ][] = $swatch_data;
                                } else {
                                    $swatches_data[ 0 ][] = $swatch_data;
                                }
                                // $swatches_data[] = $this->get_swatch_data( $args, $term );
                                $html .= '<option value="' . esc_attr( $term->slug ) . '" ' . selected( sanitize_title( $args[ 'selected' ] ), $term->slug, false ) . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $term->name, $term, $attribute, $product ) ) . '</option>';
                            }
                        }
                    } else {
                        foreach ( $options as $option ) {
                            // This handles < 2.4.0 bw compatibility where text attributes were not sanitized.
                            $selected    = sanitize_title( $args[ 'selected' ] ) === $args[ 'selected' ] ? selected( $args[ 'selected' ], sanitize_title( $option ), false ) : selected( $args[ 'selected' ], $option, false );
                            $swatch_data = $this->get_swatch_data( $args, $option );
                            // $swatches_data[] = $this->get_swatch_data( $args, $option );
                            
                            if ( $swatch_data[ 'group_slug' ] ) {
                                $swatches_data[ $swatch_data[ 'group_slug' ] ][] = $swatch_data;
                            } else {
                                $swatches_data[ 0 ][] = $swatch_data;
                            }
                            
                            $html .= '<option value="' . esc_attr( $option ) . '" ' . $selected . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $option, null, $attribute, $product ) ) . '</option>';
                        }
                    }
                }
                
                $html .= '</select>';
                
                if ( $attribute_type === 'select' ) {
                    return $html;
                }
                
                // Start Swatches
                $attributes = $product->get_variation_attributes();
                // $attributes      = $this->get_cached_variation_attributes( $product );
                $first_attribute = array_key_first( $attributes );
                
                $item        = '';
                $wrapper     = '';
                $wrapper_end = '';
                
                if ( ! empty( $options ) && ! empty( $swatches_data ) && $product ) {
                    
                    $args = $this->wrapper_class_group( $args, $swatches_data );
                    
                    $wrapper     = $this->wrapper_start( $args, $attribute, $product, $attribute_type, $options );
                    $increment   = 0;
                    $incremented = 0;
                    
                    foreach ( $swatches_data as $group => $swatches_item ) {
                        
                        $item .= $this->group_wrapper_start( $args, $group );
                        
                        foreach ( $swatches_item as $data ) {
                            
                            
                            /*echo '<pre>';
                            print_r( $data );
                            echo '</pre>';*/
                            
                            
                            // If attribute have no image we should convert attribute type image to attribute type button
                            if ( 'image' === $attribute_type && ! is_array( $this->get_image_attribute( $data, $attribute_type, $variation_data ) ) ) {
                                $attribute_type = 'button';
                            }
                            
                            
                            $item .= $this->item_start( $data, $attribute_type, $variation_data );
                            
                            $item .= $this->mixed_attribute( $data, $attribute_type, $variation_data );
                            $item .= $this->color_attribute( $data, $attribute_type, $variation_data );
                            $item .= $this->image_attribute( $data, $attribute_type, $variation_data );
                            $item .= $this->button_attribute( $data, $attribute_type, $variation_data );
                            $item .= $this->radio_attribute( $data, $attribute_type, $variation_data );
                            
                            $item .= $this->item_end();
                            
                            if ( $display_limit > 0 && $display_limit === ( $increment + 1 ) ) {
                                $incremented = $increment;
                            }
                            
                            $increment ++;
                        }
                        
                        
                        if ( $display_limit > 0 && $display_limit < $increment ) {
                            $item .= $this->item_more( $product, $swatches_item, ( $incremented + 1 ) );
                        }
                        
                        $item .= $this->group_wrapper_end( $args, $group );
                        
                    }
                    
                    $wrapper_end = $this->wrapper_end();
                }
                
                // End Swatches
                $html .= $wrapper . $item . $wrapper_end;
                
                return apply_filters( 'woo_variation_swatches_html', $html, $args, $swatches_data, $this );
            }
            
            public function archive_product_dropdown( $html, $args ) {
                return $html;
            }
            
            public function dropdown( $html, $args ) {
                
                $args = wp_parse_args( apply_filters( 'woocommerce_dropdown_variation_attribute_options_args', $args ), array(
                    'options'          => false,
                    'attribute'        => false,
                    'product'          => false,
                    'selected'         => false,
                    'name'             => '',
                    'id'               => '',
                    'class'            => '',
                    'show_option_none' => esc_html__( 'Choose an option', 'woo-variation-swatches-pro' ),
                    'is_archive'       => false
                ) );
                
                if ( $this->is_archive( $args ) ) {
                    return $this->archive_product_dropdown( $html, $args );
                } else {
                    return $this->single_product_dropdown( $html, $args );
                }
            }
            
            public function get_swatch_data( $args, $term_or_option ) {
                
                $options          = $args[ 'options' ];
                $product          = $args[ 'product' ];
                $attribute        = $args[ 'attribute' ];
                $attributes       = $product->get_variation_attributes();
                $count_attributes = count( array_keys( $attributes ) );
                
                
                $is_term = is_object( $term_or_option );
                
                $group_slug = false;
                $group_name = false;
                
                if ( $is_term ) {
                    
                    $term        = $term_or_option;
                    $slug        = $term->slug;
                    $is_selected = ( sanitize_title( $args[ 'selected' ] ) === $term->slug );
                    $option_name = apply_filters( 'woocommerce_variation_option_name', $term->name, $term, $attribute, $product );
                    
                    $group_slug = woo_variation_swatches()->get_frontend()->get_product_attribute_group_slug( $term );
                    $group_name = woo_variation_swatches()->get_frontend()->get_product_attribute_group_name( $term );
                    if ( empty( $group_name ) ) {
                        $group_slug = $group_name = false;
                    }
                } else {
                    $option      = $slug = $term_or_option;
                    $is_selected = ( sanitize_title( $args[ 'selected' ] ) === $args[ 'selected' ] ) ? ( $args[ 'selected' ] === sanitize_title( $option ) ) : ( $args[ 'selected' ] === $option );
                    $option_name = apply_filters( 'woocommerce_variation_option_name', $option, null, $attribute, $product );
                    // $group_slug = false;
                    // $group_name  = false;
                }
                
                
                $attribute_name  = wc_variation_attribute_name( $attribute );
                $attribute_value = $slug;
                
                $single_attribute_variation_image_id = 0;
                if ( count( array_keys( $attributes ) ) === 1 ) {
                    $available_variations = $this->get_available_variation_images( $product );
                    
                    $variation = $this->get_variation_by_attribute_name_value( $available_variations, $attribute_name, $attribute_value );
                    
                    $single_attribute_variation_image_id = $variation[ 'variation_image_id' ];
                }
                
                return array(
                    'group_slug'         => $group_slug,
                    'group_name'         => $group_name,
                    'is_selected'        => $is_selected,
                    'is_term'            => $is_term,
                    'term_id'            => $is_term ? $term->term_id : woo_variation_swatches()->sanitize_name( $option ),
                    'option_slug'        => woo_variation_swatches()->sanitize_name( $slug ),
                    'slug'               => $slug,
                    'variation_image_id' => absint( $single_attribute_variation_image_id ),
                    'total_attributes'   => absint( $count_attributes ),
                    'item'               => $term_or_option,
                    'options'            => $options,
                    'option_name'        => $option_name,
                    'attribute'          => $attribute,
                    'attribute_key'      => sanitize_title( $attribute ),
                    'attribute_name'     => wc_variation_attribute_name( $attribute ),
                    'attribute_label'    => wc_attribute_label( $attribute, $product ),
                    'args'               => $args,
                    'product'            => $product,
                );
            }
        }
    }
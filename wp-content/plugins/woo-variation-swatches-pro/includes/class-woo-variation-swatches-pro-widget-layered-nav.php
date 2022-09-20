<?php
    
    defined( 'ABSPATH' ) || exit;
    
    /**
     * Widget layered nav class.
     */
    class Woo_Variation_Swatches_Pro_Widget_Layered_Nav extends WC_Widget_Layered_Nav {
        
        protected function layered_nav_list( $terms, $taxonomy, $query_type ) {
            // List display.
            
            echo '<ul class="wvs-widget-layered-nav-list woocommerce-widget-layered-nav-list">';
            
            $term_counts        = $this->get_filtered_term_product_counts( wp_list_pluck( $terms, 'term_id' ), $taxonomy, $query_type );
            $_chosen_attributes = WC_Query::get_layered_nav_chosen_attributes();
            $found              = false;
            $base_link          = $this->get_current_page_url();
            
            foreach ( $terms as $term ) {
                $current_values = isset( $_chosen_attributes[ $taxonomy ][ 'terms' ] ) ? $_chosen_attributes[ $taxonomy ][ 'terms' ] : array();
                $option_is_set  = in_array( $term->slug, $current_values, true );
                $count          = isset( $term_counts[ $term->term_id ] ) ? $term_counts[ $term->term_id ] : 0;
                
                // Skip the term for the current archive.
                if ( $this->get_current_term_id() === $term->term_id ) {
                    continue;
                }
                
                // Only show options with count > 0.
                if ( 0 < $count ) {
                    $found = true;
                } elseif ( 0 === $count && ! $option_is_set ) {
                    continue;
                }
                
                $filter_name = 'filter_' . wc_attribute_taxonomy_slug( $taxonomy );
                // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                $current_filter = isset( $_GET[ $filter_name ] ) ? explode( ',', wc_clean( wp_unslash( $_GET[ $filter_name ] ) ) ) : array();
                $current_filter = array_map( 'sanitize_title', $current_filter );
                
                if ( ! in_array( $term->slug, $current_filter, true ) ) {
                    $current_filter[] = $term->slug;
                }
                
                $link = remove_query_arg( $filter_name, $base_link );
                
                // Add current filters to URL.
                foreach ( $current_filter as $key => $value ) {
                    // Exclude query arg for current term archive term.
                    if ( $value === $this->get_current_term_slug() ) {
                        unset( $current_filter[ $key ] );
                    }
                    
                    // Exclude self so filter can be unset on click.
                    if ( $option_is_set && $value === $term->slug ) {
                        unset( $current_filter[ $key ] );
                    }
                }
                
                if ( ! empty( $current_filter ) ) {
                    asort( $current_filter );
                    $link = add_query_arg( $filter_name, implode( ',', $current_filter ), $link );
                    
                    // Add Query type Arg to URL.
                    if ( 'or' === $query_type && ! ( 1 === count( $current_filter ) && $option_is_set ) ) {
                        $link = add_query_arg( 'query_type_' . wc_attribute_taxonomy_slug( $taxonomy ), 'or', $link );
                    }
                    $link = str_replace( '%2C', ',', $link );
                }
                
                if ( $count > 0 || $option_is_set ) {
                    $link      = apply_filters( 'woocommerce_layered_nav_link', $link, $term, $taxonomy );
                    $term_html = '<a rel="nofollow" href="' . esc_url( $link ) . '"><span class="text">' . esc_html( $term->name ) . '</span></a>';
                } else {
                    $link      = false;
                    $term_html = '<span class="text">' . esc_html( $term->name ) . '</span>';
                }
                
                
                $attribute = woo_variation_swatches()->get_frontend()->get_attribute_taxonomy_by_name( $term->taxonomy );
                
                if ( woo_variation_swatches()->get_frontend()->is_color_attribute( $attribute ) ) {
                    
                    echo '<li class="wvs-widget-layered-nav-list__item wvs-widget-layered-nav-list__item-color woocommerce-widget-layered-nav-list__item wc-layered-nav-term ' . ( $option_is_set ? 'woocommerce-widget-layered-nav-list__item--chosen chosen selected' : '' ) . '">';
                    // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.EscapeOutput.OutputNotEscaped
                    
                    $is_dual_color = woo_variation_swatches()->get_frontend()->get_product_attribute_is_dual_color( $term );
                    $wrapper_class = 'wvs-widget-item-wrapper';
                    $item_class    = sprintf( 'style-%s', woo_variation_swatches()->get_option( 'shape_style', 'squared' ) );
                    
                    
                    if ( $is_dual_color ) {
                        $wrapper_class   .= ' wvs-widget-dual-color-item-wrapper';
                        $primary_color   = sanitize_hex_color( woo_variation_swatches()->get_frontend()->get_product_attribute_primary_color( $term ) );
                        $secondary_color = sanitize_hex_color( woo_variation_swatches()->get_frontend()->get_product_attribute_secondary_color( $term ) );
                        $angle           = woo_variation_swatches()->get_frontend()->get_dual_color_gradient_angle();
                        
                        $item_html = '<span class="' . $item_class . ' item wvs-dual-color-item" style="background: linear-gradient(' . esc_attr( $angle ) . ', ' . esc_attr( $secondary_color ) . ' 0%, ' . esc_attr( $secondary_color ) . ' 50%, ' . esc_attr( $primary_color ) . ' 50%, ' . esc_attr( $primary_color ) . ' 100%);"></span>';
                    } else {
                        $primary_color = sanitize_hex_color( woo_variation_swatches()->get_frontend()->get_product_attribute_primary_color( $term ) );
                        
                        $item_html = '<span class="' . $item_class . ' item" style="background-color: ' . esc_attr( $primary_color ) . '"></span>';
                    }
                    
                    
                    if ( $count > 0 || $option_is_set ) {
                        $link      = apply_filters( 'woocommerce_layered_nav_link', $link, $term, $taxonomy );
                        $term_html = '<a rel="nofollow" href="' . esc_url( $link ) . '"><div data-wvstooltip="' . esc_attr( $term->name ) . '" class="' . $wrapper_class . '">' . $item_html . '<span class="text">' . esc_html( $term->name ) . '</span></div></a>';
                    } else {
                        $link = false;
                        
                        $term_html = '<div data-wvstooltip="' . esc_attr( $term->name ) . '" class="' . $wrapper_class . '">' . $item_html . '<span class="text">' . esc_html( $term->name ) . '</span></div>';
                    }
                } else {
                    echo '<li class="wvs-widget-layered-nav-list__item woocommerce-widget-layered-nav-list__item wc-layered-nav-term ' . ( $option_is_set ? 'woocommerce-widget-layered-nav-list__item--chosen chosen' : '' ) . '">';
                    // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.EscapeOutput.OutputNotEscaped
                    
                }
                
                $term_html .= ' ' . apply_filters( 'woocommerce_layered_nav_count', '<span class="count">(' . absint( $count ) . ')</span>', $count, $term );
                
                echo apply_filters( 'woocommerce_layered_nav_term_html', $term_html, $term, $link, $count );
                echo '</li>';
            }
            
            echo '</ul>';
            
            return $found;
        }
    }

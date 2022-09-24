<?php
    defined( 'ABSPATH' ) || exit;
    
    if ( ! class_exists( 'Woo_Variation_Swatches_Pro_Archive_Page' ) ) {
        class Woo_Variation_Swatches_Pro_Archive_Page extends Woo_Variation_Swatches_Pro_Product_Page {
            
            protected static $_instance = null;
            
            protected function __construct() {
                parent::__construct();
                do_action( 'woo_variation_swatches_pro_archive_page_loaded', $this );
            }
            
            public static function instance() {
                if ( is_null( self::$_instance ) ) {
                    self::$_instance = new self();
                }
                
                return self::$_instance;
            }
            
            protected function hooks() {
                
                parent::hooks();
                
                add_filter( 'woocommerce_post_class', array( $this, 'post_class' ), 10, 2 );
                
                add_action( 'woocommerce_init', array( $this, 'enable_swatches' ), 1 );
                add_action( 'wc_ajax_woo_get_variations', array( $this, 'get_variations' ) );
                add_action( 'wc_ajax_woo_get_variation', array( $this, 'get_variation' ) );
                add_action( 'wc_ajax_woo_get_preview_variation', array( $this, 'get_preview_variation' ) );
                add_action( 'wc_ajax_woo_add_to_cart_variation', array( $this, 'add_to_cart_variation' ) );
                
                add_filter( 'woo_variation_swatches_js_options', array( $this, 'extra_js_options' ) );
                add_action( 'wp_print_footer_scripts', array( $this, 'variable_template' ) );
                add_filter( 'wp_get_attachment_image_attributes', array( $this, 'add_image_class' ), 9 );
                add_filter( 'woocommerce_loop_add_to_cart_args', array( $this, 'add_to_cart_args' ), 20, 2 );
                
                add_action( 'widgets_init', array( $this, 'register_widget' ) );
                
                // WPML Support
                add_filter( 'woo_variation_swatches_product_options', array( $this, 'wpml_translate_product_options' ) );
            }
            
            // Start
            
            // WPML Support: https://wpml.org/forums/topic/variation-swatches-for-woocommerce-not-working-correctly-using-wpml/
            public function wpml_object_id( $object_id, $type = 'post', $language = null ) {
                $current_language = apply_filters( 'wpml_current_language', $language );
                
                return apply_filters( 'wpml_object_id', $object_id, $type, true, $current_language );
            }
            
            public function wpml_translate_product_options( $value ) {
                foreach ( $value as $slug => $data ) {
                    if ( is_array( $data ) && array_key_exists( 'terms', $data ) ) {
                        $value[ $slug ][ 'terms' ] = $this->wpml_translate_product_option_terms( $slug, $data[ 'terms' ] );
                    }
                }
                
                return $value;
            }
            
            public function wpml_translate_product_option_terms( $slug, $terms ) {
                
                $out = array();
                foreach ( $terms as $term_id => $data ) {
                    if ( ! empty( $data[ 'image_id' ] ) && ! empty( $data[ 'type' ] ) && 'image' == $data[ 'type' ] ) {
                        $data[ 'image_id' ] = $this->wpml_object_id( $data[ 'image_id' ], 'attachment' );
                    }
                    
                    if ( term_exists( $term_id, $slug ) ) {
                        $term_id = $this->wpml_object_id( $term_id, $slug );
                    }
                    
                    $out[ $term_id ] = $data;
                }
                
                return $out;
            }
            
            // WPML Done
            
            public function register_widget() {
                if ( wc_string_to_bool( woo_variation_swatches()->get_option( 'show_swatches_on_filter_widget', 'yes' ) ) ) {
                    unregister_widget( 'WC_Widget_Layered_Nav' );
                    register_widget( 'Woo_Variation_Swatches_Pro_Widget_Layered_Nav' );
                }
            }
            
            public function add_to_cart_variation() {
                // WC_AJAX::add_to_cart();
                
                ob_start();
                
                // phpcs:disable WordPress.Security.NonceVerification.Missing
                if ( ! isset( $_POST[ 'product_id' ] ) ) {
                    return;
                }
                
                $product_id        = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $_POST[ 'product_id' ] ) );
                $product           = wc_get_product( $product_id );
                $quantity          = empty( $_POST[ 'quantity' ] ) ? 1 : wc_stock_amount( wp_unslash( $_POST[ 'quantity' ] ) );
                $passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );
                $product_status    = get_post_status( $product_id );
                $variation_id      = 0;
                $variation         = array();
                
                if ( $product && 'variation' === $product->get_type() ) {
                    $variation_id = $product_id;
                    $product_id   = $product->get_parent_id();
                    $variation    = $this->find_attributes( $_POST );
                }
                
                if ( $passed_validation && false !== WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation ) && 'publish' === $product_status ) {
                    
                    do_action( 'woocommerce_ajax_added_to_cart', $product_id );
                    
                    if ( 'yes' === get_option( 'woocommerce_cart_redirect_after_add' ) ) {
                        wc_add_to_cart_message( array( $product_id => $quantity ), true );
                    }
                    
                    WC_AJAX::get_refreshed_fragments();
                    
                } else {
                    
                    // If there was an error adding to the cart, redirect to the product page to show any errors.
                    $data = array(
                        'error'       => true,
                        'product_url' => apply_filters( 'woocommerce_cart_redirect_after_error', get_permalink( $product_id ), $product_id ),
                    );
                    
                    wp_send_json( $data );
                }
                // phpcs:enable
            }
            
            public function find_attributes( $raw_attributes = array() ) {
                $attributes = array();
                foreach ( $raw_attributes as $key => $value ) {
                    // Get attribute by prefix which meta gets stored with.
                    if ( 0 === strpos( $key, 'attribute_' ) ) {
                        $attributes[ $key ] = $value;
                    }
                }
                
                return $attributes;
            }
            
            public function add_to_cart_args( $args, $product ) {
                
                if ( $product->is_type( 'variable' ) && wc_string_to_bool( woo_variation_swatches()->get_option( 'show_on_archive', 'yes' ) ) ) {
                    
                    $classes = array_unique( explode( ' ', $args[ 'class' ] ) );
                    
                    if ( ! isset( $args[ 'attributes' ] ) ) {
                        $args[ 'attributes' ] = array();
                    }
                    
                    $classes[] = 'wvs-add-to-cart-button';
                    
                    if ( 'yes' === get_option( 'woocommerce_enable_ajax_add_to_cart' ) && ! wc_string_to_bool( woo_variation_swatches()->get_option( 'enable_catalog_mode', 'no' ) ) ) {
                        $classes[] = 'wvs_ajax_add_to_cart';
                    }
                    
                    $args[ 'class' ] = implode( ' ', $classes );
                    
                    // $args[ 'attributes' ][ 'data-variation_id' ]         = '';
                    // $args[ 'attributes' ][ 'data-variation_attributes' ] = '';
                }
                
                return $args;
            }
            
            public function wrapper_class( $args, $attribute, $product, $attribute_type ) {
                
                $classes = array();
                
                
                $enable_catalog_mode       = wc_string_to_bool( woo_variation_swatches()->get_option( 'enable_catalog_mode', 'no' ) );
                $enable_large_size         = wc_string_to_bool( woo_variation_swatches()->get_option( 'enable_large_size', 'no' ) );
                $large_size_attribute_name = sanitize_text_field( woo_variation_swatches()->get_option( 'large_size_attribute', '' ) );
                
                $group_swatches_align = sanitize_text_field( woo_variation_swatches()->get_option( 'group_swatches_align', 'horizontal' ) );
                
                $display_limit             = absint( woo_variation_swatches()->get_option( 'display_limit', 0 ) );
                $enable_display_limit_mode = ( $display_limit > 0 );
                
                $global_shape = woo_variation_swatches()->get_option( 'shape_style', 'squared' );
                $local_shape  = woo_variation_swatches()->get_product_settings( $product, woo_variation_swatches()->sanitize_name( $attribute ), 'style' );
                $shape        = empty( $local_shape ) ? $global_shape : $local_shape;
                
                if ( $this->is_archive( $args ) ) {
                    $classes[] = 'archive-variable-items';
                    if ( $enable_catalog_mode ) {
                        $classes[] = 'enabled-catalog-display-limit-mode';
                    }
                } else {
                    
                    if ( $args[ 'has_group_attribute' ] ) {
                        $classes[] = 'grouped-variable-items';
                        $classes[] = sprintf( 'grouped-variable-items-display-%s', $group_swatches_align );
                    }
                    
                    $classes[] = 'single-product-variable-items';
                    
                    if ( $enable_display_limit_mode ) {
                        $classes[] = 'enabled-display-limit-mode';
                        // $classes[] = 'enabled-catalog-mode';
                    }
                    
                    if ( $enable_large_size ) {
                        $product        = $args[ 'product' ];
                        $attributes     = $product->get_variation_attributes();
                        $attribute_name = wc_variation_attribute_name( $attribute );
                        if ( empty( $large_size_attribute_name ) ) {
                            $large_size_attribute_name = wc_variation_attribute_name( array_key_first( $attributes ) );
                        }
                        
                        if ( $large_size_attribute_name === $attribute_name ) {
                            $classes[] = 'enabled-large-size';
                        }
                    }
                }
                
                
                $classes[] = sanitize_text_field( sprintf( 'wvs-style-%s', $shape ) );
                $classes[] = 'variable-items-wrapper';
                $classes[] = sanitize_text_field( sprintf( '%s-variable-items-wrapper', $attribute_type ) );
                
                return $classes;
            }
            
            public function enable_swatches() {
                
                $enable   = wc_string_to_bool( woo_variation_swatches()->get_option( 'show_on_archive', 'yes' ) );
                $position = sanitize_text_field( woo_variation_swatches()->get_option( 'archive_swatches_position', 'after' ) );
                
                if ( ! $enable ) {
                    return;
                }
                
                if ( 'after' === $position ) {
                    add_action( 'woocommerce_after_shop_loop_item', array( $this, 'after_shop_loop_item' ), 30 );
                } else {
                    add_action( 'woocommerce_after_shop_loop_item', array( $this, 'after_shop_loop_item' ), 7 );
                }
            }
            
            public function add_image_class( $attr ) {
                if ( ! is_admin() ) {
                    
                    $classes = (array) explode( ' ', $attr[ 'class' ] );
                    
                    array_push( $classes, 'wvs-archive-product-image' );
                    
                    $attr[ 'class' ] = implode( ' ', array_unique( $classes ) );
                }
                
                return $attr;
            }
            
            public function variable_template() {
                // We also need the wp.template for this script :).
                
                if ( wc_string_to_bool( woo_variation_swatches()->get_option( 'archive_show_availability', 'no' ) ) ) {
                    woo_variation_swatches()->get_template( 'variation-template.php' );
                }
            }
            
            public function get_product_thumbnail_image( $attachment_id = null, $product = false, $fallback = false ) {
                $props = array(
                    //'title'   => '',
                    //'caption' => '',
                    //'url'    => '',
                    'alt'    => '',
                    'src'    => '',
                    'srcset' => false,
                
                );
                
                if ( empty( $attachment_id ) && $fallback ) {
                    $attachment_id = get_option( 'woocommerce_placeholder_image', 0 );;
                }
                
                $attachment = get_post( $attachment_id );
                
                if ( $attachment && 'attachment' === $attachment->post_type ) {
                    // $props['alt'] = wp_strip_all_tags( $attachment->post_title );
                    
                    $props[ 'alt' ] = wp_strip_all_tags( get_the_title( $product->get_id() ) );
                    
                    //$props['url'] = wp_get_attachment_url( $attachment_id );
                    
                    // Thumbnail version.
                    $image_size        = apply_filters( 'woocommerce_thumbnail_size', 'woocommerce_thumbnail' );
                    $src               = wp_get_attachment_image_src( $attachment_id, $image_size );
                    $props[ 'src' ]    = $src[ 0 ];
                    $props[ 'src_w' ]  = $src[ 1 ];
                    $props[ 'src_h' ]  = $src[ 2 ];
                    $props[ 'srcset' ] = wp_get_attachment_image_srcset( $attachment_id, $image_size );
                    $props[ 'sizes' ]  = wp_get_attachment_image_sizes( $attachment_id, $image_size );
                    
                }
                
                return $props;
            }
            
            // Ajax get variations
            public function get_variations() {
                ob_start();
                // phpcs:disable WordPress.Security.NonceVerification.Missing
                if ( empty( $_POST[ 'product_id' ] ) ) {
                    wp_die();
                }
                
                $product    = wc_get_product( absint( $_POST[ 'product_id' ] ) );
                $is_archive = wc_string_to_bool( $_POST[ 'is_archive' ] );
                
                if ( ! $product ) {
                    wp_die();
                }
                
                if ( $is_archive ) {
                    $available_variations = $this->get_available_variations( $product );
                } else {
                    $available_variations = $product->get_available_variations();
                }
                
                wp_send_json( $available_variations );
                // phpcs:enable
            }
            
            // Ajax get variation
            public function get_variation() {
                ob_start();
                
                // phpcs:disable WordPress.Security.NonceVerification.Missing
                if ( empty( $_POST[ 'product_id' ] ) ) {
                    wp_die();
                }
                
                $variable_product = wc_get_product( absint( $_POST[ 'product_id' ] ) );
                
                if ( ! $variable_product ) {
                    wp_die();
                }
                
                $enable_catalog_mode = wc_string_to_bool( woo_variation_swatches()->get_option( 'enable_catalog_mode', 'no' ) );
                
                if ( $enable_catalog_mode ) {
                    $variation_id = $this->find_matching_product_variation( $variable_product, wp_unslash( $_POST ) );
                } else {
                    $data_store   = WC_Data_Store::load( 'product' );
                    $variation_id = $data_store->find_matching_product_variation( $variable_product, wp_unslash( $_POST ) );
                }
                
                // $variation    = $variation_id ? $variable_product->get_available_variation( $variation_id ) : false;
                $variation = $variation_id ? $this->get_available_variation( $variation_id, $variable_product ) : false;
                wp_send_json( $variation );
                // phpcs:enable
            }
            
            public function get_preview_variation() {
                ob_start();
                
                // phpcs:disable WordPress.Security.NonceVerification.Missing
                if ( empty( $_POST[ 'product_id' ] ) ) {
                    wp_die();
                }
                
                $variable_product = wc_get_product( absint( $_POST[ 'product_id' ] ) );
                
                if ( ! $variable_product ) {
                    wp_die();
                }
                
                
                $variation_id = $this->find_matching_product_variation( $variable_product, wp_unslash( $_POST ) );
                
                
                // $variation    = $variation_id ? $variable_product->get_available_variation( $variation_id ) : false;
                $variation = $variation_id ? $this->get_available_preview_variation( $variation_id, $variable_product ) : false;
                wp_send_json( $variation );
                // phpcs:enable
            }
            
            public function js_params() {
                
                return apply_filters( 'woo_variation_swatches_js_params', array(
                    'ajax_url'                         => WC()->ajax_url(),
                    'wc_ajax_url'                      => WC_AJAX::get_endpoint( '%%endpoint%%' ),
                    'i18n_no_matching_variations_text' => esc_attr__( 'Sorry, no products matched your selection. Please choose a different combination.', 'woo-variation-swatches-pro' ),
                    'i18n_make_a_selection_text'       => esc_attr__( 'Please select some product options before adding this product to your cart.', 'woo-variation-swatches-pro' ),
                    'i18n_unavailable_text'            => esc_attr__( 'Sorry, this product is unavailable. Please choose a different combination.', 'woo-variation-swatches-pro' ),
                ) );
            }
            
            public function enqueue_scripts() {
                
                $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
                
                parent::enqueue_scripts();
                
                if ( is_product() ) {
                    $product = wc_get_product();
                    
                    // Disable Pro
                    if ( apply_filters( 'disable_woo_variation_swatches_archive_product', false, $product, $this ) ) {
                        return;
                    }
                    
                    if ( $product->is_type( 'variable' ) ) {
                        wp_deregister_script( 'wc-add-to-cart-variation' );
                        wp_register_script( 'wc-add-to-cart-variation', woo_variation_swatches()->pro_assets_url( "/js/add-to-cart-variation{$suffix}.js" ), array( 'jquery', 'wp-util', 'underscore', 'jquery-blockui' ), woo_variation_swatches()->pro_assets_version( "/js/add-to-cart-variation{$suffix}.js" ), true );
                    }
                }
                
                wp_register_script( 'woo-variation-swatches-pro', woo_variation_swatches()->pro_assets_url( "/js/frontend-pro{$suffix}.js" ), array( 'jquery', 'wp-util', 'underscore', 'jquery-blockui' ), woo_variation_swatches()->pro_assets_version( "/js/frontend-pro{$suffix}.js" ), true );
                
                wp_localize_script( 'woo-variation-swatches-pro', 'woo_variation_swatches_pro_params', $this->js_params() );
                wp_localize_script( 'woo-variation-swatches-pro', 'woo_variation_swatches_pro_options', $this->js_options() );
                
                wp_localize_script( 'wc-add-to-cart-variation', 'woo_variation_swatches_pro_options', $this->js_options() );
                wp_localize_script( 'wc-add-to-cart-variation', 'woo_variation_swatches_pro_params', $this->js_params() );
                
            }
            
            public function extra_js_options( $options ) {
                
                $options[ 'enable_linkable_url' ]                      = wc_string_to_bool( woo_variation_swatches()->get_option( 'enable_linkable_variation_url', 'no' ) );
                $options[ 'clickable_out_of_stock' ]                   = wc_string_to_bool( woo_variation_swatches()->get_option( 'clickable_out_of_stock_variation', 'no' ) );
                $options[ 'out_of_stock_tooltip_text' ]                = esc_html__( '(Unavailable)', 'woo-variation-swatches-pro' );
                $options[ 'archive_product_wrapper' ]                  = sanitize_text_field( woo_variation_swatches()->get_option( 'archive_product_wrapper', '.wvs-archive-product-wrapper' ) );
                $options[ 'archive_image_selector' ]                   = sanitize_text_field( woo_variation_swatches()->get_option( 'archive_image_selector', '.wvs-archive-product-image' ) );
                $options[ 'archive_cart_button_selector' ]             = sanitize_text_field( woo_variation_swatches()->get_option( 'archive_cart_button_selector', '.wvs-add-to-cart-button' ) );
                $options[ 'archive_show_availability' ]                = wc_string_to_bool( woo_variation_swatches()->get_option( 'archive_show_availability', 'no' ) );
                $options[ 'enable_clickable_out_of_stock_archive' ]    = apply_filters( 'woo_variation_swatches_clickable_out_of_stock_archive', wc_string_to_bool( woo_variation_swatches()->get_option( 'enable_catalog_mode', 'no' ) ) );
                $options[ 'enable_catalog_mode' ]                      = wc_string_to_bool( woo_variation_swatches()->get_option( 'enable_catalog_mode', 'no' ) );
                $options[ 'disable_catalog_mode_on_single_attribute' ] = wc_string_to_bool( woo_variation_swatches()->get_option( 'disable_catalog_mode_on_single_attribute', 'no' ) );
                $options[ 'catalog_mode_behaviour' ]                   = sanitize_text_field( woo_variation_swatches()->get_option( 'catalog_mode_behaviour', 'navigate' ) );
                $options[ 'catalog_mode_trigger' ]                     = sanitize_text_field( woo_variation_swatches()->get_option( 'catalog_mode_trigger', 'click' ) );
                $options[ 'linkable_attribute' ]                       = wc_string_to_bool( woo_variation_swatches()->get_option( 'linkable_attribute', 'no' ) );
                $options[ 'enable_single_variation_preview' ]          = wc_string_to_bool( woo_variation_swatches()->get_option( 'enable_single_variation_preview', 'no' ) );
                $options[ 'enable_single_variation_preview_archive' ]  = wc_string_to_bool( woo_variation_swatches()->get_option( 'enable_single_variation_preview_archive', 'no' ) );
                $options[ 'enable_single_preloader' ]                  = wc_string_to_bool( woo_variation_swatches()->get_option( 'enable_single_preloader', 'yes' ) );
                $options[ 'enable_archive_preloader' ]                 = wc_string_to_bool( woo_variation_swatches()->get_option( 'enable_archive_preloader', 'yes' ) );
                $options[ 'enable_ajax_add_to_cart' ]                  = get_option( 'woocommerce_enable_ajax_add_to_cart', 'yes' );
                
                //$options[ 'single_variation_preview_attribute' ]      = sanitize_text_field( woo_variation_swatches()->get_option( 'single_variation_preview_attribute', '' ) );
                
                return $options;
            }
            
            public function post_class( $classes, $product ) {
                // @TODO: Variable / Variation
                if ( $product->is_type( 'variable' ) ) {
                    $classes[] = 'wvs-archive-product-wrapper';
                }
                
                return $classes;
            }
            
            public function get_variation_threshold_min( $product ) {
                return absint( apply_filters( 'woo_variation_swatches_ajax_variation_threshold_min', 0, $product ) );
            }
            
            public function after_shop_loop_item() {
                global $product;
                $this->display_swatches( $product );
            }
            
            public function display_swatches( $product ) {
                
                // Disable Archive Product
                if ( apply_filters( 'disable_woo_variation_swatches_archive_product', false, $product, $this ) ) {
                    return;
                }
                
                $enable = wc_string_to_bool( woo_variation_swatches()->get_option( 'show_on_archive', 'yes' ) );
                
                if ( ! is_object( $product ) ) {
                    $product = wc_get_product( $product );
                }
                
                if ( $product->is_type( 'variable' ) && $enable ) {
                    
                    // Enqueue variation scripts.
                    // wp_enqueue_script( 'wc-add-to-cart-variation' );
                    wp_enqueue_script( 'woo-variation-swatches-pro' );
                    
                    // Get Available variations?
                    $variation_threshold_min = $this->get_variation_threshold_min( $product );
                    $variation_threshold_max = $this->get_variation_threshold_max( $product );
                    $total_children          = count( $product->get_children() );
                    $get_variations          = $total_children <= absint( $variation_threshold_min );
                    
                    woo_variation_swatches()->get_template( 'variable.php', array(
                        // 'archive' => $this ,
                        'available_variations'    => $get_variations ? $this->get_available_variations( $product ) : false,
                        'attributes'              => $product->get_variation_attributes(),
                        'selected_attributes'     => $product->get_default_attributes(),
                        'variation_threshold_min' => absint( $variation_threshold_min ),
                        'variation_threshold_max' => absint( $variation_threshold_max ),
                        'total_children'          => absint( $total_children ),
                    ) );
                }
            }
            
            public function availability_html( $product ) {
                
                $availability = $product->get_availability();
                if ( ! empty( $availability[ 'availability' ] ) ) {
                    return sprintf( '<div class="stock %s">%s</div>', $availability[ 'class' ], $availability[ 'availability' ] );
                }
            }
            
            public function get_available_variation( $variation, $product ) {
                if ( is_numeric( $variation ) ) {
                    $variation = wc_get_product( $variation );
                }
                if ( ! $variation instanceof WC_Product_Variation ) {
                    return false;
                }
                
                $available_variation = array(
                    'attributes'              => $variation->get_variation_attributes(),
                    'availability_html'       => $this->availability_html( $variation ),
                    'image'                   => $this->get_product_thumbnail_image( $variation->get_image_id(), $variation, true ),
                    'image_id'                => $variation->get_image_id(),
                    'is_in_stock'             => $variation->is_in_stock(),
                    'is_purchasable'          => $variation->is_purchasable(),
                    'max_qty'                 => 0 < $variation->get_max_purchase_quantity() ? $variation->get_max_purchase_quantity() : '',
                    'min_qty'                 => $variation->get_min_purchase_quantity(),
                    //'price_html'           => '<span class="price">' . $variation->get_price_html() . '</span>',
                    'price_html'              => $variation->get_price_html(),
                    'variation_id'            => $variation->get_id(),
                    'product_id'              => $product->get_id(),
                    'variation_is_active'     => $variation->variation_is_active(),
                    'variation_is_visible'    => $variation->variation_is_visible(),
                    'add_to_cart_text'        => apply_filters( 'woo_variation_swatches_archive_add_to_cart_text', $variation->add_to_cart_text(), $variation, $product ),
                    'add_to_cart_url'         => $variation->add_to_cart_url(),
                    'add_to_cart_description' => $variation->add_to_cart_description(),
                    //'add_to_cart_ajax_class'  => $variation->supports( 'ajax_add_to_cart' ) && $variation->is_purchasable() && $variation->is_in_stock() ? 'ajax_add_to_cart' : '',
                );
                
                return apply_filters( 'woo_variation_swatches_get_available_variation', $available_variation, $variation, $product, $this );
            }
            
            public function get_available_preview_variation( $variation, $product ) {
                if ( is_numeric( $variation ) ) {
                    $variation = wc_get_product( $variation );
                }
                if ( ! $variation instanceof WC_Product_Variation ) {
                    return false;
                }
                
                $available_variation = array(
                    'attributes' => $variation->get_variation_attributes(),
                    'image'      => wc_get_product_attachment_props( $variation->get_image_id(), $variation ),
                    'image_id'   => $variation->get_image_id(),
                );
                
                return apply_filters( 'woo_variation_swatches_get_available_preview_variation', $available_variation, $product, $variation, $this );
            }
            
            public function get_available_variations( $product ) {
                
                $variation_ids        = $product->get_children();
                $available_variations = array();
                
                if ( is_callable( '_prime_post_caches' ) ) {
                    _prime_post_caches( $variation_ids );
                }
                
                foreach ( $variation_ids as $variation_id ) {
                    
                    $variation = wc_get_product( $variation_id );
                    
                    // Hide out of stock variations if 'Hide out of stock items from the catalog' is checked.
                    if ( ! $variation || ! $variation->exists() || ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) && ! $variation->is_in_stock() ) ) {
                        continue;
                    }
                    
                    // Filter 'woocommerce_hide_invisible_variations' to optionally hide invisible variations (disabled variations and variations with empty price).
                    if ( apply_filters( 'woocommerce_hide_invisible_variations', true, $product->get_id(), $variation ) && ! $variation->variation_is_visible() ) {
                        continue;
                    }
                    
                    $available_variations[] = $this->get_available_variation( $variation, $product );
                }
                
                return array_values( array_filter( $available_variations ) );
            }
            
            public function archive_product_dropdown( $html, $args ) {
                
                if ( apply_filters( 'default_woo_variation_swatches_archive_product_dropdown_html', false, $args, $html, $this ) ) {
                    return $html;
                }
                
                // Get default selected value. NOT by URL Variable
                if ( $args[ 'attribute' ] && $args[ 'product' ] instanceof WC_Product ) {
                    $selected_key = wc_variation_attribute_name( $args[ 'attribute' ] );
                    // phpcs:disable WordPress.Security.NonceVerification.Recommended
                    // $args[ 'selected' ] = isset( $_REQUEST[ $selected_key ] ) ? wc_clean( wp_unslash( $_REQUEST[ $selected_key ] ) ) : $args[ 'product' ]->get_variation_default_attribute( $args[ 'attribute' ] );
                    // $args[ 'selected' ] = isset( $_REQUEST[ $selected_key ] ) ? wc_clean( rawurldecode( wp_unslash( $_REQUEST[ $selected_key ] ) ) ) : $args[ 'product' ]->get_variation_default_attribute( $args[ 'attribute' ] );
                    $args[ 'selected' ] = $args[ 'product' ]->get_variation_default_attribute( $args[ 'attribute' ] );
                    // phpcs:enable WordPress.Security.NonceVerification.Recommended
                }
                
                if ( ! wc_string_to_bool( woo_variation_swatches()->get_option( 'archive_default_selected', 'yes' ) ) ) {
                    $args[ 'selected' ] = false;
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
                    $attributes = $product->get_variation_attributes();
                    $options    = $attributes[ $attribute ];
                }
                
                // Default Convert to button
                $global_convert_to_button   = wc_string_to_bool( woo_variation_swatches()->get_option( 'default_to_button', 'yes' ) );
                $global_convert_to_image    = wc_string_to_bool( woo_variation_swatches()->get_option( 'default_to_image', 'yes' ) );
                $enable_catalog_mode        = wc_string_to_bool( woo_variation_swatches()->get_option( 'enable_catalog_mode', 'no' ) );
                $catalog_mode_display_limit = absint( woo_variation_swatches()->get_option( 'catalog_mode_display_limit', 0 ) );
                $catalog_mode_behaviour     = sanitize_text_field( woo_variation_swatches()->get_option( 'catalog_mode_behaviour', 'navigate' ) );
                $get_attribute              = woo_variation_swatches()->get_frontend()->get_attribute_taxonomy_by_name( $attribute );
                $attribute_types            = array_keys( woo_variation_swatches()->get_backend()->extended_attribute_types() );
                $global_attribute_type      = ( $get_attribute ) ? $get_attribute->attribute_type : 'select';
                $swatches_data              = array();
                
                // Product Settings
                $product_default_to_button = woo_variation_swatches()->get_product_settings( $product, 'default_to_button' );
                $product_convert_to_image  = woo_variation_swatches()->get_product_settings( $product, 'default_to_image' );
                $product_attribute_type    = woo_variation_swatches()->get_product_settings( $product, $attribute, 'type' );
                
                // Actual Settings
                $default_to_button = empty( $product_default_to_button ) ? $global_convert_to_button : $product_default_to_button;
                $convert_to_image  = empty( $product_convert_to_image ) ? $global_convert_to_image : $product_convert_to_image;
                $attribute_type    = empty( $product_attribute_type ) ? $global_attribute_type : $product_attribute_type;
                
                if ( ! in_array( $attribute_type, $attribute_types ) ) {
                    return $html;
                }
                
                $select_inline_style = '';
                
                $variation_data = array();
                if ( $convert_to_image && $attribute_type === 'select' ) {
                    $attributes      = $product->get_variation_attributes();
                    $first_attribute = array_key_first( $attributes );
                    
                    if ( $first_attribute === $attribute ) {
                        $available_variations = $this->get_available_variation_images( $product );
                        $variation_data       = $this->get_variation_data_by_attribute_name( $available_variations, $first_attribute );
                        $attribute_type       = empty( $variation_data ) ? 'button' : 'mixed';
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
                                $swatches_data[] = $this->get_swatch_data( $args, $term );
                                
                                $html .= '<option value="' . esc_attr( $term->slug ) . '" ' . selected( sanitize_title( $args[ 'selected' ] ), $term->slug, false ) . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $term->name, $term, $attribute, $product ) ) . '</option>';
                            }
                        }
                    } else {
                        foreach ( $options as $option ) {
                            // This handles < 2.4.0 bw compatibility where text attributes were not sanitized.
                            $selected = sanitize_title( $args[ 'selected' ] ) === $args[ 'selected' ] ? selected( $args[ 'selected' ], sanitize_title( $option ), false ) : selected( $args[ 'selected' ], $option, false );
                            
                            $swatches_data[] = $this->get_swatch_data( $args, $option );
                            
                            $html .= '<option value="' . esc_attr( $option ) . '" ' . $selected . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $option, null, $attribute, $product ) ) . '</option>';
                        }
                    }
                }
                
                $html .= '</select>';
                
                if ( $attribute_type === 'select' ) {
                    return $html;
                }
                
                // Start Swatches
                
                $item        = '';
                $wrapper     = '';
                $wrapper_end = '';
                
                if ( ! empty( $options ) && ! empty( $swatches_data ) && $product ) {
                    
                    $wrapper     = $this->wrapper_start( $args, $attribute, $product, $attribute_type, $options );
                    $increment   = 0;
                    $incremented = 0;
                    foreach ( $swatches_data as $data ) {
                        
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
                        
                        if ( $enable_catalog_mode && $catalog_mode_display_limit > 0 && $catalog_mode_display_limit === ( $increment + 1 ) ) {
                            $incremented = $increment;
                            if ( 'navigate' === $catalog_mode_behaviour ) {
                                $item .= $this->item_more( $product, $swatches_data, ( $increment + 1 ) );
                                break;
                            }
                        }
                        
                        $increment ++;
                    }
                    
                    if ( 'expand' === $catalog_mode_behaviour && $enable_catalog_mode && $catalog_mode_display_limit > 0 && $catalog_mode_display_limit < $increment ) {
                        $item .= $this->item_more( $product, $swatches_data, ( $incremented + 1 ) );
                    }
                    
                    $wrapper_end = $this->wrapper_end();
                }
                
                // End Swatches
                $html .= $wrapper . $item . $wrapper_end;
                
                return apply_filters( 'woo_variation_swatches_html', $html, $args, $swatches_data, $this );
            }
        }
    }

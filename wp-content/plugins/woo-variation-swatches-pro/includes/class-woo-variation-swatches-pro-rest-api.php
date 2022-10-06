<?php
    defined( 'ABSPATH' ) || exit;
    
    if ( ! class_exists( 'Woo_Variation_Swatches_Pro_REST_API' ) ) {
        class Woo_Variation_Swatches_Pro_REST_API {
            
            protected static $_instance = null;
            
            protected $namespace                 = 'woo-variation-swatches/v1';
            protected $archive_product_rest_base = '/archive-product/(?P<product_id>[\d]+)';
            protected $single_product_rest_base  = '/single-product/(?P<product_id>[\d]+)';
            
            protected function __construct() {
                $this->includes();
                $this->hooks();
                $this->init();
                
                do_action( 'woo_variation_swatches_data_api_loaded', $this );
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
                // /wp-json/woo-variation-swatches/v1/single-product/PRODUCT_ID
                add_action( 'rest_api_init', array( $this, 'register_archive_product_rest_route' ) );
                add_action( 'rest_api_init', array( $this, 'register_single_product_rest_route' ) );
                
                add_filter( 'wp_rest_cache/allowed_endpoints', array( $this, 'rest_cache_allowed_endpoints' ) );
                
                add_filter( 'litespeed_const_DONOTCACHEPAGE', '__return_false' );
                add_action( 'litespeed_load_thirdparty', array( $this, 'litespeed_cache' ) );
            }
            
            protected function init() {
            
            }
            
            public function get_args_params() {
                $params                 = array();
                $params[ 'product_id' ] = array(
                    'description'       => esc_html__( 'Product ID.', 'woocommerce' ),
                    'type'              => 'integer',
                    'sanitize_callback' => 'absint',
                    'validate_callback' => array( $this, 'validate_request_arg' ),
                );
                
                return $params;
            }
            
            public function validate_request_arg( $param, $request, $key ) {
                return is_numeric( $param );
            }
            
            public function rest_cache_allowed_endpoints( $allowed_endpoints ) {
                
                if ( ! isset( $allowed_endpoints[ 'woo-variation-swatches/v1' ] ) ) {
                    $allowed_endpoints[ 'woo-variation-swatches/v1' ][] = 'archive-product';
                    $allowed_endpoints[ 'woo-variation-swatches/v1' ][] = 'single-product';
                }
                
                return $allowed_endpoints;
            }
            
            public function litespeed_cache() {
                add_action( 'litespeed_control_finalize', function () {
                    
                    if ( ! apply_filters( 'woo_variation_swatches_litespeed_cache_control', true ) ) {
                        return;
                    }
                    
                    if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
                        do_action( 'litespeed_control_set_cacheable', 'Woo Variation Swatches - REST API Set Cache' );
                        do_action( 'litespeed_control_force_cacheable', 'Woo Variation Swatches - REST API Force Cache' );
                    }
                },          20 );
            }
            
            public function enable_cache_constant( $status = true ) {
                wc_maybe_define_constant( 'DONOTCACHEPAGE', ! $status );
                wc_maybe_define_constant( 'DONOTCACHEOBJECT', ! $status );
                wc_maybe_define_constant( 'DONOTCACHEDB', ! $status );
            }
            
            public function rest_header( $object ) {
                return apply_filters( 'woo_variation_swatches_rest_api_headers', array(
                    'Expires'                     => gmdate( 'D, d M Y H:i:s \G\M\T', time() + HOUR_IN_SECONDS ),
                    'Cache-Control'               => sprintf( 'public, max-age=%d', HOUR_IN_SECONDS ),
                    'X-Variation-Swatches-Header' => true
                ),                    $object );
            }
            
            public function register_archive_product_rest_route() {
                register_rest_route( $this->namespace, $this->archive_product_rest_base, array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'archive_product_variations_for_response' ),
                    'permission_callback' => '__return_true',
                    'args'                => $this->get_args_params(),
                ) );
            }
            
            public function register_single_product_rest_route() {
                register_rest_route( $this->namespace, $this->single_product_rest_base, array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'single_product_variations_for_response' ),
                    'permission_callback' => '__return_true',
                    'args'                => $this->get_args_params(),
                ) );
            }
            
            public function single_product_variations_for_response( WP_REST_Request $request ) {
                
                $product_id = absint( $request->get_param( 'product_id' ) );
                
                if ( WP_REST_Server::READABLE !== $request->get_method() ) {
                    return new WP_Error( 'readble_only', 'We process only READABLE request', array( 'status' => 403 ) );
                }
                
                $product = wc_get_product( $product_id );
                
                if ( ! $product ) {
                    return new WP_REST_Response( array(
                                                     'message' => 'Product was not found',
                                                 ), 404 );
                }
                
                if ( ! $product->is_type( 'variable' ) ) {
                    return new WP_REST_Response( array(
                                                     'message' => 'Only variable product is allowed',
                                                 ), 403 );
                }
                
                // Object cache single product
                $cache_name = sprintf( 'wvs_rest_single_product_%s', $product->get_id() );
                $cache      = new Woo_Variation_Swatches_Cache( $cache_name, 'wvs_rest_single_product' );
                
                if ( false === ( $response_objects = $cache->get_cache( $cache_name ) ) ) {
                    $response_objects = $product->get_available_variations();
                    $cache->set_cache( $response_objects, $cache_name );
                }
                
                $this->enable_cache_constant();
                $headers  = $this->rest_header( $product );
                $response = rest_ensure_response( $response_objects );
                $response->set_headers( $headers );
                
                return $response;
            }
            
            public function archive_product_variations_for_response( WP_REST_Request $request ) {
                
                $product_id = absint( $request->get_param( 'product_id' ) );
                
                if ( WP_REST_Server::READABLE !== $request->get_method() ) {
                    return new WP_Error( 'readble_only', 'We process only READABLE request', array( 'status' => 403 ) );
                }
                
                $product = wc_get_product( $product_id );
                
                if ( ! $product ) {
                    return new WP_REST_Response( array(
                                                     'message' => 'Product was not found',
                                                 ), 404 );
                }
                
                if ( ! $product->is_type( 'variable' ) ) {
                    return new WP_REST_Response( array(
                                                     'message' => 'Only variable product is allowed',
                                                 ), 403 );
                }
                
                
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
                
                
                // Object cache archive product
                $cache_name = sprintf( 'wvs_rest_archive_product_%s', $product->get_id() );
                $cache      = new Woo_Variation_Swatches_Cache( $cache_name, 'wvs_rest_archive_product' );
                
                if ( false === ( $response_objects = $cache->get_cache( $cache_name ) ) ) {
                    $response_objects = array_values( array_filter( $available_variations ) );
                    $cache->set_cache( $response_objects, $cache_name );
                }
                
                $this->enable_cache_constant();
                $headers  = $this->rest_header( $product );
                $response = rest_ensure_response( $response_objects );
                $response->set_headers( $headers );
                
                return $response;
            }
            
            public function availability_html( $product ) {
                
                $availability = $product->get_availability();
                if ( ! empty( $availability[ 'availability' ] ) ) {
                    return sprintf( '<div class="stock %s">%s</div>', $availability[ 'class' ], $availability[ 'availability' ] );
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
        }
    }
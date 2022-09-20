<?php
    
    defined( 'ABSPATH' ) or die( 'Keep Silent' );
    
    if ( ! class_exists( 'GetWooPlugins_Plugin_Updater', false ) ):
        abstract class GetWooPlugins_Plugin_Updater {
            
            public $plugin_id;
            public $plugin_slug;
            public $slug;
            public $product_id;
            public $license_key;
            public $plugin_data;
            public $args = array();
            private $current_version;
            private $tested;
            private $requires_php;
            // private $plugin_homepage = 'https://getwooplugins.com/plugins/woocommerce-variation-swatches/';
            // private $free_plugin_slug = 'woo-variation-swatches';
            
            
            public function __construct( $plugin_file, $product_id, $license_key = '', $args = array() ) {
                
                if ( current_user_can( 'update_plugins' ) && ! function_exists( 'get_plugin_data' ) ) {
                    require_once ABSPATH . 'wp-admin/includes/plugin.php';
                }
                
                $this->plugin_id   = trim( str_ireplace( array(
                                                             'http://',
                                                             'https://'
                                                         ), '', $this->get_plugin_homepage() ), '/' );
                $this->plugin_slug = plugin_basename( $plugin_file );
                $this->license_key = $license_key;
                $this->product_id  = $product_id;
                $this->args        = wp_parse_args( $args, array(
                    'domain' => strtolower( $_SERVER['HTTP_HOST'] ),
                    // 'theme'  => basename( get_template_directory() )
                ) );
                
                
                add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_update' ), 22 );
                // add_filter( 'pre_set_transient_update_plugins', array( $this, 'check_update' ) );
                add_filter( 'plugins_api', array( $this, 'plugin_information' ), 10, 3 );
                add_action( "in_plugin_update_message-{$this->plugin_slug}", array( $this, "update_message" ), 10, 2 );
                
                add_filter( 'extra_plugin_headers', array( $this, 'add_plugin_headers' ) );
                add_filter( 'plugin_row_meta', array( $this, 'check_for_update_link' ), 10, 2 );
                
                add_action( 'admin_action_gwp_check_update', array( $this, 'force_update_check' ) );
                
                
                $this->plugin_data = get_plugin_data( $plugin_file );
                
                $this->requires_php    = trim( $this->plugin_data['RequiresPHP'] );
                $this->current_version = trim( $this->plugin_data['Version'] );
                $this->tested          = trim( $this->plugin_data['Tested up to'] );
                
                // $this->slug            = basename( dirname( $plugin_file ) );
                $this->slug = basename( $plugin_file, '.php' );
            }
            
            private function get_update_path() {
                return 'https://getwooplugins.com/wp-json/getwooplugins/v1/check-update';
            }
            
            // Must Override this method to perform
            abstract public function get_plugin_homepage();
            
            // Must Override this method to perform
            abstract public function get_org_plugin_slug();
            
            public function force_update_check() {
                if ( current_user_can( 'update_plugins' ) ) {
                    if ( ! function_exists( 'wp_clean_plugins_cache' ) ) {
                        require_once ABSPATH . 'wp-admin/includes/plugin.php';
                    }
                    
                    wp_clean_plugins_cache();
                    
                    wp_safe_redirect( admin_url( 'plugins.php' ) );
                }
            }
            
            public function check_for_update_link( $links, $file ) {
                
                if ( $file == $this->plugin_slug && current_user_can( 'update_plugins' ) ) {
                    
                    $update_check_url = esc_url( add_query_arg( array( 'action' => 'gwp_check_update' ), admin_url( 'plugins.php' ) ) );
                    
                    $row_meta['gwp_check_update'] = '<a href="' . $update_check_url . '" title="' . esc_attr( esc_html__( 'Check Update', 'woo-variation-swatches-pro' ) ) . '">' . esc_html__( 'Check Update', 'woo-variation-swatches-pro' ) . '</a>';
                    
                    return array_merge( $links, $row_meta );
                }
                
                return (array) $links;
            }
            
            public function add_plugin_headers( $headers ) {
                return array_merge( $headers, array( 'Tested up to' ) );
            }
            
            public function check_update( $transient ) {
                
                // global $pagenow;
                
                if ( ! is_object( $transient ) ) {
                    $transient = new stdClass;
                }
                
                /*
                if ( 'plugins.php' == $pagenow && is_multisite() ) {
                    return $transient;
                }*/
                
                /*if ( empty( $transient->checked ) ) {
                    return $transient;
                }*/
                
                $remote_version = $this->get_version_info();
                
                $item = array(
                    'id'            => $this->plugin_id,
                    'slug'          => $this->slug,
                    'plugin'        => $this->plugin_slug,
                    'new_version'   => $this->current_version,
                    'url'           => $this->get_plugin_homepage(),
                    'icons'         => array(
                        '2x' => sprintf( 'https://ps.w.org/%s/assets/icon-256x256.png', $this->get_org_plugin_slug() ),
                        '1x' => sprintf( 'https://ps.w.org/%s/assets/icon-128x128.png', $this->get_org_plugin_slug() ),
                        // 'svg' => sprintf( 'https://ps.w.org/%s/assets/icon.svg', $this->get_org_plugin_slug() ),
                    ),
                    'banners'       => array(
                        '2x' => sprintf( 'https://ps.w.org/%s/assets/banner-1544x500.png', $this->get_org_plugin_slug() ),
                        '1x' => sprintf( 'https://ps.w.org/%s/assets/banner-772x250.png', $this->get_org_plugin_slug() ),
                    ),
                    'banners_rtl'   => array(),
                    'tested'        => $this->tested,
                    'requires_php'  => $this->requires_php,
                    'compatibility' => new stdClass(),
                );
                
                if ( $remote_version ) {
                    
                    if ( isset( $remote_version->package ) ) {
                        $item['package'] = $remote_version->package;
                    }
                    
                    // WP Tested
                    if ( isset( $remote_version->tested ) ) {
                        $item['tested'] = $remote_version->tested;
                    }
                    // Upgrade Notice
                    if ( isset( $remote_version->upgrade_notice ) ) {
                        $item['upgrade_notice'] = $remote_version->upgrade_notice;
                    }
                    // Latest version number
                    if ( isset( $remote_version->new_version ) && version_compare( $this->current_version, $remote_version->new_version, '<' ) ) {
                        $item['new_version']                       = $remote_version->new_version;
                        $transient->response[ $this->plugin_slug ] = (object) $item;
                        unset( $transient->no_update[ $this->plugin_slug ] );
                    } else {
                        // Populating the no_update information is required to support auto-updates in WordPress 5.5.
                        $transient->no_update[ $this->plugin_slug ] = (object) $item;
                        unset( $transient->response[ $this->plugin_slug ] );
                    }
                }
                
                return $transient;
            }
            
            public function clear_plugin_update_cache() {
                delete_site_transient( 'update_plugins' );
                // delete_site_transient( 'update_themes' );
            }
            
            public function get_plugin_update_cache() {
                return get_site_transient( 'update_plugins' );
                // delete_site_transient( 'update_themes' );
            }
            
            public function request_params( $action ) {
                return array(
                    'timeout' => 15,
                    'body'    => array(
                        'action'      => $action,
                        'name'        => $this->slug,
                        'type'        => 'plugins',
                        'license_key' => $this->license_key,
                        'product_id'  => $this->product_id,
                        'args'        => $this->args
                    )
                );
            }
            
            public function get_response( $request_type = 'version-info' ) {
                $params = $this->request_params( $request_type );
                
                $response = wp_remote_get( $this->get_update_path(), $params );
                
                if ( ! is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) == 200 ) {
                    return maybe_unserialize( json_decode( wp_remote_retrieve_body( $response ) ) );
                }
                
                return false;
            }
            
            public function get_version_info() {
                return $this->get_response( 'version-info' );
            }
            
            public function get_details_info() {
                return $this->get_response( 'details-info' );
            }
            
            public function plugin_information( $def, $action, $arg ) {
                
                if ( ! ( 'plugin_information' === $action ) ) {
                    return $def;
                }
                
                if ( isset( $arg->slug ) && $arg->slug == $this->slug ) {
                    $information = $this->get_details_info();
                    
                    if ( $information && is_object( $information ) ) {
                        $information->name    = trim( $this->plugin_data['Name'] );
                        $information->slug    = $this->slug;
                        $information->banners = array(
                            'high' => sprintf( 'https://ps.w.org/%s/assets/banner-1544x500.png', $this->get_org_plugin_slug() ),
                            'low'  => sprintf( 'https://ps.w.org/%s/assets/banner-772x250.png', $this->get_org_plugin_slug() ),
                        );
                        
                        $information->author   = '<a href="https://getwooplugins.com/">GetWooPlugins.com</a>';
                        $information->homepage = $this->get_plugin_homepage();
                    }
                    
                    return $information;
                }
                
                return $def;
            }
            
            public function update_message( $plugin_data, $response ) {
                
                
                if ( empty( $this->license_key ) ) {
                    printf( ' <strong><em>%s</em></strong>', esc_html__( 'Please add license key to get automatic updates.', 'woo-variation-swatches-pro' ) );
                }
                
                if ( isset( $plugin_data['upgrade_notice'] ) && ! empty( $plugin_data['upgrade_notice'] ) ) {
                    echo ' ' . wp_kses_post( $plugin_data['upgrade_notice'] );
                }
            }
        }
    endif;
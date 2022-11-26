<?php
    
    defined( 'ABSPATH' ) || exit;
    
    class Woo_Variation_Swatches_Pro_Migration {
        
        protected static $_instance = null;
        
        private $_migration_functions = array(
            
            '2.0.0' => array(
                'woo_variation_swatches_pro_migrate_200_product_attributes',
                'woo_variation_swatches_pro_migrate_200_variable_products_swatches_settings',
                'woo_variation_swatches_pro_migrate_200_global_settings',
            ),
            '2.0.2' => array(
                'woo_variation_swatches_pro_migrate_202_group_slugs',
            )
        );
        
        public static function instance() {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }
            
            return self::$_instance;
        }
        
        protected function __construct() {
            $this->includes();
            $this->hooks();
        }
        
        public function includes() {
            include_once dirname( __FILE__ ) . '/migration-functions.php';
        }
        
        protected function hooks() {
            add_filter( 'woocommerce_debug_tools', array( $this, 'add_to_debug_tool' ) );
            add_action( 'admin_init', array( $this, 'init' ) );
            add_action( 'admin_init', array( $this, 'migrate_notice' ) );
            add_action( 'woo_variation_swatches_pro_run_migration', array( $this, 'run_migration' ) );
            add_action( 'woo_variation_swatches_pro_update_to_current_version', array( $this, 'update_version' ) );
        }
        
        public function add_to_debug_tool( $tools = array() ) {
            $tools[ 'woo_variation_swatches_run_migrator' ] = array(
                'name'     => esc_html__( '"Variation Swatches for WooCommerce" Migrator', 'woo-variation-swatches-pro' ),
                'button'   => esc_html__( 'Run Migration', 'woo-variation-swatches-pro' ),
                'desc'     => esc_html__( 'This will migrate from old version to new version of "Variation Swatches for WooCommerce".', 'woo-variation-swatches-pro' ),
                'callback' => array( $this, 'rerun_migration' )
            );
            
            return $tools;
        }
        
        public function rerun_migration() {
            delete_option( 'woo_variation_swatches_pro_version' );
            
            return esc_html__( 'Variation Swatches for WooCommerce migration has been re scheduled to run in the background.', 'woo-variation-swatches-pro' );
        }
        
        public function get_migration_callbacks() {
            return $this->_migration_functions;
        }
        
        // start
        public function needs_migration() {
            $current_version = get_option( 'woo_variation_swatches_pro_version', '1.1.18' );
            $updates         = $this->get_migration_callbacks();
            $update_versions = array_keys( $updates );
            usort( $update_versions, 'version_compare' );
            
            return ! is_null( $current_version ) && version_compare( $current_version, end( $update_versions ), '<' );
        }
        
        public function update_version( $version ) {
            update_option( 'woo_variation_swatches_pro_version', $version );
        }
        
        public function init() {
            
            
            // delete_option( 'woo_variation_swatches_pro_version' );
            // return false;
            
            if ( ! $this->needs_migration() ) {
                return false;
            }
            
            $current_version = get_option( 'woo_variation_swatches_pro_version', '1.1.18' );
            $latest_version  = woo_variation_swatches()->pro_version();
            
            $loop = 1;
            
            // 1. Updating current version
            if ( version_compare( $current_version, $latest_version, '<' ) ) {
                
                $callback = array( 'version' => $latest_version );
                
                $next = WC()->queue()->get_next( 'woo_variation_swatches_pro_update_to_current_version', $callback, 'woo-variation-swatches-pro-migration' );
                if ( ! $next ) {
                    WC()->queue()->cancel_all( 'woo_variation_swatches_pro_update_to_current_version', $callback, 'woo-variation-swatches-pro-migration' );
                    WC()->queue()->schedule_single( time(), 'woo_variation_swatches_pro_update_to_current_version', $callback, 'woo-variation-swatches-pro-migration' );
                }
            }
            
            // 2. Run Migrator
            foreach ( $this->get_migration_callbacks() as $version => $migration_callbacks ) {
                if ( version_compare( $current_version, $version, '<' ) ) {
                    foreach ( $migration_callbacks as $migration_callback ) {
                        
                        $callback = array( 'callback' => $migration_callback );
                        
                        $next = WC()->queue()->get_next( 'woo_variation_swatches_pro_run_migration', $callback, 'woo-variation-swatches-pro-migration' );
                        
                        if ( ! $next ) {
                            WC()->queue()->cancel_all( 'woo_variation_swatches_pro_run_migration', $callback, 'woo-variation-swatches-pro-migration' );
                            WC()->queue()->schedule_single( time() + $loop, 'woo_variation_swatches_pro_run_migration', $callback, 'woo-variation-swatches-pro-migration' );
                        }
                        
                        $loop ++;
                    }
                }
            }
        }
        
        public function run_migration( $callback ) {
            
            if ( is_callable( $callback ) ) {
                $this->run_update_callback_start( $callback );
                $result = (bool) call_user_func( $callback );
                $this->run_update_callback_end( $callback, $result );
            }
        }
        
        public function run_update_callback_start( $callback ) {
            wc_maybe_define_constant( 'WOO_VARIATION_SWATCHES_PRO_MIGRATING', true );
        }
        
        public function run_update_callback_end( $callback, $result ) {
            if ( ! $result ) {
                WC()->queue()->add( 'woo_variation_swatches_pro_run_migration', array(
                    'callback' => $callback,
                ),                  'woo-variation-swatches-pro-migration' );
            }
        }
        
        public function is_running() {
            $updates_pending = WC()->queue()->search( array(
                                                          // 'hook'     => 'woo_variation_swatches_pro_run_update',
                                                          'status'   => 'pending',
                                                          'group'    => 'woo-variation-swatches-pro-migration',
                                                          'per_page' => 1,
                                                      ) );
            
            return (bool) count( $updates_pending );
        }
        
        public function notice() {
            ob_start();
            include dirname( __FILE__ ) . '/html-notice-updating.php';
            
            return ob_get_clean();
        }
        
        public function migrate_notice() {
            if ( $this->is_running() ) {
                WC_Admin_Notices::add_custom_notice( 'woo_variation_swatches_pro_update', $this->notice() );
            } else {
                WC_Admin_Notices::remove_notice( 'woo_variation_swatches_pro_update' );
            }
        }
    }

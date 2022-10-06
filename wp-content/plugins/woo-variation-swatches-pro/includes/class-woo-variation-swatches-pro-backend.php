<?php
    defined( 'ABSPATH' ) || exit;
    
    if ( ! class_exists( 'Woo_Variation_Swatches_Pro_Backend' ) ) {
        class Woo_Variation_Swatches_Pro_Backend extends Woo_Variation_Swatches_Backend {
            
            protected static $_instance = null;
            protected        $group_instance;
            protected        $edit_panel_instance;
            
            protected function __construct() {
                parent::__construct();
            }
            
            public static function instance() {
                if ( is_null( self::$_instance ) ) {
                    self::$_instance = new self();
                }
                
                return self::$_instance;
            }
            
            protected function hooks() {
                parent::hooks();
                add_action( 'admin_init', array( $this, 'updater_init' ) );
                add_filter( 'plugin_action_links_' . plugin_basename( WOO_VARIATION_SWATCHES_PRO_PLUGIN_FILE ), array( $this, 'pro_plugin_action_links' ) );
                add_filter( 'plugin_row_meta', array( $this, 'pro_plugin_row_meta' ), 10, 2 );
            }
            
            // start
            
            public function includes() {
                parent::includes();
                require_once dirname( __FILE__ ) . '/migration/class-woo-variation-swatches-pro-migration.php';
                require_once dirname( __FILE__ ) . '/class-woo-variation-swatches-pro-updater.php';
                require_once dirname( __FILE__ ) . '/class-woo-variation-swatches-pro-group.php';
                require_once dirname( __FILE__ ) . '/class-woo-variation-swatches-pro-product-edit-panel.php';
            }
            
            protected function init() {
                parent::init();
                $this->get_migrator();
                $this->get_group();
                $this->get_edit_panel();
            }
            
            public function get_migrator() {
                return Woo_Variation_Swatches_Pro_Migration::instance();
            }
            
            public function get_group() {
                return Woo_Variation_Swatches_Pro_Group::instance();
            }
            
            public function get_edit_panel() {
                return Woo_Variation_Swatches_Pro_Product_Edit_Panel::instance();
            }
            
            public function updater_init() {
                Woo_Variation_Swatches_Pro_Updater::instance();
            }
            
            public function pro_plugin_row_meta( $links, $file ) {
                if ( plugin_basename( WOO_VARIATION_SWATCHES_PRO_PLUGIN_FILE ) !== $file ) {
                    return $links;
                }
                
                $row_meta = apply_filters( 'woo_variation_swatches_pro_row_meta', array(
                    'docs'    => '<a target="_blank" href="' . esc_url( 'https://getwooplugins.com/documentation/woocommerce-variation-swatches/' ) . '" aria-label="' . esc_attr__( 'View documentation', 'woo-variation-swatches-pro' ) . '">' . esc_html__( 'Documentation', 'woo-variation-swatches-pro' ) . '</a>',
                    'videos'  => '<a target="_blank" href="' . esc_url( 'https://www.youtube.com/channel/UC6F21JXiLUPO7sm-AYlA3Ig/videos' ) . '" aria-label="' . esc_attr__( 'Video Tutorials', 'woo-variation-swatches-pro' ) . '">' . esc_html__( 'Video Tutorials', 'woo-variation-swatches-pro' ) . '</a>',
                    'support' => '<a target="_blank" href="' . esc_url( 'https://getwooplugins.com/tickets/' ) . '" aria-label="' . esc_attr__( 'Help & Support', 'woo-variation-swatches-pro' ) . '">' . esc_html__( 'Help & Support', 'woo-variation-swatches-pro' ) . '</a>',
                ) );
                
                return array_merge( $links, $row_meta );
            }
            
            public function pro_plugin_action_links( $links ) {
                $action_links = array(
                    'settings' => '<a href="' . esc_url( $this->get_admin_menu()->get_settings_link( 'woo_variation_swatches' ) ) . '" aria-label="' . esc_attr__( 'View Swatches settings', 'woo-variation-swatches-pro' ) . '">' . esc_html__( 'Settings', 'woo-variation-swatches-pro' ) . '</a>',
                );
                
                
                $pro_links = array(
                    'wvs-go-pro-action-link' => '<a target="_blank" href="https://getwooplugins.com/plugins/woocommerce-variation-swatches/" aria-label="' . esc_attr__( 'Go Pro', 'woo-variation-swatches-pro' ) . '">' . esc_html__( 'Go Pro', 'woo-variation-swatches-pro' ) . '</a>',
                );
                
                if ( woo_variation_swatches()->is_pro() ) {
                    $pro_links = array();
                }
                
                return array_merge( $action_links, $links, $pro_links );
                
                
            }
            
            public function load_settings() {
                
                include_once woo_variation_swatches()->include_path() . '/class-woo-variation-swatches-settings.php';
                include_once dirname( __FILE__ ) . '/class-woo-variation-swatches-pro-settings.php';
                
                return new Woo_Variation_Swatches_Settings_Pro();
            }
            
            public function attribute_meta_fields() {
                
                $fields = array();
                
                $fields[ 'button' ] = array();
                
                $fields[ 'radio' ] = array();
                
                $fields[ 'color' ] = array(
                    array(
                        'label' => esc_html__( 'Color', 'woo-variation-swatches-pro' ), // <label>
                        'desc'  => esc_html__( 'Choose a color', 'woo-variation-swatches-pro' ), // description
                        'id'    => 'product_attribute_color', // name of field
                        'type'  => 'color'
                    ),
                    array(
                        'label'   => esc_html__( 'Is Dual Color', 'woo-variation-swatches-pro' ), // <label>
                        'desc'    => esc_html__( 'Make dual color', 'woo-variation-swatches-pro' ), // description
                        'id'      => 'is_dual_color', // name of field
                        'type'    => 'select2',
                        'options' => array(
                            'no'  => esc_html__( 'No', 'woo-variation-swatches-pro' ),
                            'yes' => esc_html__( 'Yes', 'woo-variation-swatches-pro' ),
                        )
                    ),
                    array(
                        'label'      => esc_html__( 'Secondary Color', 'woo-variation-swatches-pro' ), // <label>
                        'desc'       => esc_html__( 'Add another color', 'woo-variation-swatches-pro' ), // description
                        'id'         => 'secondary_color', // name of field
                        'type'       => 'color',
                        'dependency' => array(
                            array( '#is_dual_color' => array( 'type' => 'equal', 'value' => 'yes' ) )
                        )
                    )
                );
                
                $fields[ 'image' ] = array(
                    array(
                        'label' => esc_html__( 'Image', 'woo-variation-swatches-pro' ), // <label>
                        'desc'  => esc_html__( 'Choose an Image', 'woo-variation-swatches-pro' ), // description
                        'id'    => 'product_attribute_image', // name of field
                        'type'  => 'image'
                    ),
                    array(
                        'label'   => esc_html__( 'Image Size', 'woo-variation-swatches-pro' ),
                        'desc'    => esc_html__( 'Choose Image size, ( this will override global settings )', 'woo-variation-swatches-pro' ),
                        'id'      => 'image_size',
                        'type'    => 'select2',
                        'options' => array_reduce( get_intermediate_image_sizes(), function ( $carry, $item ) {
                            $carry[ $item ] = ucwords( str_ireplace( array( '-', '_' ), ' ', $item ) );
                            
                            return $carry;
                        },                         array() )
                    )
                );
                
                $all_types = array( 'color', 'image', 'button', 'radio' );
                
                $common_fields = array(
                    array(
                        'label'   => esc_html__( 'Show Tooltip', 'woo-variation-swatches-pro' ),
                        'desc'    => esc_html__( 'Individually show or hide tooltip.', 'woo-variation-swatches-pro' ),
                        'id'      => 'show_tooltip',
                        'type'    => 'select2',
                        'options' => array(
                            'text'  => esc_html__( 'Text', 'woo-variation-swatches-pro' ),
                            'image' => esc_html__( 'Image', 'woo-variation-swatches-pro' ),
                            'no'    => esc_html__( 'No', 'woo-variation-swatches-pro' ),
                        )
                    ),
                    array(
                        'label'      => esc_html__( 'Tooltip text', 'woo-variation-swatches-pro' ),
                        'desc'       => esc_html__( 'Tooltip text. Default tooltip text will be term name.', 'woo-variation-swatches-pro' ),
                        'id'         => 'tooltip_text',
                        'type'       => 'text',
                        'dependency' => array(
                            array( '#show_tooltip' => array( 'type' => 'equal', 'value' => 'text' ) )
                        )
                    ),
                    array(
                        'label'      => esc_html__( 'Tooltip image', 'woo-variation-swatches-pro' ),
                        'desc'       => esc_html__( 'Tooltip image. Default tooltip text will be term name.', 'woo-variation-swatches-pro' ),
                        'id'         => 'tooltip_image_id',
                        'type'       => 'image',
                        'dependency' => array(
                            array( '#show_tooltip' => array( 'type' => 'equal', 'value' => 'image' ) )
                        )
                    )
                );
                
                $groups = $this->get_group()->get_all();
                
                if ( ! empty( $groups ) ) {
                    
                    $options      = array();
                    $options[ 0 ] = esc_html__( '-- No Group --', 'woo-variation-swatches-pro' );
                    
                    $options = array_merge( $options, $groups );
                    ksort( $options );
                    
                    $common_fields[] = array(
                        'label'   => esc_html__( 'Group', 'woo-variation-swatches-pro' ),
                        'desc'    => esc_html__( 'Add to a group.', 'woo-variation-swatches-pro' ),
                        'id'      => 'group_name',
                        'type'    => 'select2',
                        'options' => $options
                    );
                }
                
                foreach ( $all_types as $type ) {
                    foreach ( $common_fields as $field ) {
                        array_push( $fields[ $type ], $field );
                    }
                }
                
                return $fields;
            }
    
        }
    }

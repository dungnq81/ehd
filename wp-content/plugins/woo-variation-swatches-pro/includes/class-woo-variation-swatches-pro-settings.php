<?php

    defined( 'ABSPATH' ) || exit;

    if ( ! class_exists( 'Woo_Variation_Swatches_Settings_Pro' ) ):

        class Woo_Variation_Swatches_Settings_Pro extends Woo_Variation_Swatches_Settings {

            public function __construct() {
                parent::__construct();
                $this->license_notices();
            }

            /*protected function notices() {
                parent::notices();
            }*/

            protected function hooks() {
                parent::hooks();
                add_action( 'getwooplugins_settings_action', array( $this, 'process_group' ), 10, 3 );
            }

            public function process_group( $current_tab, $current_section, $current_action ) {

                if ( $current_tab !== $this->get_id() && $current_section !== 'group' ) {
                    return;
                }

                if ( $current_action === 'new-group' && isset( $_POST[ 'woo_variation_swatches_group' ] ) ) {
                    check_admin_referer( 'woo_variation_swatches_group' );
                    $name = sanitize_text_field( $_POST[ 'woo_variation_swatches_group' ][ 'name' ] );
                    $slug = woo_variation_swatches()->sanitize_name( $_POST[ 'woo_variation_swatches_group' ][ 'slug' ] );

                    if ( empty( $slug ) ) {
                        $slug = woo_variation_swatches()->sanitize_name( $name );
                    }

                    if ( empty( $name ) ) {
                        GetWooPlugins_Admin_Settings::add_error( esc_html__( 'Group name not available.', 'woo-variation-swatches-pro' ) );
                    }

                    if ( empty( $slug ) ) {
                        GetWooPlugins_Admin_Settings::add_error( esc_html__( 'Group slug not available.', 'woo-variation-swatches-pro' ) );
                    }

                    if ( strlen( $slug ) > 28 ) {
                        GetWooPlugins_Admin_Settings::add_error( esc_html__( 'Group slug too long.', 'woo-variation-swatches-pro' ) );
                    }

                    $data          = array();
                    $data[ $slug ] = $name;

                    $saved = woo_variation_swatches()->get_backend()->get_group()->save( $slug, $data );
                    if ( $saved ) {
                        GetWooPlugins_Admin_Settings::add_message( esc_html__( 'Group Saved.', 'woo-variation-swatches-pro' ) );
                    }
                }

                if ( $current_action === 'delete-group' && isset( $_GET[ 'slug' ] ) ) {
                    check_admin_referer( 'woo_variation_swatches_group' );

                    $slug = sanitize_text_field( $_GET[ 'slug' ] );

                    if ( empty( $slug ) ) {
                        GetWooPlugins_Admin_Settings::add_error( esc_html__( 'Group slug not available.', 'woo-variation-swatches-pro' ) );

                        return;
                    }

                    $delete = woo_variation_swatches()->get_backend()->get_group()->delete( $slug );
                    if ( $delete ) {
                        GetWooPlugins_Admin_Settings::add_message( esc_html__( 'Group Deleted.', 'woo-variation-swatches-pro' ) );
                    }
                }

                if ( $current_action === 'update-group' && isset( $_GET[ 'slug' ] ) && isset( $_POST[ 'woo_variation_swatches_group' ] ) ) {
                    check_admin_referer( 'woo_variation_swatches_group' );

                    $slug = sanitize_text_field( $_GET[ 'slug' ] );
                    $name = sanitize_text_field( $_POST[ 'woo_variation_swatches_group' ][ 'name' ] );

                    $updated = woo_variation_swatches()->get_backend()->get_group()->update( $slug, $name );
                    if ( $updated ) {
                        GetWooPlugins_Admin_Settings::add_message( esc_html__( 'Group Name Update.', 'woo-variation-swatches-pro' ) );
                    }
                }
            }

            protected function get_own_sections() {

                $sections = parent::get_own_sections();

                $sections[ 'license' ][ 'url' ] = true;

                return $sections;
            }

            protected function license_notices() {
            	return;
                $license = sanitize_text_field( get_option( 'woo_variation_swatches_license' ) );
                if ( $this->is_current_tab() && empty( $license ) ) {
                    GetWooPlugins_Admin_Settings::add_notice( esc_html__( 'Add Variation Swatches for WooCommerce - Pro license key to get automatic update.', 'woo-variation-swatches-pro' ) );
                }
            }

            public function get_taxonomies( $with_attribute_name = true ) {
                // attribute_name | attribute_id
                $lists = (array) wp_list_pluck( wc_get_attribute_taxonomies(), 'attribute_label', 'attribute_name' );

                $list = array();
                foreach ( $lists as $name => $label ) {
                    if ( $with_attribute_name ) {
                        $list[ woo_variation_swatches()->sanitize_name( wc_variation_attribute_name( wc_attribute_taxonomy_name( $name ) ) ) ] = $label . " ( {$name} )";
                    } else {
                        $list[ woo_variation_swatches()->sanitize_name( wc_attribute_taxonomy_name( $name ) ) ] = $label . " ( {$name} )";
                    }
                }

                return array( '' => esc_attr__( ' - First Attribute - ', 'woo-variation-swatches-pro' ) ) + $list;
            }

            public function output( $current_tab ) {
                global $current_section;


                if ( $current_tab === $this->get_id() && 'group' === $current_section ) {
                    // print_r( $_POST); die;
                }


                parent::output( $current_tab );

            }

            public function group_section( $current_section ) {
                ob_start();
                $settings = $this->get_settings( $current_section );
                include_once dirname( __FILE__ ) . '/html-settings-group-pro.php';
                echo ob_get_clean();
            }

            protected function get_settings_for_default_section() {

                $settings = array(

                    array(
                        'id'    => 'general_options',
                        'type'  => 'title',
                        'title' => esc_html__( 'General options', 'woo-variation-swatches-pro' ),
                        'desc'  => '',
                    ),

                    array(
                        'id'      => 'enable_stylesheet',
                        'type'    => 'checkbox',
                        'title'   => esc_html__( 'Enable Stylesheet', 'woo-variation-swatches-pro' ),
                        'desc'    => esc_html__( 'Enable default stylesheet', 'woo-variation-swatches-pro' ),
                        'default' => 'yes'
                    ),

                    array(
                        'id'      => 'enable_tooltip',
                        'type'    => 'checkbox',
                        'title'   => esc_html__( 'Enable Tooltip', 'woo-variation-swatches-pro' ),
                        'desc'    => esc_html__( 'Enable tooltip on each product attribute.', 'woo-variation-swatches-pro' ),
                        'default' => 'yes',
                        'require' => $this->normalize_required_attribute( array( 'enable_stylesheet' => array( 'type' => '!empty' ) ) ),
                    ),

                    array(
                        'id'           => 'shape_style',
                        'title'        => esc_html__( 'Shape Style', 'woo-variation-swatches-pro' ),
                        'type'         => 'radio',
                        'desc'         => esc_html__( 'This controls which shape style used by default.', 'woo-variation-swatches-pro' ),
                        'desc_tip'     => true,
                        'default'      => 'squared',
                        'options'      => array(
                            'rounded' => esc_html__( 'Rounded Shape', 'woo-variation-swatches-pro' ),
                            'squared' => esc_html__( 'Squared Shape', 'woo-variation-swatches-pro' ),
                        ),
                        'help_preview' => true,
                    ),

                    array(
                        'id'           => 'default_to_button',
                        'title'        => esc_html__( 'Dropdowns to Button', 'woo-variation-swatches-pro' ),
                        'desc'         => esc_html__( 'Convert default dropdowns to button.', 'woo-variation-swatches-pro' ),
                        'default'      => 'yes',
                        'type'         => 'checkbox',
                        'help_preview' => true,
                    ),

                    array(
                        'id'           => 'default_to_image',
                        'type'         => 'checkbox',
                        'title'        => esc_html__( 'Dropdowns to Image', 'woo-variation-swatches-pro' ),
                        'desc'         => esc_html__( 'Convert default dropdowns to image type if variation has an image.', 'woo-variation-swatches-pro' ),
                        'default'      => 'yes',
                        'help_preview' => true,
                    ),

                    array(
                        'type' => 'sectionend',
                        'id'   => 'general_options',
                    ),


                );

                return $settings;
            }

            protected function get_settings_for_advanced_section() {
                $settings = array(

                    array(
                        'id'    => 'advanced_options',
                        'type'  => 'title',
                        'title' => esc_html__( 'Advanced options', 'woo-variation-swatches-pro' ),
                        'desc'  => '',
                    ),

                    array(
                        'id'           => 'clear_on_reselect',
                        'type'         => 'checkbox',
                        'title'        => esc_html__( 'Clear on Reselect', 'woo-variation-swatches-pro' ),
                        'desc'         => esc_html__( 'Clear selected attribute on select again', 'woo-variation-swatches-pro' ),
                        'default'      => 'no',
                        'help_preview' => true,
                    ),

                    array(
                        'id'           => 'hide_out_of_stock_variation',
                        'type'         => 'checkbox',
                        'title'        => esc_html__( 'Disable Out of stock', 'woo-variation-swatches-pro' ),
                        'desc'         => esc_html__( 'Disable Out Of Stock item', 'woo-variation-swatches-pro' ),
                        'default'      => 'yes',
                        'help_preview' => true,
                    ),

                    array(
                        'id'           => 'clickable_out_of_stock_variation',
                        'type'         => 'checkbox',
                        'title'        => esc_html__( 'Clickable Out Of Stock', 'woo-variation-swatches-pro' ),
                        'desc'         => esc_html__( 'Clickable Out Of Stock item', 'woo-variation-swatches-pro' ),
                        'default'      => 'no',
                        'help_preview' => true,
                        'require'      => $this->normalize_required_attribute( array( 'hide_out_of_stock_variation' => array( 'type' => 'empty' ) ) ),
                    ),

                    array(
                        'id'           => 'attribute_behavior',
                        'type'         => 'radio',
                        'title'        => esc_html__( 'Disabled Attribute style', 'woo-variation-swatches-pro' ),
                        'desc'         => esc_html__( 'Disabled / Out Of Stock attribute will be hide / blur / crossed.', 'woo-variation-swatches-pro' ),
                        'desc_tip'     => true,
                        'options'      => array(
                            'blur'          => esc_html__( 'Blur with cross', 'woo-variation-swatches-pro' ),
                            'blur-no-cross' => esc_html__( 'Blur without cross', 'woo-variation-swatches-pro' ),
                            'hide'          => esc_html__( 'Hide', 'woo-variation-swatches-pro' ),
                        ),
                        'default'      => 'blur',
                        'help_preview' => true,
                    ),

                    array(
                        'id'      => 'attribute_image_size',
                        'type'    => 'select',
                        'title'   => esc_html__( 'Attribute image size', 'woo-variation-swatches-pro' ),
                        'desc'    => has_filter( 'woo_variation_swatches_global_product_attribute_image_size' ) ? __( '<span style="color: red">Attribute image size changed by <code>woo_variation_swatches_global_product_attribute_image_size</code> hook. So this option will not apply any effect.</span>', 'woo-variation-swatches-pro' ) : __( sprintf( 'Choose attribute image size. <a target="_blank" href="%s">Media Settings</a> or use <strong>Regenerate Thumbnails</strong> plugin', esc_url( admin_url( 'options-media.php' ) ) ), 'woo-variation-swatches-pro' ),
                        'options' => $this->get_all_image_sizes(),
                        'default' => 'variation_swatches_image_size'
                    ),

                    array(
                        'id'      => 'exclude_categories',
                        'type'    => 'multiselect',
                        //'is_pro' => true,
                        // 'is_new'  => true,
                        //'help_preview' => true,
                        'options' => $this->get_product_categories(),
                        'title'   => esc_html__( 'Exclude Product Categories', 'woo-variation-swatches-pro' ),
                        'desc'    => esc_html__( 'Exclude product categories to disable variation swatches.', 'woo-variation-swatches-pro' ),
                        'default' => '',
                    ),

                    array(
                        'type' => 'sectionend',
                        'id'   => 'advanced_options',
                    ),
                );

                return $settings;
            }

            protected function get_settings_for_style_section() {

                $settings = array(

                    // Start swatches tick and cross coloring
                    array(
                        'id'    => 'style_icons_options',
                        'type'  => 'title',
                        'title' => esc_html__( 'Swatches indicator', 'woo-variation-swatches-pro' ),
                        'desc'  => esc_html__( 'Change swatches indicator color', 'woo-variation-swatches-pro' ),
                    ),

                    array(
                        'id'                => 'tick_color',
                        'type'              => 'color',
                        'title'             => esc_html__( 'Tick Color', 'woo-variation-swatches-pro' ),
                        'desc'              => esc_html__( 'Swatches Selected tick color. Default is: #ffffff', 'woo-variation-swatches-pro' ),
                        'css'               => 'width: 6em;',
                        'default'           => '#ffffff',
                        //'is_new'            => true,
                        'custom_attributes' => array(//    'data-alpha-enabled' => 'true'
                        )
                    ),

                    array(
                        'id'                => 'cross_color',
                        'type'              => 'color',
                        'title'             => esc_html__( 'Cross Color', 'woo-variation-swatches-pro' ),
                        'desc'              => esc_html__( 'Swatches cross color. Default is: #ff0000', 'woo-variation-swatches-pro' ),
                        'css'               => 'width: 6em;',
                        'default'           => '#ff0000',
                        //'is_new'            => true,
                        'custom_attributes' => array(//    'data-alpha-enabled' => 'true'
                        )
                    ),

                    array(
                        'type' => 'sectionend',
                        'id'   => 'style_icons_options',
                    ),

                    // Start single page swatches style
                    array(
                        'id'    => 'single_style_options',
                        'type'  => 'title',
                        'title' => esc_html__( 'Product Page Swatches Size', 'woo-variation-swatches-pro' ),
                        'desc'  => esc_html__( 'Change swatches style on product page', 'woo-variation-swatches-pro' ),
                    ),

                    array(
                        'id'                => 'width',
                        'type'              => 'number',
                        'title'             => esc_html__( 'Width', 'woo-variation-swatches-pro' ),
                        'desc'              => esc_html__( 'Single product variation item width. Default is: 30', 'woo-variation-swatches-pro' ),
                        'css'               => 'width: 50px;',
                        'default'           => '30',
                        'suffix'            => 'px',
                        'custom_attributes' => array(
                            'min'  => 10,
                            'max'  => 200,
                            'step' => 5,
                        ),
                    ),

                    array(
                        'id'                => 'height',
                        'type'              => 'number',
                        'title'             => esc_html__( 'Height', 'woo-variation-swatches-pro' ),
                        'desc'              => esc_html__( 'Single product variation item height. Default is: 30', 'woo-variation-swatches-pro' ),
                        'css'               => 'width: 50px;',
                        'default'           => 30,
                        'suffix'            => 'px',
                        'custom_attributes' => array(
                            'min'  => 10,
                            'max'  => 200,
                            'step' => 5,
                        ),
                    ),

                    array(
                        'id'                => 'single_font_size',
                        'type'              => 'number',
                        'title'             => esc_html__( 'Font Size', 'woo-variation-swatches-pro' ),
                        'desc'              => esc_html__( 'Single product variation item font size. Default is: 16', 'woo-variation-swatches-pro' ),
                        'css'               => 'width: 50px;',
                        'default'           => 16,
                        'suffix'            => 'px',
                        'custom_attributes' => array(
                            'min'  => 8,
                            'max'  => 48,
                            'step' => 2,
                        ),
                    ),

                    array(
                        'type' => 'sectionend',
                        'id'   => 'single_style_options',
                    ),

                    // Start archive swatches style
                    array(
                        'id'    => 'archive_style_options',
                        'type'  => 'title',
                        'title' => esc_html__( 'Archive Page Swatches Style', 'woo-variation-swatches-pro' ),
                        'desc'  => esc_html__( 'Change swatches style on archive page', 'woo-variation-swatches-pro' ),
                    ),

                    array(
                        'id'                => 'archive_width',
                        'type'              => 'number',
                        'title'             => esc_html__( 'Width', 'woo-variation-swatches-pro' ),
                        'desc'              => esc_html__( 'Archive/Shop page variation item width. Default is: 30', 'woo-variation-swatches-pro' ),
                        'css'               => 'width: 60px;',
                        'default'           => 30,
                        'suffix'            => 'px',
                        'custom_attributes' => array(
                            'min'  => 10,
                            'max'  => 200,
                            'step' => 5,
                        ),
                    ),

                    array(
                        'id'                => 'archive_height',
                        'type'              => 'number',
                        'title'             => esc_html__( 'Height', 'woo-variation-swatches-pro' ),
                        'desc'              => esc_html__( 'Archive/Shop page variation item height. Default is: 30', 'woo-variation-swatches-pro' ),
                        'css'               => 'width: 60px;',
                        'default'           => 30,
                        'suffix'            => 'px',
                        'custom_attributes' => array(
                            'min'  => 10,
                            'max'  => 200,
                            'step' => 5,
                        ),
                    ),

                    array(
                        'id'                => 'archive_font_size',
                        'type'              => 'number',
                        'title'             => esc_html__( 'Font Size', 'woo-variation-swatches-pro' ),
                        'desc'              => esc_html__( 'Archive/Shop page variation item font size. Default is: 16', 'woo-variation-swatches-pro' ),
                        'css'               => 'width: 60px;',
                        'default'           => 16,
                        'suffix'            => 'px',
                        'custom_attributes' => array(
                            'min'  => 8,
                            'max'  => 24,
                            'step' => 2,
                        ),
                    ),

                    array(
                        'type' => 'sectionend',
                        'id'   => 'archive_style_options',
                    ),


                    // Start Tooltip style
                    array(
                        'id'    => 'tooltip_style_options',
                        'type'  => 'title',
                        'title' => esc_html__( 'Tooltip Styling', 'woo-variation-swatches-pro' ),
                        'desc'  => esc_html__( 'Change tooltip styles', 'woo-variation-swatches-pro' )
                    ),

                    array(
                        'id'                => 'tooltip_background_color',
                        'type'              => 'color',
                        'title'             => esc_html__( 'Tooltip background', 'woo-variation-swatches-pro' ),
                        'desc'              => esc_html__( 'Tooltip background color. Default is: #333333', 'woo-variation-swatches-pro' ),
                        'css'               => 'width: 6em;',
                        'default'           => '#333333',
                        'custom_attributes' => array(//    'data-alpha-enabled' => 'true'
                        )
                    ),

                    array(
                        'id'      => 'tooltip_text_color',
                        'type'    => 'color',
                        'title'   => esc_html__( 'Tooltip text color', 'woo-variation-swatches-pro' ),
                        'desc'    => esc_html__( 'Tooltip text color. Default is: #FFFFFF', 'woo-variation-swatches-pro' ),
                        'css'     => 'width: 6em;',
                        'default' => '#FFFFFF',
                    ),

                    array(
                        'id'      => 'tooltip_image_size',
                        'type'    => 'select',
                        'title'   => esc_html__( 'Tooltip image size', 'woo-variation-swatches-pro' ),
                        'default' => 'variation_swatches_tooltip_size',
                        'options' => $this->get_all_image_sizes(),
                    ),

                    array(
                        'type' => 'sectionend',
                        'id'   => 'tooltip_style_options',
                    ),

                    // Start item style
                    array(
                        'id'    => 'item_style_options',
                        'type'  => 'title',
                        'title' => esc_html__( 'Variation Item Styling', 'woo-variation-swatches-pro' ),
                        'desc'  => esc_html__( 'Change variation item display style', 'woo-variation-swatches-pro' ),
                    ),

                    array(
                        'id'      => 'border_color',
                        'type'    => 'color',
                        'title'   => esc_html__( 'Border color', 'woo-variation-swatches-pro' ),
                        'desc'    => esc_html__( 'Variation item border color. Default is: #a8a8a8', 'woo-variation-swatches-pro' ),
                        'css'     => 'width: 6em;',
                        'default' => '#a8a8a8',
                    ),

                    array(
                        'id'                => 'border_size',
                        'type'              => 'number',
                        'title'             => esc_html__( 'Border size', 'woo-variation-swatches-pro' ),
                        'desc'              => esc_html__( 'Variation item border size. Default is: 1', 'woo-variation-swatches-pro' ),
                        'css'               => 'width: 60px;',
                        'default'           => 1,
                        'suffix'            => 'px',
                        'custom_attributes' => array(
                            'min'  => 1,
                            'max'  => 5,
                            'step' => 1,
                        ),
                    ),

                    array(
                        'id'      => 'background_color',
                        'type'    => 'color',
                        'title'   => esc_html__( 'Background color', 'woo-variation-swatches-pro' ),
                        'desc'    => esc_html__( 'Variation item background color. Default is: #FFFFFF', 'woo-variation-swatches-pro' ),
                        'css'     => 'width: 6em;',
                        'default' => '#FFFFFF',
                    ),

                    array(
                        'id'      => 'text_color',
                        'type'    => 'color',
                        'title'   => esc_html__( 'Text color', 'woo-variation-swatches-pro' ),
                        'desc'    => esc_html__( 'Variation item text color. Default is: #000000', 'woo-variation-swatches-pro' ),
                        'css'     => 'width: 6em;',
                        'default' => '#000000',
                    ),

                    array(
                        'type' => 'sectionend',
                        'id'   => 'item_style_options',
                    ),

                    // Start item hover style
                    array(
                        'id'    => 'item_hover_style_options',
                        'type'  => 'title',
                        'title' => esc_html__( 'Variation Item Hover Styling', 'woo-variation-swatches-pro' ),
                        'desc'  => esc_html__( 'Change variation item hover display style', 'woo-variation-swatches-pro' ),
                    ),

                    array(
                        'id'      => 'hover_border_color',
                        'type'    => 'color',
                        'title'   => esc_html__( 'Hover border color', 'woo-variation-swatches-pro' ),
                        'desc'    => esc_html__( 'Variation item hover border color. Default is: #000000', 'woo-variation-swatches-pro' ),
                        'css'     => 'width: 6em;',
                        'default' => '#000000',
                    ),

                    array(
                        'id'                => 'hover_border_size',
                        'type'              => 'number',
                        'title'             => esc_html__( 'Hover border size', 'woo-variation-swatches-pro' ),
                        'desc'              => esc_html__( 'Variation item hover border size. Default is: 3', 'woo-variation-swatches-pro' ),
                        'css'               => 'width: 60px;',
                        'default'           => 3,
                        'suffix'            => esc_html__( 'px', 'woo-variation-swatches-pro' ),
                        'custom_attributes' => array(
                            'min'  => 1,
                            'max'  => 5,
                            'step' => 1,
                        ),
                    ),

                    array(
                        'id'      => 'hover_text_color',
                        'type'    => 'color',
                        'title'   => esc_html__( 'Hover text color', 'woo-variation-swatches-pro' ),
                        'desc'    => esc_html__( 'Variation item hover text color. Default is: #000000', 'woo-variation-swatches-pro' ),
                        'css'     => 'width: 6em;',
                        'default' => '#000000',
                    ),

                    array(
                        'id'      => 'hover_background_color',
                        'type'    => 'color',
                        'title'   => esc_html__( 'Hover background color', 'woo-variation-swatches-pro' ),
                        'desc'    => esc_html__( 'Variation item hover background color. Default is: #FFFFFF', 'woo-variation-swatches-pro' ),
                        'css'     => 'width: 6em;',
                        'default' => '#FFFFFF',
                    ),

                    array(
                        'type' => 'sectionend',
                        'id'   => 'item_hover_style_options',
                    ),

                    // Start item selected style
                    array(
                        'id'    => 'item_selected_style_options',
                        'type'  => 'title',
                        'title' => esc_html__( 'Variation Item Selected Styling', 'woo-variation-swatches-pro' ),
                        'desc'  => esc_html__( 'Change variation selected item display style', 'woo-variation-swatches-pro' ),
                    ),

                    array(
                        'id'      => 'selected_border_color',
                        'type'    => 'color',
                        'title'   => esc_html__( 'Selected border color', 'woo-variation-swatches-pro' ),
                        'desc'    => esc_html__( 'Variation item selected border color. Default is: #000000', 'woo-variation-swatches-pro' ),
                        'css'     => 'width: 6em;',
                        'default' => '#000000',
                    ),

                    array(
                        'id'                => 'selected_border_size',
                        'type'              => 'number',
                        'title'             => esc_html__( 'Selected border size', 'woo-variation-swatches-pro' ),
                        'desc'              => esc_html__( 'Variation item selected border size. Default is: 2', 'woo-variation-swatches-pro' ),
                        'css'               => 'width: 60px;',
                        'default'           => 2,
                        'suffix'            => esc_html__( 'px', 'woo-variation-swatches-pro' ),
                        'custom_attributes' => array(
                            'min'  => 1,
                            'max'  => 5,
                            'step' => 1,
                        ),
                    ),

                    array(
                        'id'      => 'selected_text_color',
                        'type'    => 'color',
                        'title'   => esc_html__( 'Selected text color', 'woo-variation-swatches-pro' ),
                        'desc'    => esc_html__( 'Variation item selected text color. Default is: #000000', 'woo-variation-swatches-pro' ),
                        'css'     => 'width: 6em;',
                        'default' => '#000000',
                    ),

                    array(
                        'id'      => 'selected_background_color',
                        'type'    => 'color',
                        'title'   => esc_html__( 'Selected background color', 'woo-variation-swatches-pro' ),
                        'desc'    => esc_html__( 'Variation item selected background color. Default is: #FFFFFF', 'woo-variation-swatches-pro' ),
                        'css'     => 'width: 6em;',
                        'default' => '#FFFFFF',
                    ),

                    array(
                        'type' => 'sectionend',
                        'id'   => 'item_selected_style_options',
                    ),

                );

                return $settings;
            }

            protected function get_settings_for_single_section() {
                $settings = array(
                    array(
                        'id'    => 'single_page_options',
                        'type'  => 'title',
                        'title' => esc_html__( 'Single Product Page', 'woo-variation-swatches-pro' ),
                        'desc'  => esc_html__( 'Settings for single product page', 'woo-variation-swatches-pro' ),
                    ),

                    array(
                        'id'      => 'show_variation_label',
                        'type'    => 'checkbox',
                        'title'   => esc_html__( 'Show selected attribute', 'woo-variation-swatches-pro' ),
                        'desc'    => esc_html__( 'Show selected attribute variation name beside the title', 'woo-variation-swatches-pro' ),
                        'default' => 'yes',
                        // 'is_new'  => true,
                    ),

                    array(
                        'id'       => 'variation_label_separator',
                        'type'     => 'text',
                        'title'    => esc_html__( 'Variation label separator', 'woo-variation-swatches-pro' ),
                        'desc'     => sprintf( __( 'Variation label separator. Default: %s.', 'woo-variation-swatches-pro' ), '<code>:</code>' ),
                        'desc_tip' => true,
                        'default'  => ':',
                        'css'      => 'width: 30px;',
                        'require'  => $this->normalize_required_attribute( array( 'show_variation_label' => array( 'type' => '==', 'value' => '1' ) ) ),
                        // 'is_new'   => true,
                    ),

                    array(
                        'id'      => 'enable_single_preloader',
                        'type'    => 'checkbox',
                        'title'   => esc_html__( 'Enable Preloader', 'woo-variation-swatches-pro' ),
                        'desc'    => esc_html__( 'Enable single product page swatches preloader', 'woo-variation-swatches-pro' ),
                        'default' => 'yes',
                        //'is_new'  => true,
                    ),

                    array(
                        'id'           => 'enable_linkable_variation_url',
                        'type'         => 'checkbox',
                        'title'        => esc_html__( 'Generate variation url', 'woo-variation-swatches-pro' ),
                        'desc'         => esc_html__( 'Generate sharable url based on selected variation attributes.', 'woo-variation-swatches-pro' ),
                        'default'      => 'no',
                        'help_preview' => true,
                    ),

                    array(
                        'id'           => 'show_variation_stock_info',
                        'type'         => 'checkbox',
                        'title'        => esc_html__( 'Variation stock info', 'woo-variation-swatches-pro' ),
                        'desc'         => esc_html__( 'Show variation product stock info', 'woo-variation-swatches-pro' ),
                        'default'      => 'no',
                        'help_preview' => true,
                    ),

                    array(
                        'id'                => 'stock_label_display_threshold',
                        'type'              => 'number',
                        'title'             => esc_html__( 'Minimum stock threshold', 'woo-variation-swatches-pro' ),
                        'desc'              => esc_html__( 'When stock reaches this amount stock label will be shown.', 'woo-variation-swatches-pro' ),
                        'css'               => 'width: 50px;',
                        'default'           => '5',
                        'custom_attributes' => array(
                            'min'  => 1,
                            'max'  => 99,
                            'step' => 1,
                        ),
                        'require'           => $this->normalize_required_attribute( array( 'show_variation_stock_info' => array( 'type' => '!empty' ) ) ),
                    ),

                    array(
                        'id'                => 'display_limit',
                        'type'              => 'number',
                        // 'size'    => 'tiny',
                        'title'             => esc_html__( 'Attribute display limit', 'woo-variation-swatches-pro' ),
                        'desc'              => esc_html__( 'Single Product page attribute display limit. Default is 0. Means no limit.', 'woo-variation-swatches-pro' ),
                        'desc_tip'          => true,
                        'custom_attributes' => array( 'min' => 0 ),
                        'css'               => 'width: 80px;',
                        'default'           => '0',
                        'help_preview'      => true,
                        // 'require' => array( 'enable_catalog_mode' => array( 'type' => '!empty' ) )
                        // 'require'  => $this->normalize_required_attribute( array( 'enable_catalog_mode' => array( 'type' => '!empty' ) ) ),
                    ),

                    array(
                        'id'       => 'group_swatches_align',
                        'type'     => 'select',
                        'size'     => 'tiny',
                        'title'    => esc_html__( 'Group Swatches align', 'woo-variation-swatches-pro' ),
                        'desc'     => esc_html__( 'Group based Swatches align on product page', 'woo-variation-swatches-pro' ),
                        'desc_tip' => true,
                        'css'      => 'width: 100px;',
                        'default'  => 'horizontal',
                        'options'  => array(
                            'vertical'   => esc_html__( 'Vertical', 'woo-variation-swatches-pro' ),
                            'horizontal' => esc_html__( 'Horizontal', 'woo-variation-swatches-pro' ),
                        )
                    ),

                    array(
                        'type' => 'sectionend',
                        'id'   => 'single_page_options',
                    ),
                );

                return $settings;
            }

            protected function get_settings_for_archive_section() {
                $settings = array(

                    array(
                        'id'    => 'archive_options',
                        'type'  => 'title',
                        'title' => esc_html__( 'Visual Section', 'woo-variation-swatches-pro' ),
                        'desc'  => esc_html__( 'Advanced change some visual styles on shop / archive page', 'woo-variation-swatches-pro' ),
                    ),

                    array(
                        'id'           => 'show_on_archive',
                        'type'         => 'checkbox',
                        'title'        => esc_html__( 'Enable Swatches', 'woo-variation-swatches-pro' ),
                        'desc'         => esc_html__( 'Show swatches on archive / shop page.', 'woo-variation-swatches-pro' ),
                        'default'      => 'yes',
                        'help_preview' => true,
                    ),

                    array(
                        'id'      => 'enable_archive_preloader',
                        'type'    => 'checkbox',
                        'title'   => esc_html__( 'Enable Preloader', 'woo-variation-swatches-pro' ),
                        'desc'    => esc_html__( 'Enable archive page swatches preloader', 'woo-variation-swatches-pro' ),
                        'default' => 'yes',
                        //'is_new'  => true,
                    ),

                    array(
                        'id'      => 'archive_product_wrapper',
                        'type'    => 'text',
                        'title'   => esc_html__( 'Product wrapper', 'woo-variation-swatches-pro' ),
                        'desc'    => sprintf( __( 'Archive product wrapper selector, You can also use multiple selectors separated by comma (,). Default: %s.', 'woo-variation-swatches-pro' ), '<code>.wvs-archive-product-wrapper</code>' ),
                        //'desc_tip' => true,
                        'default' => '.wvs-archive-product-wrapper'
                    ),

                    array(
                        'id'      => 'archive_image_selector',
                        'type'    => 'text',
                        'title'   => esc_html__( 'Image selector', 'woo-variation-swatches-pro' ),
                        'desc'    => sprintf( __( 'Archive product image selector to show variation image. You can also use multiple selectors separated by comma (,). Default: %s.', 'woo-variation-swatches-pro' ), '<code>.wvs-archive-product-image</code>' ),
                        //'desc_tip' => true,
                        'default' => '.wvs-archive-product-image'
                    ),

                    array(
                        'id'      => 'archive_cart_button_selector',
                        'type'    => 'text',
                        'title'   => esc_html__( 'Add to cart button selector', 'woo-variation-swatches-pro' ),
                        'desc'    => sprintf( __( 'Archive add to cart button selector. Default should be: %s', 'woo-variation-swatches-pro' ), '<code>.wvs-add-to-cart-button</code>' ),
                        //'desc_tip' => true,
                        'default' => '.wvs-add-to-cart-button'
                    ),

                    array(
                        'id'           => 'archive_show_availability',
                        'type'         => 'checkbox',
                        'title'        => esc_html__( 'Show Product Availability', 'woo-variation-swatches-pro' ),
                        'desc'         => esc_html__( 'Show Product availability stock info', 'woo-variation-swatches-pro' ),
                        'default'      => 'no',
                        'help_preview' => true,
                    ),

                    array(
                        'id'           => 'archive_default_selected',
                        'type'         => 'checkbox',
                        //'is_pro' => true,
                        //'is_new' => true,
                        //'help_preview' => true,
                        'title'        => esc_html__( 'Show default selected', 'woo-variation-swatches-pro' ),
                        'desc'         => esc_html__( 'Show default selected attribute swatches on archive / shop page.', 'woo-variation-swatches-pro' ),
                        'default'      => 'yes',
                        'help_preview' => true,
                    ),


                    array(
                        'id'           => 'archive_swatches_position',
                        'type'         => 'radio',
                        'title'        => esc_html__( 'Display position', 'woo-variation-swatches-pro' ),
                        'desc'         => sprintf( __( 'Show archive swatches position. <span style="color: red">Note:</span> Some theme remove default woocommerce hooks that why it\'s may not work as expected. For theme compatibility <a target="_blank" href="%s">please open a ticket</a>.', 'woo-variation-swatches-pro' ), 'https://getwooplugins.com/tickets/' ),
                        //'desc_tip' => true,
                        'default'      => 'after',
                        'options'      => array(
                            'before' => esc_html__( 'Before add to cart button', 'woo-variation-swatches-pro' ),
                            'after'  => esc_html__( 'After add to cart button', 'woo-variation-swatches-pro' )
                        ),
                        'help_preview' => true,
                    ),

                    array(
                        'id'           => 'archive_align',
                        'type'         => 'select',
                        'size'         => 'tiny',
                        'title'        => esc_html__( 'Swatches align', 'woo-variation-swatches-pro' ),
                        'desc'         => esc_html__( 'Swatches align on archive page', 'woo-variation-swatches-pro' ),
                        'desc_tip'     => true,
                        'css'          => 'width: 100px;',
                        'default'      => 'flex-start',
                        'options'      => array(
                            'flex-start' => esc_html__( 'Left', 'woo-variation-swatches-pro' ),
                            'center'     => esc_html__( 'Center', 'woo-variation-swatches-pro' ),
                            'flex-end'   => esc_html__( 'Right', 'woo-variation-swatches-pro' )
                        ),
                        'help_preview' => true,
                    ),

                    array(
                        'id'      => 'show_clear_on_archive',
                        'type'    => 'checkbox',
                        'title'   => esc_html__( 'Show clear link', 'woo-variation-swatches-pro' ),
                        'desc'    => esc_html__( 'Show clear link on archive / shop page.', 'woo-variation-swatches-pro' ),
                        'default' => 'yes'
                    ),

                    array(
                        'id'           => 'show_swatches_on_filter_widget',
                        'type'         => 'checkbox',
                        'title'        => esc_html__( 'Show on filter widget', 'woo-variation-swatches-pro' ),
                        'desc'         => esc_html__( 'Show variation swatches on filter widget.', 'woo-variation-swatches-pro' ),
                        'default'      => 'yes',
                        'help_preview' => true,
                    ),

                    array(
                        'type' => 'sectionend',
                        'id'   => 'archive_options',
                    ),
                );

                return $settings;
            }

            protected function get_settings_for_special_section() {
                $settings = array(

                    // Catalog mode
                    array(
                        'id'    => 'catalog_mode_options',
                        'type'  => 'title',
                        'title' => esc_html__( 'Catalog mode', 'woo-variation-swatches-pro' ),
                        'desc'  => esc_html__( 'Show single attribute as catalog mode on shop / archive pages. Catalog mode only change image based on selected variation.', 'woo-variation-swatches-pro' ),
                    ),

                    array(
                        'id'           => 'enable_catalog_mode',
                        'type'         => 'checkbox',
                        'title'        => esc_html__( 'Show Single Attribute', 'woo-variation-swatches-pro' ),
                        'desc'         => esc_html__( 'Show Single Attribute taxonomies on archive page', 'woo-variation-swatches-pro' ),
                        'default'      => 'no',
                        'help_preview' => true,
                    ),

                    array(
                        'id'       => 'catalog_mode_attribute',
                        'type'     => 'select',
                        // 'size'     => 'tiny',
                        'title'    => esc_html__( 'Choose Attribute', 'woo-variation-swatches-pro' ),
                        'desc'     => esc_html__( 'Choose an attribute to show on catalog mode', 'woo-variation-swatches-pro' ),
                        'desc_tip' => true,
                        'css'      => 'width: 200px;',
                        'default'  => '',
                        'options'  => $this->get_taxonomies( false ), // wvs_pro_get_attribute_taxonomies_option()
                        // 'require' => array( 'enable_catalog_mode' => array( 'type' => '!empty' ) )
                        'require'  => $this->normalize_required_attribute( array( 'enable_catalog_mode' => array( 'type' => '!empty' ) ) ),
                    ),


                    array(
                        'id'      => 'disable_catalog_mode_on_single_attribute',
                        'type'    => 'checkbox',
                        'title'   => esc_html__( 'Single Attribute Catalog Mode', 'woo-variation-swatches-pro' ),
                        'desc'    => esc_html__( 'Enable "add to cart" and "change price" on catalog mode if a product have only one attribute to show', 'woo-variation-swatches-pro' ),
                        'default' => 'no',
                        'require' => $this->normalize_required_attribute( array( 'enable_catalog_mode' => array( 'type' => '!empty' ) ) ),

                    ),


                    array(
                        'id'       => 'catalog_mode_trigger',
                        'type'     => 'select',
                        // 'size'    => 'tiny',
                        'title'    => esc_html__( 'Catalog Mode Image Preview', 'woo-variation-swatches-pro' ),
                        'desc'     => esc_html__( 'Show catalog mode image', 'woo-variation-swatches-pro' ),
                        'desc_tip' => true,
                        'css'      => 'width: 200px;',
                        'default'  => 'click',
                        'options'  => array(
                            'click' => esc_html__( 'on Click', 'woo-variation-swatches-pro' ),
                            'hover' => esc_html__( 'on Hover', 'woo-variation-swatches-pro' ),
                        ),
                        // 'require' => array( 'enable_catalog_mode' => array( 'type' => '!empty' ) )
                        'require'  => $this->normalize_required_attribute( array( 'enable_catalog_mode' => array( 'type' => '!empty' ) ) ),
                    ),

                    array(
                        'id'      => 'linkable_attribute',
                        'type'    => 'checkbox',
                        'title'   => esc_html__( 'Linkable Attribute', 'woo-variation-swatches-pro' ),
                        'desc'    => esc_html__( 'Keep attribute variation selected on product page after clicking from shop / archive page', 'woo-variation-swatches-pro' ),
                        'default' => 'no',
                        'require' => $this->normalize_required_attribute( array(
                                                                              'catalog_mode_trigger' => array( 'type' => 'equal', 'value' => 'hover' ),
                                                                              'enable_catalog_mode'  => array( 'type' => '!empty' )
                                                                          ) )
                    ),

                    array(
                        'id'       => 'linkable_attribute_link_type',
                        'type'     => 'select',
                        'title'    => esc_html__( 'Linkable Attribute Link type', 'woo-variation-swatches-pro' ),
                        'desc_tip' => esc_html__( 'Linkable Attribute Link should be full url or partial url', 'woo-variation-swatches-pro' ),
                        'default'  => 'variation',
                        'options'  => array(
                            'variation' => esc_html__( 'Variation Product Link', 'woo-variation-swatches-pro' ),
                            'attribute' => esc_html__( 'Selected Attribute Link', 'woo-variation-swatches-pro' ),
                        ),
                        'require'  => $this->normalize_required_attribute( array(
                                                                               'catalog_mode_trigger' => array( 'type' => 'equal', 'value' => 'hover' ),
                                                                               'enable_catalog_mode'  => array( 'type' => '!empty' ),
                                                                               'linkable_attribute'   => array( 'type' => '!empty' )
                                                                           ) )
                    ),

                    array(
                        'id'                => 'catalog_mode_display_limit',
                        'type'              => 'number',
                        // 'size'    => 'tiny',
                        'title'             => esc_html__( 'Attribute display limit', 'woo-variation-swatches-pro' ),
                        'desc'              => esc_html__( 'Catalog mode attribute display limit. Default is 0. Means no limit.', 'woo-variation-swatches-pro' ),
                        'desc_tip'          => true,
                        'css'               => 'width: 50px;',
                        'default'           => '0',
                        'custom_attributes' => array( 'min' => 0, 'max' => 100 ),
                        // 'require' => array( 'enable_catalog_mode' => array( 'type' => '!empty' ) )
                        'require'           => $this->normalize_required_attribute( array( 'enable_catalog_mode' => array( 'type' => '!empty' ) ) ),
                    ),

                    array(
                        'id'       => 'catalog_mode_behaviour',
                        'type'     => 'select',
                        // 'size'    => 'tiny',
                        'title'    => esc_html__( 'Catalog More Link Behaviour', 'woo-variation-swatches-pro' ),
                        'desc'     => esc_html__( 'Catalog More Link Behaviour', 'woo-variation-swatches-pro' ),
                        'desc_tip' => true,
                        'css'      => 'width: 200px;',
                        'default'  => 'navigate',
                        'options'  => array(
                            'expand'   => esc_html__( 'Expand More Item', 'woo-variation-swatches-pro' ),
                            'navigate' => esc_html__( 'Navigate to Product Page', 'woo-variation-swatches-pro' ),
                        ),
                        // 'require' => array( 'enable_catalog_mode' => array( 'type' => '!empty' ) )
                        'require'  => $this->normalize_required_attribute( array(
                                                                               'enable_catalog_mode'        => array( 'type' => '!empty' ),
                                                                               'catalog_mode_display_limit' => array( 'type' => 'compare', 'sign' => '>', 'value' => '0' )
                                                                           ) )

                    ),

                    array(
                        'type' => 'sectionend',
                        'id'   => 'catalog_mode_options',
                    ),

                    array(
                        'id'    => 'single_variation_image_preview_options',
                        'type'  => 'title',
                        'title' => esc_html__( 'Single Variation Image Preview', 'woo-variation-swatches-pro' ),
                        'desc'  => esc_html__( 'Switch variation image when single attribute selected on product page.', 'woo-variation-swatches-pro' ),
                    ),

                    array(
                        'id'           => 'enable_single_variation_preview',
                        'type'         => 'checkbox',
                        'title'        => esc_html__( 'Variation Image Preview', 'woo-variation-swatches-pro' ),
                        'desc'         => esc_html__( 'Show single attribute variation image based on first attribute select on product page.', 'woo-variation-swatches-pro' ),
                        'default'      => 'no',
                        'help_preview' => true,
                    ),

                    array(
                        'id'       => 'single_variation_preview_attribute',
                        'type'     => 'select',
                        'title'    => esc_html__( 'Choose Attribute', 'woo-variation-swatches-pro' ),
                        'desc'     => esc_html__( 'Choose an attribute to show variation image', 'woo-variation-swatches-pro' ),
                        'desc_tip' => true,
                        'css'      => 'width: 200px;',
                        'default'  => '',
                        'options'  => $this->get_taxonomies(),
                        'require'  => $this->normalize_required_attribute( array( 'enable_single_variation_preview' => array( 'type' => '!empty' ) ) ),
                    ),

                    /*array(
                        'id'       => 'single_variation_preview_js_event',
                        'type'     => 'select',
                        'title'    => esc_html__( 'Fire JS Event', 'woo-variation-swatches-pro' ),
                        'desc'     => esc_html__( 'Fire Variation JS event on variation preview. Default is: "When Variation Shown"', 'woo-variation-swatches-pro' ),
                        'desc_tip' => true,
                        'css'      => 'width: 200px;',
                        'default'  => 'show_variation',
                        'options'  => array(
                            'show_variation'  => esc_html__( 'When Variation Shown', 'woo-variation-swatches-pro' ),
                            'found_variation' => esc_html__( 'When Variation Found', 'woo-variation-swatches-pro' )
                        ),
                        'require'  => $this->normalize_required_attribute( array( 'enable_single_variation_preview' => array( 'type' => '!empty' ) ) ),
                    ),*/

                    array(
                        'id'      => 'enable_single_variation_preview_archive',
                        'type'    => 'checkbox',
                        'title'   => esc_html__( 'Preview on Shop Page', 'woo-variation-swatches-pro' ),
                        'desc'    => esc_html__( 'Enable single variation image preview on shop / archive page. Won\'t active when Catalog mode enabled.', 'woo-variation-swatches-pro' ),
                        'default' => 'no',
                        'require' => $this->normalize_required_attribute( array( 'enable_single_variation_preview' => array( 'type' => '!empty' ) ) ),
                    ),

                    array(
                        'type' => 'sectionend',
                        'id'   => 'single_variation_image_preview_options',
                    ),

                    // Attribute large size
                    array(
                        'id'    => 'attr_large_size_options',
                        'type'  => 'title',
                        'title' => esc_html__( 'Large Size Attribute Section', 'woo-variation-swatches-pro' ),
                        'desc'  => esc_html__( 'Make a attribute taxonomies size large on single product', 'woo-variation-swatches-pro' ),
                    ),

                    array(
                        'id'           => 'enable_large_size',
                        'type'         => 'checkbox',
                        'title'        => esc_html__( 'Show First Attribute In Large Size', 'woo-variation-swatches-pro' ),
                        'desc'         => esc_html__( 'Show Attribute taxonomies in large size', 'woo-variation-swatches-pro' ),
                        'default'      => 'no',
                        'help_preview' => true,
                    ),

                    array(
                        'id'       => 'large_size_attribute',
                        'type'     => 'select',
                        // 'size'    => 'tiny',
                        'title'    => esc_html__( 'Choose Attribute', 'woo-variation-swatches-pro' ),
                        'desc'     => esc_html__( 'Choose an attribute to make it large', 'woo-variation-swatches-pro' ),
                        'desc_tip' => true,
                        'css'      => 'width: 200px;',
                        'default'  => '',
                        'options'  => $this->get_taxonomies(),
                        'require'  => $this->normalize_required_attribute( array( 'enable_large_size' => array( 'type' => '!empty' ) ) )
                    ),

                    array(
                        'id'                => 'large_size_width',
                        'type'              => 'number',
                        'title'             => esc_html__( 'Width', 'woo-variation-swatches-pro' ),
                        'desc'              => esc_html__( 'Large variation item width', 'woo-variation-swatches-pro' ),
                        'desc_tip'          => true,
                        'css'               => 'width: 60px;',
                        'default'           => 40,
                        'suffix'            => 'px',
                        'custom_attributes' => array(
                            'min'  => 10,
                            'max'  => 200,
                            'step' => 5,
                        ),
                        // 'require' => array( 'enable_large_size' => array( 'type' => '!empty' ) )
                        'require'           => $this->normalize_required_attribute( array( 'enable_large_size' => array( 'type' => '!empty' ) ) )
                    ),

                    array(
                        'id'                => 'large_size_height',
                        'type'              => 'number',
                        'title'             => esc_html__( 'Height', 'woo-variation-swatches-pro' ),
                        'desc'              => esc_html__( 'Large variation item height', 'woo-variation-swatches-pro' ),
                        'desc_tip'          => true,
                        'css'               => 'width: 60px;',
                        'default'           => 40,
                        'suffix'            => 'px',
                        'custom_attributes' => array(
                            'min'  => 10,
                            'max'  => 200,
                            'step' => 5,
                        ),
                        // 'require' => array( 'enable_large_size' => array( 'type' => '!empty' ) )
                        'require'           => $this->normalize_required_attribute( array( 'enable_large_size' => array( 'type' => '!empty' ) ) )
                    ),

                    array(
                        'id'                => 'large_size_font_size',
                        'type'              => 'number',
                        'title'             => esc_html__( 'Font Size', 'woo-variation-swatches-pro' ),
                        'desc'              => esc_html__( 'Large variation font size', 'woo-variation-swatches-pro' ),
                        'desc_tip'          => true,
                        'css'               => 'width: 60px;',
                        'default'           => 16,
                        'suffix'            => 'px',
                        'custom_attributes' => array(
                            'min'  => 8,
                            'max'  => 24,
                            'step' => 2,
                        ),
                        // 'require' => array( 'enable_large_size' => array( 'type' => '!empty' ) )
                        'require'           => $this->normalize_required_attribute( array( 'enable_large_size' => array( 'type' => '!empty' ) ) ),

                    ),

                    array(
                        'type' => 'sectionend',
                        'id'   => 'attr_large_size_options',
                    ),
                );

                return $settings;
            }

            protected function get_settings_for_license_section() {

                $settings = array(

                    array(
                        'name' => esc_html__( 'License Section', 'woo-variation-swatches-pro' ),
                        'type' => 'title',
                        'desc' => '',
                        'id'   => 'license_section',
                    ),

                    array(
                        'title'        => esc_html__( 'License key', 'woo-variation-swatches-pro' ),
                        'type'         => 'text',
                        'default'      => '',
                        'desc'         => esc_html__( 'License key', 'woo-variation-swatches-pro' ),
                        'id'           => 'license',
                        'standalone'   => true,
                        'help_preview' => true,
                    ),

                    array(
                        'type' => 'sectionend',
                        'id'   => 'license_section'
                    ),
                );

                return $settings;
            }

        }
    endif;



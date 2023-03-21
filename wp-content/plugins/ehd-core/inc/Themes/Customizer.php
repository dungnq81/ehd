<?php

namespace EHD\Themes;

\defined( 'ABSPATH' ) || die;

/**
 * Customizer Class
 *
 * @author eHD
 */

use WP_Customize_Color_Control;
use WP_Customize_Image_Control;
use WP_Customize_Manager;

final class Customizer {
	public function __construct() {

		// Theme Customizer settings and controls.
		add_action( 'customize_register', [ &$this, 'customize_register' ], 29 );

		//...
		add_action( 'login_enqueue_scripts', [ &$this, 'login_enqueue_script' ], 31 );
		add_action( 'enqueue_block_editor_assets', [ &$this, 'enqueue_block_editor_assets' ] );

		// Changing the alt text on the logo to show your site name
		add_filter( 'login_headertext', function () {
			return get_bloginfo( 'name' );
		} );

		// Changing the logo link from wordpress.org to your site
		add_filter( 'login_headerurl', function () {
			return esc_url( site_url( '/' ) );
		} );
	}

	/**
	 * Register customizer options.
	 *
	 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
	 */
	public function customize_register( WP_Customize_Manager $wp_customize ): void {
		// logo mobile
		$wp_customize->add_setting( 'alternative_logo' );
		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'alternative_logo',
				[
					'label'    => __( 'Alternative Logo', EHD_PLUGIN_TEXT_DOMAIN ),
					'section'  => 'title_tagline',
					'settings' => 'alternative_logo',
					'priority' => 8,
				]
			)
		);

		// add control
		$wp_customize->add_setting( 'logo_title_setting', [
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		] );

		$wp_customize->add_control(
			'logo_title_control',
			[
				'label'    => __( 'The title of logo', EHD_PLUGIN_TEXT_DOMAIN ),
				'section'  => 'title_tagline',
				'settings' => 'logo_title_setting',
				'type'     => 'text',
				'priority' => 9,
			]
		);

		// -------------------------------------------------------------
		// -------------------------------------------------------------

		// Create custom panels
		$wp_customize->add_panel(
			'addon_menu_panel',
			[
				'priority'       => 140,
				'theme_supports' => '',
				'title'          => __( 'eHD', EHD_PLUGIN_TEXT_DOMAIN ),
				'description'    => __( 'Controls the add-on menu', EHD_PLUGIN_TEXT_DOMAIN ),
			]
		);

		// -------------------------------------------------------------
		// offCanvas Menu
		// -------------------------------------------------------------

		$wp_customize->add_section(
			'offcanvas_menu_section',
			[
				'title'    => __( 'offCanvas', EHD_PLUGIN_TEXT_DOMAIN ),
				'panel'    => 'addon_menu_panel',
				'priority' => 1000,
			]
		);

		// add offcanvas control
		$wp_customize->add_setting(
			'offcanvas_menu_setting',
			[
				'default'           => 'default',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			]
		);
		$wp_customize->add_control(
			'offcanvas_menu_control',
			[
				'label'    => __( 'offCanvas position', EHD_PLUGIN_TEXT_DOMAIN ),
				'type'     => 'radio',
				'section'  => 'offcanvas_menu_section',
				'settings' => 'offcanvas_menu_setting',
				'choices'  => [
					'left'    => __( 'Left', EHD_PLUGIN_TEXT_DOMAIN ),
					'right'   => __( 'Right', EHD_PLUGIN_TEXT_DOMAIN ),
					'top'     => __( 'Top', EHD_PLUGIN_TEXT_DOMAIN ),
					'bottom'  => __( 'Bottom', EHD_PLUGIN_TEXT_DOMAIN ),
					'default' => __( 'Default (Right)', EHD_PLUGIN_TEXT_DOMAIN ),
				],
			]
		);

		// -------------------------------------------------------------
		// Breadcrumbs
		// -------------------------------------------------------------

		$wp_customize->add_section(
			'breadcrumb_section',
			[
				'title'    => __( 'Breadcrumbs', EHD_PLUGIN_TEXT_DOMAIN ),
				'panel'    => 'addon_menu_panel',
				'priority' => 1007,
			]
		);

		// add control
		$wp_customize->add_setting( 'breadcrumb_bg_setting', [ 'transport' => 'refresh' ] );
		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'breadcrumb_bg_control',
				[
					'label'    => __( 'Breadcrumb background', EHD_PLUGIN_TEXT_DOMAIN ),
					'section'  => 'breadcrumb_section',
					'settings' => 'breadcrumb_bg_setting',
					'priority' => 9,
				]
			)
		);

		// -------------------------------------------------------------
		// Header
		// -------------------------------------------------------------

		// Create footer section
		$wp_customize->add_section(
			'header_section',
			[
				'title'    => __( 'Header', EHD_PLUGIN_TEXT_DOMAIN ),
				'panel'    => 'addon_menu_panel',
				'priority' => 1008,
			]
		);

		// add control
		$wp_customize->add_setting( 'header_bg_setting', [ 'transport' => 'refresh' ] );
		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'header_bg_control',
				[
					'label'    => __( 'Header background', EHD_PLUGIN_TEXT_DOMAIN ),
					'section'  => 'header_section',
					'settings' => 'header_bg_setting',
					'priority' => 9,
				]
			)
		);

		// add control
		$wp_customize->add_setting( 'header_bgcolor_setting', [ 'sanitize_callback' => 'sanitize_hex_color' ] );
		$wp_customize->add_control(
			new WP_Customize_Color_Control( $wp_customize,
				'header_bgcolor_control',
				[
					'label'    => __( 'Header background Color', EHD_PLUGIN_TEXT_DOMAIN ),
					'section'  => 'header_section',
					'settings' => 'header_bgcolor_setting',
				]
			)
		);

		// -------------------------------------------------------------
		// Footer
		// -------------------------------------------------------------

		// Create footer section
		$wp_customize->add_section(
			'footer_section',
			[
				'title'    => __( 'Footer', EHD_PLUGIN_TEXT_DOMAIN ),
				'panel'    => 'addon_menu_panel',
				'priority' => 1008,
			]
		);

		// add control Footer background
		$wp_customize->add_setting( 'footer_bg_setting', [ 'transport' => 'refresh' ] );
		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'footer_bg_control',
				[
					'label'    => __( 'Footer background', EHD_PLUGIN_TEXT_DOMAIN ),
					'section'  => 'footer_section',
					'settings' => 'footer_bg_setting',
					'priority' => 9,
				]
			)
		);

		// add control
		$wp_customize->add_setting( 'footer_bgcolor_setting', [ 'sanitize_callback' => 'sanitize_hex_color' ] );
		$wp_customize->add_control(
			new WP_Customize_Color_Control( $wp_customize,
				'footer_bgcolor_control',
				[
					'label'    => __( 'Footer background Color', EHD_PLUGIN_TEXT_DOMAIN ),
					'section'  => 'footer_section',
					'settings' => 'footer_bgcolor_setting',
				]
			)
		);

		// add control
		$wp_customize->add_setting( 'footer_row_setting', [ 'sanitize_callback' => 'sanitize_text_field', ] );
		$wp_customize->add_control(
			'footer_row_control',
			[
				'label'       => __( 'Footer row number', EHD_PLUGIN_TEXT_DOMAIN ),
				'section'     => 'footer_section',
				'settings'    => 'footer_row_setting',
				'type'        => 'number',
				'description' => __( 'Footer rows number', EHD_PLUGIN_TEXT_DOMAIN ),
			]
		);

		// add control
		$wp_customize->add_setting( 'footer_col_setting', [ 'sanitize_callback' => 'sanitize_text_field', ] );
		$wp_customize->add_control(
			'footer_col_control',
			[
				'label'       => __( 'Footer columns number', EHD_PLUGIN_TEXT_DOMAIN ),
				'section'     => 'footer_section',
				'settings'    => 'footer_col_setting',
				'type'        => 'number',
				'description' => __( 'Footer columns number', EHD_PLUGIN_TEXT_DOMAIN ),
			]
		);

		// -------------------------------------------------------------
		// Block Editor
		// -------------------------------------------------------------

		// Block Editor + Gutenberg + Widget
		$wp_customize->add_section(
			'block_editor_section',
			[
				'title'    => __( 'Block Editor', EHD_PLUGIN_TEXT_DOMAIN ),
				'panel'    => 'addon_menu_panel',
				'priority' => 1010,
			]
		);

		// add control
		$wp_customize->add_setting(
			'use_widgets_block_editor_setting',
			[
				'default'   => false,
				//'sanitize_callback' => 'sanitize_checkbox',
				'transport' => 'refresh',
			]
		);

		$wp_customize->add_control(
			'use_widgets_block_editor_control',
			[
				'type'        => 'checkbox',
				'settings'    => 'use_widgets_block_editor_setting',
				'section'     => 'block_editor_section',
				'label'       => __( 'Disable block widgets', EHD_PLUGIN_TEXT_DOMAIN ),
				'description' => __( 'Disables the block editor from managing widgets', EHD_PLUGIN_TEXT_DOMAIN ),
			]
		);

		// add control
		$wp_customize->add_setting(
			'gutenberg_use_widgets_block_editor_setting',
			[
				'default'   => false,
				//'sanitize_callback' => 'sanitize_checkbox',
				'transport' => 'refresh',
			]
		);

		$wp_customize->add_control(
			'gutenberg_use_widgets_block_editor_control',
			[
				'type'        => 'checkbox',
				'settings'    => 'gutenberg_use_widgets_block_editor_setting',
				'section'     => 'block_editor_section',
				'label'       => __( 'Disable Gutenberg widgets', EHD_PLUGIN_TEXT_DOMAIN ),
				'description' => __( 'Disables the block editor from managing widgets in the Gutenberg plugin', EHD_PLUGIN_TEXT_DOMAIN ),
			]
		);

		// add control
		$wp_customize->add_setting(
			'use_block_editor_for_post_type_setting',
			[
				'default'   => false,
				//'sanitize_callback' => 'sanitize_checkbox',
				'transport' => 'refresh',
			]
		);

		$wp_customize->add_control(
			'use_block_editor_for_post_type_control',
			[
				'type'        => 'checkbox',
				'settings'    => 'use_block_editor_for_post_type_setting',
				'section'     => 'block_editor_section',
				'label'       => __( 'Use Classic Editor', EHD_PLUGIN_TEXT_DOMAIN ),
				'description' => __( 'Use Classic Editor - Disable Gutenberg Editor', EHD_PLUGIN_TEXT_DOMAIN ),
			]
		);

		// -------------------------------------------------------------
		// Others
		// -------------------------------------------------------------

		$wp_customize->add_section(
			'other_section',
			[
				'title'    => __( 'Other', EHD_PLUGIN_TEXT_DOMAIN ),
				'panel'    => 'addon_menu_panel',
				'priority' => 1011,
			]
		);

		// meta theme-color
		$wp_customize->add_setting( 'theme_color_setting', [ 'sanitize_callback' => 'sanitize_hex_color' ] );
		$wp_customize->add_control(
			new WP_Customize_Color_Control( $wp_customize,
				'theme_color_control',
				[
					'label'    => __( 'Theme Color', EHD_PLUGIN_TEXT_DOMAIN ),
					'section'  => 'other_section',
					'settings' => 'theme_color_setting',
				]
			)
		);

		// Hide menu
		$wp_customize->add_setting(
			'remove_menu_setting',
			[
				'sanitize_callback' => 'sanitize_textarea_field',
				'transport'         => 'refresh',
			]
		);

		$wp_customize->add_control(
			'remove_menu_control',
			[
				'type'        => 'textarea',
				'section'     => 'other_section',
				'settings'    => 'remove_menu_setting',
				'label'       => __( 'Remove Menu', EHD_PLUGIN_TEXT_DOMAIN ),
				'description' => __( 'The menu list will be hidden', EHD_PLUGIN_TEXT_DOMAIN ),
			]
		);
	}

	/** ---------------------------------------- */

	/**
	 * @retun void
	 */
	public function login_enqueue_script(): void {
		wp_enqueue_style( "login-style", EHD_PLUGIN_URL . "assets/css/admin.css", [], EHD_PLUGIN_VERSION );
		wp_enqueue_script( "login", EHD_PLUGIN_URL . "assets/js/login.js", [ "jquery" ], EHD_PLUGIN_VERSION, true );

		// custom script/style
		$logo    = EHD_PLUGIN_URL . "assets/img/logo.png";
		$logo_bg = EHD_PLUGIN_URL . "assets/img/login-bg.jpg";

		$css = new Css;
		if ( $logo_bg ) {
			$css->set_selector( 'body.login' );
			$css->add_property( 'background-image', 'url(' . $logo_bg . ')' );
		}

		$css->set_selector( 'body.login #login h1 a' );
		if ( $logo ) {
			$css->add_property( 'background-image', 'url(' . $logo . ')' );
		} else {
			$css->add_property( 'background-image', 'unset' );
		}

		if ( $css->css_output() ) {
			wp_add_inline_style( 'login-style', $css->css_output() );
		}
	}

	/** ---------------------------------------- */

	/**
	 * Gutenberg editor
	 *
	 * @return void
	 */
	public function enqueue_block_editor_assets(): void {
		wp_enqueue_style( 'editor-style', EHD_PLUGIN_URL . "assets/css/editor-style.css" );
	}
}

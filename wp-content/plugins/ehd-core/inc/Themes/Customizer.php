<?php

namespace EHD\Plugins\Themes;

\defined('ABSPATH') || die;

/**
 * Customizer Class
 *
 * @author eHD
 */

use WP_Customize_Color_Control;
use WP_Customize_Image_Control;
use WP_Customize_Manager;

class Customizer
{
    public function __construct()
    {
        // Theme Customizer settings and controls.
        add_action('customize_register', [&$this, 'customize_register'], 29);

        //...
        add_action('login_enqueue_scripts', [&$this, 'login_enqueue_script'], 31);
        add_action('enqueue_block_editor_assets', [&$this, 'enqueue_block_editor_assets']);
    }

    /**
     * Register customizer options.
     *
     * @param WP_Customize_Manager $wp_customize Theme Customizer object.
     */
    public function customize_register(WP_Customize_Manager $wp_customize)
    {
        // logo mobile
        $wp_customize->add_setting('alternative_logo');
        $wp_customize->add_control(
            new WP_Customize_Image_Control(
                $wp_customize,
                'alternative_logo',
                [
                    'label' => __('Alternative Logo', 'ehd-core'),
                    'section' => 'title_tagline',
                    'settings' => 'alternative_logo',
                    'priority' => 8,
                ]
            )
        );

        // add control
        $wp_customize->add_setting('logo_title_setting', [
            'sanitize_callback' => 'sanitize_text_field',
            'transport' => 'refresh'
        ]);

        $wp_customize->add_control(
            'logo_title_control',
            [
                'label' => __('The title of the logo', 'ehd-core'),
                'section' => 'title_tagline',
                'settings' => 'logo_title_setting',
                'type' => 'text',
                'priority' => 9,
            ]
        );

        // -------------------------------------------------------------
        // -------------------------------------------------------------

        // Create custom panels
        $wp_customize->add_panel(
            'addon_menu_panel',
            [
                'priority' => 140,
                'theme_supports' => '',
                'title' => __('eHD', 'ehd-core'),
                'description' => __('Controls the add-on menu', 'ehd-core'),
            ]
        );

        // -------------------------------------------------------------
        // offCanvas Menu
        // -------------------------------------------------------------

        $wp_customize->add_section(
            'offcanvas_menu_section',
            [
                'title' => __('offCanvas', 'ehd-core'),
                'panel' => 'addon_menu_panel',
                'priority' => 1000,
            ]
        );

        // add offcanvas control
        $wp_customize->add_setting(
            'offcanvas_menu_setting',
            [
                'default' => 'default',
                'sanitize_callback' => 'sanitize_text_field',
                'transport' => 'refresh',
            ]
        );
        $wp_customize->add_control(
            'offcanvas_menu_control',
            [
                'label' => __('offCanvas position', 'ehd-core'),
                'type' => 'radio',
                'section' => 'offcanvas_menu_section',
                'settings' => 'offcanvas_menu_setting',
                'choices' => [
                    'left' => __('Left', 'ehd-core'),
                    'right' => __('Right', 'ehd-core'),
                    'top' => __('Top', 'ehd-core'),
                    'bottom' => __('Bottom', 'ehd-core'),
                    'default' => __('Default (Right)', 'ehd-core'),
                ],
            ]
        );

        // -------------------------------------------------------------
        // News
        // -------------------------------------------------------------

        $wp_customize->add_section(
            'news_menu_section',
            [
                'title' => __('News', 'ehd-core'),
                'panel' => 'addon_menu_panel',
                'priority' => 1001,
            ]
        );

        // add news images
        $wp_customize->add_setting(
            'news_menu_setting',
            [
                'default' => 'default',
                'sanitize_callback' => 'sanitize_text_field',
                'transport' => 'refresh',
            ]
        );
        $wp_customize->add_control(
            'news_menu_control',
            [
                'label' => __('News images', 'ehd-core'),
                'type' => 'radio',
                'section' => 'news_menu_section',
                'settings' => 'news_menu_setting',
                'choices' => [
                    '1-1' => __('1:1', 'ehd-core'),
                    '3-2' => __('3:2', 'ehd-core'),
                    '4-3' => __('4:3', 'ehd-core'),
                    '16-9' => __('16:9', 'ehd-core'),
                    'default' => __('Ratio default (16:9)', 'ehd-core'),
                ],
            ]
        );

        // -------------------------------------------------------------
        // Products
        // -------------------------------------------------------------

        $wp_customize->add_section(
            'product_menu_section',
            [
                'title' => __('Products', 'hd'),
                'panel' => 'addon_menu_panel',
                'priority' => 1002,
            ]
        );

        // add product control
        $wp_customize->add_setting(
            'product_menu_setting',
            [
                'default' => 'default',
                'sanitize_callback' => 'sanitize_text_field',
                'transport' => 'refresh',
            ]
        );
        $wp_customize->add_control(
            'product_menu_control',
            [
                'label' => __('Products images', 'ehd-core'),
                'type' => 'radio',
                'section' => 'product_menu_section',
                'settings' => 'product_menu_setting',
                'choices' => [
                    '1-1' => __('1:1', 'ehd-core'),
                    '3-2' => __('3:2', 'ehd-core'),
                    '4-3' => __('4:3', 'ehd-core'),
                    '16-9' => __('16:9', 'ehd-core'),
                    'default' => __('Ratio default (16:9)', 'ehd-core'),
                ],
            ]
        );

        // -------------------------------------------------------------
        // Socials
        // -------------------------------------------------------------

        $wp_customize->add_section(
            'socials_menu_section',
            [
                'title' => __('Socials', 'ehd-core'),
                'panel' => 'addon_menu_panel',
                'priority' => 1005,
            ]
        );

        // Add options for facebook appid
        $wp_customize->add_setting('fb_menu_setting', ['sanitize_callback' => 'sanitize_text_field']);
        $wp_customize->add_control(
            'fb_menu_control',
            [
                'label' => __('Facebook AppID', 'ehd-core'),
                'section' => 'socials_menu_section',
                'settings' => 'fb_menu_setting',
                'type' => 'text',
                'description' => __("You can do this at <a href='https://developers.facebook.com/apps/'>developers.facebook.com/apps</a>", 'ehd-core'),
            ]
        );

        // Add options for facebook page_id
        $wp_customize->add_setting('fbpage_menu_setting', ['sanitize_callback' => 'sanitize_text_field']);
        $wp_customize->add_control(
            'fbpage_menu_control',
            [
                'label' => __('Facebook pageID', 'ehd-core'),
                'section' => 'socials_menu_section',
                'settings' => 'fbpage_menu_setting',
                'type' => 'text',
                'description' => __("How do I find my Facebook Page ID? <a href='https://www.facebook.com/help/1503421039731588'>facebook.com/help/1503421039731588</a>", 'ehd-core'),
            ]
        );

        // add control
        $wp_customize->add_setting(
            'fb_chat_setting',
            [
                'default' => false,
                //'capability'        => 'edit_theme_options',
                'sanitize_callback' => 'sanitize_checkbox',
                'transport' => 'refresh',
            ]
        );
        $wp_customize->add_control(
            'fb_chat_control',
            [
                'type' => 'checkbox',
                'settings' => 'fb_chat_setting',
                'section' => 'socials_menu_section',
                'label' => __('Facebook Live Chat', 'ehd-core'),
                //'description' => __( 'Thêm facebook messenger live chat', 'hd' ),
            ]
        );

        // Zalo Appid
        $wp_customize->add_setting('zalo_menu_setting', ['sanitize_callback' => 'sanitize_text_field',]);
        $wp_customize->add_control(
            'zalo_menu_control',
            [
                'label' => __('Zalo AppID', 'ehd-core'),
                'section' => 'socials_menu_section',
                'settings' => 'zalo_menu_setting',
                'type' => 'text',
                'description' => __("You can do this at <a href='https://developers.zalo.me/docs/'>developers.zalo.me/docs/</a>", 'ehd-core'),
            ]
        );

        // Zalo oaid
        $wp_customize->add_setting('zalo_oa_menu_setting', ['sanitize_callback' => 'sanitize_text_field',]);
        $wp_customize->add_control(
            'zalo_oa_menu_control',
            [
                'label' => __('Zalo OAID', 'hd'),
                'section' => 'socials_menu_section',
                'settings' => 'zalo_oa_menu_setting',
                'type' => 'text',
                'description' => __("You can do this at <a href='https://oa.zalo.me/manage/oa?option=create'>oa.zalo.me/manage/oa?option=create</a>", 'ehd-core'),
            ]
        );

        // add control
        $wp_customize->add_setting(
            'zalo_chat_setting',
            [
                'default' => false,
                //'capability'        => 'edit_theme_options',
                'sanitize_callback' => 'sanitize_checkbox',
                'transport' => 'refresh',
            ]
        );
        $wp_customize->add_control(
            'zalo_chat_control',
            [
                'type' => 'checkbox',
                'settings' => 'zalo_chat_setting',
                'section' => 'socials_menu_section',
                'label' => __('Zalo Live Chat', 'ehd-core'),
                //'description' => __( 'Thêm zalo live chat', 'hd' ),
            ]
        );

        // -------------------------------------------------------------
        // link
        // -------------------------------------------------------------

        $wp_customize->add_section(
            'link_menu_section',
            [
                'title' => __('Links', 'ehd-core'),
                'panel' => 'addon_menu_panel',
                'priority' => 1006,
            ]
        );

        // add control
        $wp_customize->add_setting('hotline_setting', [
            'sanitize_callback' => 'sanitize_text_field',
            'transport' => 'refresh'
        ]);

        $wp_customize->add_control(
            'hotline_control',
            [
                'label' => __('Hotline', 'ehd-core'),
                'section' => 'link_menu_section',
                'settings' => 'hotline_setting',
                'description' => __('Hotline number, support easier interaction on the phone', 'ehd-core'),
                'type' => 'text',
            ]
        );

        // -------------------------------------------------------------
        // Breadcrumbs
        // -------------------------------------------------------------

        $wp_customize->add_section(
            'breadcrumb_section',
            [
                'title' => __('Breadcrumbs', 'ehd-core'),
                'panel' => 'addon_menu_panel',
                'priority' => 1007,
            ]
        );

        // add control
        $wp_customize->add_setting('breadcrumb_bg_setting', ['transport' => 'refresh']);
        $wp_customize->add_control(
            new WP_Customize_Image_Control(
                $wp_customize,
                'breadcrumb_bg_control',
                [
                    'label' => __('Breadcrumb background', 'ehd-core'),
                    'section' => 'breadcrumb_section',
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
                'title' => __('Header', 'ehd-core'),
                'panel' => 'addon_menu_panel',
                'priority' => 1008,
            ]
        );

        // add control
        $wp_customize->add_setting('header_bg_setting', ['transport' => 'refresh']);
        $wp_customize->add_control(
            new WP_Customize_Image_Control(
                $wp_customize,
                'header_bg_control',
                [
                    'label' => __('Header background', 'ehd-core'),
                    'section' => 'header_section',
                    'settings' => 'header_bg_setting',
                    'priority' => 9,
                ]
            )
        );

        // add control
        $wp_customize->add_setting('header_bgcolor_setting', ['sanitize_callback' => 'sanitize_hex_color']);
        $wp_customize->add_control(
            new WP_Customize_Color_Control($wp_customize,
                'header_bgcolor_control',
                [
                    'label' => __('Header background Color', 'ehd-core'),
                    'section' => 'header_section',
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
                'title' => __('Footer', 'ehd-core'),
                'panel' => 'addon_menu_panel',
                'priority' => 1008,
            ]
        );

        // add control Footer background
        $wp_customize->add_setting('footer_bg_setting', ['transport' => 'refresh']);
        $wp_customize->add_control(
            new WP_Customize_Image_Control(
                $wp_customize,
                'footer_bg_control',
                [
                    'label' => __('Footer background', 'ehd-core'),
                    'section' => 'footer_section',
                    'settings' => 'footer_bg_setting',
                    'priority' => 9,
                ]
            )
        );

        // add control
        $wp_customize->add_setting('footer_bgcolor_setting', ['sanitize_callback' => 'sanitize_hex_color']);
        $wp_customize->add_control(
            new WP_Customize_Color_Control($wp_customize,
                'footer_bgcolor_control',
                [
                    'label' => __('Footer background Color', 'ehd-core'),
                    'section' => 'footer_section',
                    'settings' => 'footer_bgcolor_setting',
                ]
            )
        );

        // add control
        $wp_customize->add_setting('footer_row_setting', ['sanitize_callback' => 'sanitize_text_field',]);
        $wp_customize->add_control(
            'footer_row_control',
            [
                'label' => __('Footer row number', 'hd'),
                'section' => 'footer_section',
                'settings' => 'footer_row_setting',
                'type' => 'number',
                'description' => __('Footer rows number', 'hd'),
            ]
        );

        // add control
        $wp_customize->add_setting('footer_col_setting', ['sanitize_callback' => 'sanitize_text_field',]);
        $wp_customize->add_control(
            'footer_col_control',
            [
                'label' => __('Footer columns number', 'hd'),
                'section' => 'footer_section',
                'settings' => 'footer_col_setting',
                'type' => 'number',
                'description' => __('Footer columns number', 'hd'),
            ]
        );

        // -------------------------------------------------------------
        // Block Editor
        // -------------------------------------------------------------

        // Block Editor + Gutenberg + Widget
        $wp_customize->add_section(
            'block_editor_section',
            [
                'title' => __('Block Editor', 'ehd-core'),
                'panel' => 'addon_menu_panel',
                'priority' => 1010,
            ]
        );

        // add control
        $wp_customize->add_setting(
            'use_widgets_block_editor_setting',
            [
                'default' => false,
                'capability' => 'edit_theme_options',
                'sanitize_callback' => 'sanitize_checkbox',
                'transport' => 'refresh',
            ]
        );

        $wp_customize->add_control(
            'use_widgets_block_editor_control',
            [
                'type' => 'checkbox',
                'settings' => 'use_widgets_block_editor_setting',
                'section' => 'block_editor_section',
                'label' => __('Disable block widgets', 'ehd-core'),
                'description' => __('Disables the block editor from managing widgets', 'ehd-core'),
            ]
        );

        // add control
        $wp_customize->add_setting(
            'gutenberg_use_widgets_block_editor_setting',
            [
                'default' => false,
                'capability' => 'edit_theme_options',
                'sanitize_callback' => 'sanitize_checkbox',
                'transport' => 'refresh',
            ]
        );

        $wp_customize->add_control(
            'gutenberg_use_widgets_block_editor_control',
            [
                'type' => 'checkbox',
                'settings' => 'gutenberg_use_widgets_block_editor_setting',
                'section' => 'block_editor_section',
                'label' => __('Disable Gutenberg widgets', 'ehd-core'),
                'description' => __('Disables the block editor from managing widgets in the Gutenberg plugin', 'ehd-core'),
            ]
        );

        // add control
        $wp_customize->add_setting(
            'use_block_editor_for_post_type_setting',
            [
                'default' => false,
                'capability' => 'edit_theme_options',
                'sanitize_callback' => 'sanitize_checkbox',
                'transport' => 'refresh',
            ]
        );

        $wp_customize->add_control(
            'use_block_editor_for_post_type_control',
            [
                'type' => 'checkbox',
                'settings' => 'use_block_editor_for_post_type_setting',
                'section' => 'block_editor_section',
                'label' => __('Use Classic Editor', 'ehd-core'),
                'description' => __('Use Classic Editor - Disable Gutenberg Editor', 'ehd-core'),
            ]
        );

        // -------------------------------------------------------------
        // -------------------------------------------------------------

        // Other
        $wp_customize->add_section(
            'other_section',
            [
                'title' => __('Other', 'ehd-core'),
                'panel' => 'addon_menu_panel',
                'priority' => 1011,
            ]
        );

        // add control
        // meta theme-color
        $wp_customize->add_setting('theme_color_setting', ['sanitize_callback' => 'sanitize_hex_color']);
        $wp_customize->add_control(
            new WP_Customize_Color_Control($wp_customize,
                'theme_color_control',
                [
                    'label' => __('Theme Color', 'ehd-core'),
                    'section' => 'other_section',
                    'settings' => 'theme_color_setting',
                ]
            )
        );

        // Hide menu
        $wp_customize->add_setting(
            'remove_menu_setting',
            [
                'sanitize_callback' => 'sanitize_textarea_field',
                'transport' => 'refresh'
            ]
        );

        $wp_customize->add_control(
            'remove_menu_control',
            [
                'label' => __('Remove Menu', 'ehd-core'),
                'section' => 'other_section',
                'settings' => 'remove_menu_setting',
                'description' => __('The menu list will be hidden', 'ehd-core'),
                'type' => 'textarea',
            ]
        );
    }

    /** ---------------------------------------- */

    /**
     * @retun void
     */
    public function login_enqueue_script()
    {
        wp_enqueue_style("login-style", EHD_PLUGIN_URL . "assets/css/admin.css", [], EHD_VERSION);
        wp_enqueue_script("login", EHD_PLUGIN_URL . "assets/js/login.js", ["jquery"], EHD_VERSION, true);

        // custom script/style
        $logo = EHD_PLUGIN_URL . "assets/img/logo.png";
        $logo_bg = EHD_PLUGIN_URL . "assets/img/login-bg.jpg";

        $css = new Css;
        if ($logo_bg) {
            $css->set_selector('body.login');
            $css->add_property('background-image', 'url(' . $logo_bg . ')');
        }

        $css->set_selector('body.login #login h1 a');
        if ($logo) {
            $css->add_property('background-image', 'url(' . $logo . ')');
        } else {
            $css->add_property('background-image', 'unset');
        }

        if ($css->css_output()) {
            wp_add_inline_style('login-style', $css->css_output());
        }
    }

    /** ---------------------------------------- */

    /**
     * Gutenberg editor
     *
     * @return void
     */
    public function enqueue_block_editor_assets()
    {
        wp_enqueue_style('editor-style', EHD_PLUGIN_URL . "assets/css/editor-style.css");
    }
}
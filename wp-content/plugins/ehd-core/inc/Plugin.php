<?php

namespace EHD;

use EHD\Cores\Shortcode;

use EHD\Plugins\ACF;
use EHD\Plugins\Elementor\Elementor;
use EHD\Plugins\WooCommerce\WooCommerce;

use EHD\Themes\Admin;
use EHD\Themes\Customizer;
use EHD\Themes\Optimizer;

use EHD\Widgets\DropdownSearch_Widget;
use EHD\Widgets\offCanvas_Widget;
use EHD\Widgets\RecentPosts_Widget;
use EHD\Widgets\Search_Widget;

\defined('ABSPATH') || die;

final class Plugin
{
    public function __construct()
    {
        add_action('init', [&$this, 'i18n']);
        add_action('init', [&$this, 'init']);

        add_action('plugins_loaded', [&$this, 'plugins_loaded']);
        add_action('wp_enqueue_scripts', [&$this, 'enqueue']);
    }

    /**
     * @return void
     */
    public function plugins_loaded() : void
    {
        // Widgets wordpress
        add_action('widgets_init', [&$this, 'unregister_default_widgets'], 11);
        add_action('widgets_init', [&$this, 'register_widgets'], 11);

        /** WooCommerce */
        class_exists('\WooCommerce') && (new WooCommerce());

        /** ACF */
        if (!class_exists('\ACF')) {
            add_action('admin_notices', [$this, 'admin_notice_missing_acf']);
        } else {
            (new ACF());
        }

        /** Elementor */
        if (!did_action('elementor/loaded')) {
            add_action('admin_notices', [$this, 'admin_notice_missing_elementor']);
        } else {
            (new Elementor());
        }
    }

    /**
     * Unregisters a WP_Widget widget
     *
     * @return void
     */
    public function unregister_default_widgets() : void
    {
        unregister_widget('WP_Widget_Search');
        unregister_widget('WP_Widget_Recent_Posts');
    }

    /**
     * Registers a WP_Widget widget
     *
     * @return void
     */
    public function register_widgets() : void
    {
        class_exists(offCanvas_Widget::class) && register_widget(new offCanvas_Widget());
        class_exists(Search_Widget::class) && register_widget(new Search_Widget());
        class_exists(DropdownSearch_Widget::class) && register_widget(new DropdownSearch_Widget());
        class_exists(RecentPosts_Widget::class) && register_widget(new RecentPosts_Widget());
    }

    /**
     * @return void
     */
    public function i18n() : void
    {
        // Load localization file
        load_plugin_textdomain(EHD_PLUGIN_TEXT_DOMAIN);
        load_plugin_textdomain(EHD_PLUGIN_TEXT_DOMAIN, false, EHD_PLUGIN_PATH . 'languages');
    }

    /**
     * Handles admin notice for non-active
     *
     * @return void
     */
    public function admin_notice_missing_acf() : void
    {
        $class = 'notice notice-error';
        $message = sprintf(__('You need %1$s"Advanced Custom Fields"%2$s for the %1$s"EHD-Core"%2$s plugin to work and updated.', EHD_PLUGIN_TEXT_DOMAIN), '<strong>', '</strong>');

        printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), $message);
    }

    /**
     * Handles admin notice for non-active
     *
     * Elementor plugin situations
     *
     * @return void
     */
    public function admin_notice_missing_elementor() : void
    {
        $class = 'notice notice-error';
        $message = sprintf(__('You need %1$s"Elementor"%2$s for the %1$s"EHD-Core"%2$s plugin to work and updated.', EHD_PLUGIN_TEXT_DOMAIN), '<strong>', '</strong>');

        printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), $message);
    }

    /**
     * @return void
     */
    public function enqueue() : void
    {
        wp_register_style('ehd-core-style', EHD_PLUGIN_URL . "assets/css/ehd.css", [], EHD_PLUGIN_VERSION);
        wp_register_script("ehd-core", EHD_PLUGIN_URL . "assets/js/ehd.js", ["jquery"], EHD_PLUGIN_VERSION, true);
    }

    /**
     * @return void
     */
    public function init() : void
    {
        if (is_admin()) {
            (new Admin());
        }

        (new Customizer());
        (new Optimizer());
        (new Shortcode())::init();
    }
}

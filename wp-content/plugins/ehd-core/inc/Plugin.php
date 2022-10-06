<?php

namespace EHD\Plugins;

use EHD\Plugins\Themes\Admin;
use EHD\Plugins\Themes\Customizer;
use EHD\Plugins\Themes\Optimizer;
use EHD\Plugins\Widgets\offCanvas_Widget;

\defined('ABSPATH') || die;

final class Plugin
{
    public function __construct()
    {
        $this->_init();

        //...
        add_action('init', [&$this, 'i18n']);
        add_action('plugins_loaded', [&$this, 'plugins_loaded']);

        add_action('wp_enqueue_scripts', [&$this, 'enqueue']);
    }

    /**
     * @return void
     */
    public function plugins_loaded() : void
    {
        // Notice if the Elementor is not active
        if (!did_action('elementor/loaded')) {
            add_action('admin_notices', [$this, 'admin_notice_missing_elementor']);
            return;
        }

        // widgets wordpress
        add_action('widgets_init', [&$this, 'w_register_widgets'], 10);

        // Elementor action
        //
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

    }

    /**
     * Registers a WP_Widget widget
     *
     * @return void
     */
    public function w_register_widgets() : void
    {
        class_exists(offCanvas_Widget::class) && register_widget(new offCanvas_Widget);
    }

    /**
     * @return void
     */
    protected function _init() : void
    {
        if (is_admin()) {
            (new Admin());
        }

        (new Customizer());
        (new Optimizer());
    }
}
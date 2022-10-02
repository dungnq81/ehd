<?php
/**
 * Plugin Name: eHD Core
 * Plugin URI: https://webhd.vn
 * Description: Core plugin for EHD Theme
 * Version: 1.0.1
 * Requires PHP: 7.4
 * Author: eHD Team
 * Author URI: https://webhd.vn
 * Text Domain: ehd-core
 * Domain Path: /languages
 * License: GPL-3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 */

\defined('ABSPATH') || die;

const EHD_VERSION = '1.0.1';
const EHD__FILE__ = __FILE__;

define('EHD_PLUGIN_URL', plugins_url('/', __FILE__)); // https://**/wp-content/plugins/ehd-core/
define('EHD_PLUGIN_PATH', plugin_dir_path(__FILE__)); // **\wp-content\plugins\ehd-core/
define('EHD_PLUGIN_BASE', plugin_basename(EHD__FILE__)); // ehd-core/ehd-core.php

if (file_exists($composer = __DIR__ . '/vendor/autoload.php')) {
    require_once $composer;
}

add_action('plugins_loaded', 'ehd_load');
register_activation_hook(EHD__FILE__, 'ehd_activate');

/**
 * Load the plugin after Elementor (and other plugins) are loaded.
 *
 * @return void
 */
function ehd_load()
{
    // Load localization file
    load_plugin_textdomain('ehd-core');

    // Notice if the Elementor is not active
    if (!did_action('elementor/loaded')) {
        add_action('admin_notices', 'ehd_fail_load');
        return;
    }

    //require_once EHD_PLUGIN_PATH . 'inc/Plugin.php';
    (new \EHD\Plugins\Plugin());
}

/**
 * Handles admin notice for non-active
 * Elementor plugin situations
 * @return void
 */
function ehd_fail_load()
{
    $class = 'notice notice-error';
    $message = sprintf(__('You need %1$s"Elementor"%2$s for the %1$s"EHD-Core"%2$s plugin to work and updated.', 'ehd-core'), '<strong>', '</strong>');

    printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), $message);
}

/**
 * Runs code upon activation
 * @return void
 */
function ehd_activate()
{
    add_option('ehd_do_activation_redirect', true);
}
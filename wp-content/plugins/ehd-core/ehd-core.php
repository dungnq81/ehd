<?php
/**
 * Plugin Name: eHD Core
 * Plugin URI: https://webhd.vn
 * Description: Core plugin for EHD Theme
 * Version: 1.0.2
 * Requires PHP: 7.4
 * Author: eHD Team
 * Author URI: https://webhd.vn
 * Text Domain: ehd-core
 * License: GPL-3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 */

use EHD\Plugins\Plugin;

\defined('ABSPATH') || die;

$headers = [
    'Name' => 'Plugin Name',
    'Version' => 'Version',
    'TextDomain'  => 'Text Domain',
];

$plugin_data = get_file_data(__FILE__, $headers, 'plugin');

define('EHD_PLUGIN_VERSION', $plugin_data['Version']);
define('EHD_PLUGIN_URL', plugins_url('/', __FILE__)); // https://**/wp-content/plugins/ehd-core/
define('EHD_PLUGIN_PATH', plugin_dir_path(__FILE__)); // **\wp-content\plugins\ehd-core/
define('EHD_PLUGIN_BASENAME', plugin_basename(__FILE__)); // ehd-core/ehd-core.php

if (file_exists($composer = __DIR__ . '/vendor/autoload.php')) {
    require_once $composer;
}

//...
add_action('plugins_loaded', 'ehd_load');

/**
 * Load the plugin after Elementor (and other plugins) are loaded.
 *
 * @return void
 */
function ehd_load() : void
{
    // Load localization file
    load_plugin_textdomain('ehd-core');
    load_plugin_textdomain('ehd-core', false, EHD_PLUGIN_PATH . 'languages');

    // Notice if the Elementor is not active
    if (!did_action('elementor/loaded')) {
        add_action('admin_notices', 'ehd_fail_load');
        return;
    }

    // require_once EHD_PLUGIN_PATH . 'inc/Plugin.php';
    new Plugin();
}

/**
 * Handles admin notice for non-active
 * Elementor plugin situations
 *
 * @return void
 */
function ehd_fail_load() : void
{
    $class = 'notice notice-error';
    $message = sprintf(__('You need %1$s"Elementor"%2$s for the %1$s"EHD-Core"%2$s plugin to work and updated.', 'ehd-core'), '<strong>', '</strong>');

    printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), $message);
}

//...
register_activation_hook(__FILE__, 'ehd_activate');

/**
 * Runs code upon activation
 *
 * @return void
 */
function ehd_activate() : void
{
    add_option('ehd_do_activation_redirect', true);
}
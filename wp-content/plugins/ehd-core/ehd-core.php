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

use EHD\Plugin;

\defined('ABSPATH') || die;

$headers = [
    'Name'       => 'Plugin Name',
    'Version'    => 'Version',
    'TextDomain' => 'Text Domain',
];

$plugin_data = get_file_data(__FILE__, $headers, 'plugin');

define('EHD_PLUGIN_URL', plugin_dir_url(__FILE__));       // https://**/wp-content/plugins/ehd-core/
define('EHD_PLUGIN_PATH', plugin_dir_path(__FILE__));     // **\wp-content\plugins\ehd-core/
define('EHD_PLUGIN_BASENAME', plugin_basename(__FILE__)); // ehd-core/ehd-core.php

define('EHD_PLUGIN_VERSION', $plugin_data['Version']);
define('EHD_PLUGIN_TEXT_DOMAIN', $plugin_data['TextDomain']);

defined('EHD_MU_PLUGIN_VERSION') || wp_die(__('eHD Core requires "eHD mu-core" plugin to function properly', EHD_PLUGIN_TEXT_DOMAIN));

file_exists($composer = __DIR__ . '/vendor/autoload.php') || wp_die(__('Error locating autoloader. Please run <code>composer install</code>.', EHD_PLUGIN_TEXT_DOMAIN));

require_once $composer;

// require_once EHD_PLUGIN_PATH . 'inc/Plugin.php';
(new Plugin());

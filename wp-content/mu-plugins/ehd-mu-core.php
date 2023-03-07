<?php

/**
 * Plugin Name: eHD mu-core
 * Plugin URI: https://webhd.vn
 * Description: Core plugin for EHD Theme
 * Version: 1.0.2
 * Requires PHP: 7.4
 * Author: eHD Team
 * Author URI: https://webhd.vn
 * Text Domain: ehd-mu-core
 * License: GPL-3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 */

$headers = [
    'Name'       => 'Plugin Name',
    'Version'    => 'Version',
    'TextDomain' => 'Text Domain',
];

$plugin_data = get_file_data(__FILE__, $headers, 'plugin');

define('EHD_MU_PLUGIN_VERSION', $plugin_data['Version']);
define('EHD_MU_PLUGIN_TEXT_DOMAIN', $plugin_data['TextDomain']);

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

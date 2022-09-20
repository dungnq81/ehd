<?php
/**
 * Theme functions and definitions
 *
 * @package eHD
 */

use EHD\Themes\Theme;

$theme_version = (wp_get_theme()->get('Version')) ?: false;
$theme_author = (wp_get_theme()->get('Author')) ?: 'eHD Team';

defined('EHD_THEME_VERSION') || define('EHD_THEME_VERSION', $theme_version);
defined('EHD_AUTHOR') || define('EHD_AUTHOR', $theme_author);
defined('EHD_THEME_PATH') || define('EHD_THEME_PATH', untrailingslashit(get_template_directory()));
defined('EHD_THEME_URL') || define('EHD_THEME_URL', untrailingslashit(esc_url(get_template_directory_uri())));

if (!file_exists($composer = __DIR__ . '/vendor/autoload.php')) {
    wp_die(__('Error locating autoloader. Please run <code>composer install</code>.', 'apx'));
}

require $composer;

// Initialize theme settings.
( new Theme )->init();
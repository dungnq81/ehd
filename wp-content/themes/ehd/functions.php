<?php

/**
 * Theme functions and definitions
 *
 * @package eHD
 */

use EHD\Sites\Themes\Theme;

$theme_version = (wp_get_theme()->get('Version')) ?: false;
$theme_author = (wp_get_theme()->get('Author')) ?: 'eHD Team';
$text_domain = (wp_get_theme()->get('TextDomain')) ?: 'ehd';

define('EHD_TEXT_DOMAIN', $text_domain);
define('EHD_THEME_VERSION', $theme_version);
define('EHD_AUTHOR', $theme_author);

define('EHD_THEME_PATH', untrailingslashit(get_template_directory()));
define('EHD_THEME_URL', untrailingslashit(esc_url(get_template_directory_uri())));

//...
if (!is_admin()) {
    defined('EHD_PLUGIN_VERSION') || wp_die(__('eHD Theme requires "ehd-core" plugin to work properly', EHD_TEXT_DOMAIN));
}

//...
file_exists($composer = __DIR__ . '/vendor/autoload.php') || wp_die(__('Error locating autoloader. Please run <code>composer install</code>.', EHD_TEXT_DOMAIN));

require_once $composer;

// Initialize theme settings.
(new Theme());

<?php

/**
 * Theme functions and definitions
 *
 * @package eHD
 */

use EHD_Sites\Themes\Theme;

$theme_version = ( wp_get_theme()->get( 'Version' ) ) ?: false;
$theme_author  = ( wp_get_theme()->get( 'Author' ) ) ?: 'eHD Team';
$text_domain   = ( wp_get_theme()->get( 'TextDomain' ) ) ?: 'ehd';

define( 'EHD_TEXT_DOMAIN', $text_domain );
define( 'EHD_THEME_VERSION', $theme_version );
define( 'EHD_AUTHOR', $theme_author );

define( 'EHD_THEME_PATH', untrailingslashit( get_template_directory() ) );
define( 'EHD_THEME_URL', untrailingslashit( esc_url( get_template_directory_uri() ) ) );

file_exists( __DIR__ . '/vendor/autoload.php' ) || wp_die( __( 'Error locating autoloader. Please run <code>composer install</code>.', EHD_TEXT_DOMAIN ) );
require_once __DIR__ . '/vendor/autoload.php';

if ( ! defined( 'EHD_PLUGIN_VERSION' ) ) {
	switch_theme( WP_DEFAULT_THEME );
	wp_die( __( 'eHD Theme requires "ehd-core" plugin to function properly.', EHD_TEXT_DOMAIN ) );
}

// Initialize theme settings.
( new Theme() );

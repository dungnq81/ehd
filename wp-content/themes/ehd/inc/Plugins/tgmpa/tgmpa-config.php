<?php
/**
 * This file represents an example of the code that themes would use to register
 * the required plugins.
 *
 * It is expected that theme authors would copy and paste this code into their
 * functions.php file, and amend to suit.
 *
 * @see http://tgmpluginactivation.com/configuration/ for detailed documentation.
 *
 * @package    TGM-Plugin-Activation
 * @subpackage Example
 * @version    2.6.1 for parent theme eHD for publication on WordPress.org
 * @author     Thomas Griffin, Gary Jones, Juliette Reinders Folmer
 * @copyright  Copyright (c) 2011, Thomas Griffin
 * @license    http://opensource.org/licenses/gpl-2.0.php GPL v2 or later
 * @link       https://github.com/TGMPA/TGM-Plugin-Activation
 */

//require_once get_template_directory() . '/inc/Themes/tgmpa/TGM_Plugin_Activation.php';
require_once __DIR__ . '/TGM_Plugin_Activation.php';

add_action( 'tgmpa_register', 'ehd_register_required_plugins' );

/**
 * @return void
 */
function ehd_register_required_plugins(): void {

	/*
	 * Array of plugin arrays. Required keys are name and slug.
	 * If the source is NOT from the .org repo, then source is also required.
	 */
	$plugins = [

		//
		// From the WordPress repository
		//
		[
			'name'     => 'Contact Form 7',
			'slug'     => 'contact-form-7',
			'required' => false, // required / recommended
		],
//		[
//			'name'     => 'Elementor',
//			'slug'     => 'elementor',
//			'required' => false,
//		],
		[
			'name'     => 'WooCommerce',
			'slug'     => 'woocommerce',
			'required' => false,
		],
		[
			'name'     => 'Akismet Anti-Spam',
			'slug'     => 'akismet',
			'required' => false,
		],
		[
			'name'     => 'Rank Math SEO',
			'slug'     => 'seo-by-rank-math',
			'required' => false,
		],
//		[
//			'name'     => 'Comments – wpDiscuz',
//			'slug'     => 'wpdiscuz',
//			'required' => false,
//		],
		[
			'name'     => 'Converter for Media',
			'slug'     => 'webp-converter-for-media',
			'required' => false,
		],
		[
			'name'     => 'Easy Table of Contents',
			'slug'     => 'easy-table-of-contents',
			'required' => false,
		],

		//
		// Include a plugin bundled with a theme.
		//
		[
			'name'             => 'Advanced Custom Fields PRO',
			'slug'             => 'advanced-custom-fields-pro',
			'source'           => __DIR__ . '/bundled/advanced-custom-fields-pro.zip',
			'required'         => true,
			'force_activation' => false,
			'external_url'     => 'https://www.advancedcustomfields.com/'
		],
//		[
//			'name'             => 'Fixed TOC',
//			'slug'             => 'fixed-toc',
//			'source'           => __DIR__ . '/bundled/fixed-toc.zip',
//			'required'         => false,
//			'force_activation' => false,
//			'external_url'     => 'https://codecanyon.net/item/fixed-toc-wordpress-plugin/7264676'
//		],
	];

	/*
	 * Array of configuration settings. Amend each line as needed.
	 */
	$config = [
		'id'           => 'ehd',
		'default_path' => '',
		'menu'         => 'ehd-install-plugins',
		'parent_slug'  => 'themes.php',
		'capability'   => 'edit_theme_options',
		'has_notices'  => true,
		'dismissable'  => false,
		'dismiss_msg'  => '',
		'is_automatic' => false,
		'message'      => '',
	];

	tgmpa( $plugins, $config );
}

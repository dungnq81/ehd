<?php

namespace EHD_Themes;

use EHD_Cores\Helper;
use EHD_CSS\CSS;

\defined('ABSPATH') || die;

/**
 * Custom Login Class
 *
 * @author eHD
 */
final class Login {
	public function __construct() {

		add_action( 'login_enqueue_scripts', [ &$this, 'login_enqueue_script' ], 31 );

		// Changing the alt text on the logo to show your site name
		add_filter( 'login_headertext', function () {
			return get_bloginfo( 'name' );
		} );

		// Changing the logo link from wordpress.org to your site
		add_filter( 'login_headerurl', function () {
			return Helper::home();
		} );
	}

	/**
	 * @retun void
	 */
	public function login_enqueue_script(): void
	{
		wp_enqueue_style( "login-style", EHD_PLUGIN_URL . "assets/css/admin.css", [], EHD_PLUGIN_VERSION );
		wp_enqueue_script( "login", EHD_PLUGIN_URL . "assets/js/login.js", [ "jquery" ], EHD_PLUGIN_VERSION, true );

		// custom script/style
		$logo    = EHD_PLUGIN_URL . "assets/img/logo.png";
		$logo_bg = EHD_PLUGIN_URL . "assets/img/login-bg.jpg";

		$css = new CSS();
		if ( $logo_bg ) {
			$css->set_selector( 'body.login' );
			$css->add_property( 'background-image', 'url(' . $logo_bg . ')' );
		}

		$css->set_selector( 'body.login #login h1 a' );
		if ( $logo ) {
			$css->add_property( 'background-image', 'url(' . $logo . ')' );
		} else {
			$css->add_property( 'background-image', 'unset' );
		}

		if ( $css->css_output() ) {
			wp_add_inline_style( 'login-style', $css->css_output() );
		}
	}
}
<?php

namespace EHD_Themes;

use EHD_Cores\Helper;
use EHD_Libs\Security\Headers;
use EHD_Libs\Security\Illegal_Users;
use EHD_Libs\Security\Login_Attempts;
use EHD_Libs\Security\Readme;
use EHD_Libs\Security\Two_FA;

/**
 * Security Class
 *
 * @author eHD
 */
final class Security {

	/**
	 * @var array|false|mixed
	 */
	public $security_options = [];

	// ------------------------------------------------------

	public function __construct() {

		$this->security_options = Helper::getOption( 'security__options', false, false );

		$this->_illegal_users();
		$this->_hide_wp_version();
		$this->_disable_XMLRPC();
		$this->_remove_ReadMe();
		$this->_disable_RSSFeed();
		$this->_xss_protection();
		$this->_login_attempts();
		$this->_2fa_hooks();
	}

	// ------------------------------------------------------

	/**
	 * Add two-factor auth hooks.
	 *
	 * @return void
	 */
	private function _2fa_hooks() {
		$two_factor_authentication = $this->security_options['two_factor_authentication'] ?? 0;
		$two_fa = new Two_FA();

		// Check

		add_filter( 'pre_update_option_ehd_security_2fa', [ &$two_fa, 'handle_option_change' ], 10, 2 );
		add_action( 'admin_notices', [ &$two_fa, 'show_notices' ] );
		add_action( 'wp_ajax_dismiss_ehd_2fa_notice', [ &$two_fa, 'hide_notice' ] );

		// Bail if the option is not enabled.
		if ( ! $two_factor_authentication ) {
			return;
		}

		add_action( 'wp_login', [ &$two_fa, 'move_encryption_file' ], 9, 2 );
		add_action( 'wp_login', [ &$two_fa, 'init_2fa' ], 10, 2 );
		add_action( 'login_form_ehd_2fa', [ &$two_fa, 'validate_2fa_login' ] );
		add_action( 'login_form_ehd_2fabc', [ &$two_fa, 'validate_2fabc_login' ] );
		add_action( 'login_form_load_ehd_2fabc', [ &$two_fa, 'load_backup_codes_form' ] );
	}

	// ------------------------------------------------------

	/**
	 * Add login service hooks.
	 *
	 * @return void
	 */
	private function _login_attempts() {
		$limit_login_attempts = $this->security_options['limit_login_attempts'] ?? 0;
		$security_login       = new Login_Attempts();

		// Bail if optimization is disabled.
		if ( 0 === intval( $limit_login_attempts ) ) {
			$security_login->reset_login_attempts();

			return;
		}

		// Check the login attempts for an IP and block the access to the login page.
		add_action( 'login_head', [ &$security_login, 'maybe_block_login_access' ], PHP_INT_MAX );

		// Add login attempts for ip.
		add_filter( 'login_errors', [ &$security_login, 'log_login_attempt' ] );

		// Reset login attempts for an ip on successful login.
		add_filter( 'wp_login', [ &$security_login, 'reset_login_attempts' ] );
	}

	// ------------------------------------------------------

	/**
	 * Add username hooks.
	 *
	 * @return void
	 */
	private function _illegal_users() {
		$illegal_users_option = $this->security_options['illegal_users'] ?? 0;
		if ( $illegal_users_option ) {
			$common_user = new Illegal_Users();
			add_action( 'illegal_user_logins', [ &$common_user, 'get_illegal_usernames' ] );
		}
	}

	// ------------------------------------------------------

	/**
	 * Add headers_service hooks.
	 *
	 * @return void
	 */
	private function _xss_protection() {
		$xss_protection = $this->security_options['advanced_xss_protection'] ?? 0;
		if ( $xss_protection ) {
			$headers = new Headers();

			// Add security headers.
			add_action( 'wp_headers', [ &$headers, 'set_security_headers' ] );

			// Add security headers for rest.
			add_filter( 'rest_post_dispatch', [ &$headers, 'set_rest_security_headers' ] );
		}
	}

	// ------------------------------------------------------

	/**
	 * Remove the WordPress version meta tag and parameter.
	 *
	 * @return void
	 */
	private function _hide_wp_version() {
		$hide_wp_version = $this->security_options['hide_wp_version'] ?? 0;
		if ( $hide_wp_version ) {

			// Remove admin wp version
			add_filter( 'update_footer', '__return_empty_string', 11 );

			// Remove WP version from RSS.
			add_filter( 'the_generator', '__return_empty_string' );

			add_filter( 'style_loader_src', [ &$this, 'remove_version_scripts_styles' ], PHP_INT_MAX );
			add_filter( 'script_loader_src', [ &$this, 'remove_version_scripts_styles' ], PHP_INT_MAX );
		}
	}

	// ------------------------------------------------------

	/**
	 * Remove version from scripts and styles
	 *
	 * @param $src
	 *
	 * @return false|mixed|string
	 */
	public function remove_version_scripts_styles( $src ) {
		if ( $src && str_contains( $src, 'ver=' ) ) {
			$src = remove_query_arg( 'ver', $src );
		}

		return $src;
	}

	// ------------------------------------------------------

	/**
	 * Disable the WordPress feed.
	 *
	 * @return void
	 */
	private function _disable_RSSFeed() {
		$rss_feed_off = $this->security_options['rss_feed_off'] ?? 0;

		// If the option is already enabled.
		if ( $rss_feed_off ) {
			add_action( 'do_feed', [ &$this, 'disable_feed' ], 1 );
			add_action( 'do_feed_rdf', [ &$this, 'disable_feed' ], 1 );
			add_action( 'do_feed_rss', [ &$this, 'disable_feed' ], 1 );
			add_action( 'do_feed_rss2', [ &$this, 'disable_feed' ], 1 );
			add_action( 'do_feed_atom', [ &$this, 'disable_feed' ], 1 );
			add_action( 'do_feed_rss2_comments', [ &$this, 'disable_feed' ], 1 );
			add_action( 'do_feed_atom_comments', [ &$this, 'disable_feed' ], 1 );

			remove_action( 'wp_head', 'feed_links_extra', 3 ); // remove comments feed.
			remove_action( 'wp_head', 'feed_links', 2 );
		}
	}

	// ------------------------------------------------------

	/**
	 * Disables the WordPress feed.
	 *
	 * @return void
	 */
	public function disable_feed() {
		wp_redirect( Helper::home() );
	}

	// ------------------------------------------------------

	/**
	 * Add readme hooks.
	 *
	 * @return void
	 */
	private function _remove_ReadMe() {
		$remove_readme = $this->security_options['remove_readme'] ?? 0;
		if ( $remove_readme ) {

			// Add action to delete the README on WP core update, if option is set.
			$readme = new Readme();
			add_action( '_core_updated_successfully', [ &$readme, 'delete_readme' ] );
		}
	}

	// ------------------------------------------------------

	/**
	 * XML-RPC
	 *
	 * @return void
	 */
	private function _disable_XMLRPC() {
		$xml_rpc_off = $this->security_options['xml_rpc_off'] ?? 0;
		if ( $xml_rpc_off ) {

			// Disable XML-RPC authentication
			if ( is_admin() ) {
				update_option( 'default_ping_status', 'closed' );
			}

			add_filter( 'xmlrpc_enabled', '__return_false' );
			add_filter( 'pre_update_option_enable_xmlrpc', '__return_false' );
			add_filter( 'pre_option_enable_xmlrpc', '__return_zero' );

			/**
			 * unset XML-RPC headers
			 *
			 * @param array $headers The array of wp headers
			 */
			add_filter( 'wp_headers', function ( $headers ) {
				if ( isset( $headers['X-Pingback'] ) ) {
					unset( $headers['X-Pingback'] );
				}

				return $headers;
			}, 10, 1 );

			/**
			 * unset XML-RPC methods for ping-backs
			 *
			 * @param array $methods The array of xml-rpc methods
			 */
			add_filter( 'xmlrpc_methods', function ( $methods ) {
				unset( $methods['pingback.ping'] );
				unset( $methods['pingback.extensions.getPingbacks'] );

				return $methods;
			}, 10, 1 );
		}
	}
}

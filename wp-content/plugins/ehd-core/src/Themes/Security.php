<?php

namespace EHD_Themes;

use EHD_Cores\Helper;
use EHD_Libs\Readme_File;

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

		/** Security */
		$this->_disable_XMLRPC();
		$this->_remove_ReadMe();
		$this->_disable_RSSFeed();
	}

	// ------------------------------------------------------

	/**
	 * @return void
	 */
	private function _disable_RSSFeed() {
		$rss_feed_off = $this->security_options['rss_feed_off'] ? 1 : 0;

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
	 * @return void
	 */
	private function _remove_ReadMe() {
		$remove_readme = $this->security_options['remove_readme'] ? 1 : 0;
		if ( $remove_readme ) {

			// Add action to delete the README on WP core update, if option is set.
			$readme = new Readme_File();
			add_action( '_core_updated_successfully', [ &$readme, 'delete_readme' ] );
		}
	}

	// ------------------------------------------------------

	/**
	 * @return void
	 */
	private function _disable_XMLRPC() {
		$xml_rpc_off = $this->security_options['xml_rpc_off'] ? 1 : 0;
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

<?php

namespace EHD_Themes;

use EHD_Cores\Helper;

/**
 * Options Class
 *
 * @author eHD
 */

\defined('ABSPATH') || die;

final class Security {
	public function __construct() {

		$this->_disable_XMLRPC();


	}

	// ------------------------------------------------------

	/**
	 * @return void
	 */
	private function _disable_XMLRPC() {

		// Array containing all plugins using XML-RPC.
		$xml_rpc_plugins = apply_filters( 'ehd_xml_rpc_plugins', [] );
		$active_plugins = Helper::getOption( 'active_plugins', [] );

		$has_active_plugin = false;
		foreach ( $active_plugins as $plugin ) {
			if ( in_array( $plugin, $xml_rpc_plugins ) ) {
				$has_active_plugin = true;
				break;
			}
		}

		// the plugin is not in the list.
		if ( ! $has_active_plugin ) {

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

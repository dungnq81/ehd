<?php

namespace EHD_Libs\Security;

use EHD_Cores\Abstract_Htaccess;
use EHD_Cores\Helper;

class Xmlrpc extends Abstract_Htaccess {

	/**
	 * Array containing all plugins using XML-RPC.
	 *
	 * @var array All known plugins using XML-RPC.
	 */
	private array $xml_rpc_plugin_list = [
		'jetpack/jetpack.php',
	];

	/**
	 * The path to the htaccess template.
	 *
	 * @var string
	 */
	public string $template = 'xml-rpc.tpl';

	/**
	 * Regular expressions to check if the rules are enabled.
	 *
	 * @var array Regular expressions to check if the rules are enabled.
	 */
	public array $rules = [
		'enabled'     => '/\#\s+eHD\s+XML-RPC\s+Disable/si',
		'disabled'    => '/\#\s+eHD\s+XML-RPC\s+Disable(.+?)\#\s+eHD\s+XML-RPC\s+Disable\s+END(\n)?/ims',
		'disable_all' => '/\#\s+eHD\s+XML-RPC\s+Disable(.+?)\#\s+eHD\s+XML-RPC\s+Disable\s+END(\n)?/ims',
	];

	/**
	 * Check if we have active plugins that are using XML-RPC.
	 *
	 * @return array The array containing known active plugins using XML-RPC or empty array if none are active.
	 */
	public function plugins_using_xml_rpc(): array {
		// Get the list of active plugins.
		$active_plugins = Helper::getOption( 'active_plugins', false, false );

		// The array that will contain conflicting plugins if there are any.
		$maybe_conflict = [];

		// Check if the function exists, since we are connecting a bit early.
		if ( ! function_exists( 'get_plugin_data' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		// Loop trough active plugins and check if any is present in the known plugins that use XML-RPC.
		foreach ( $active_plugins as $key => $plugin ) {
			// Continue if the plugin is not in the list.
			if ( ! in_array( $plugin, $this->xml_rpc_plugin_list ) ) {
				continue;
			}

			// Get the plugin data and push it to an array.
			$plugin_data      = get_plugin_data( ABSPATH . 'wp-content/plugins/' . $plugin );
			$maybe_conflict[] = $plugin_data['Name'];
		}

		// Return the names of all active plugins that use XML-RPC or empty array to be consistent for the FE management.
		return $maybe_conflict;
	}
}
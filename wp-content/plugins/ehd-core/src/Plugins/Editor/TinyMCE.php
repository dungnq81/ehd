<?php

namespace EHD_Plugins\Editor;

\defined( 'ABSPATH' ) || die;

/**
 * TinyMCE Plugin
 *
 * @author   WEBHD
 */
final class TinyMCE {
	public function __construct() {

		add_filter( 'mce_buttons', [ &$this, 'tinymce_add_table_button' ] );
		add_filter( 'mce_external_plugins', [ &$this, 'tinymce_add_table_plugin' ] );
	}

	/**
	 * @param $buttons
	 *
	 * @return mixed
	 */
	public function tinymce_add_table_button( $buttons ) {
		array_push( $buttons, 'separator', 'table' );
		array_push( $buttons, 'separator', 'codesample' );
		array_push( $buttons, 'separator', 'toc' );
		//array_push( $buttons, 'separator', 'fullscreen' );

		return $buttons;
	}

	/**
	 * @param $plugins
	 *
	 * @return mixed
	 */
	public function tinymce_add_table_plugin( $plugins ) {
		$plugins['table']      = EHD_PLUGIN_SRC_URL . 'Plugins/Editor/tinymce/table/plugin.min.js';
		$plugins['codesample'] = EHD_PLUGIN_SRC_URL . 'Plugins/Editor/tinymce/codesample/plugin.min.js';
		$plugins['toc']        = EHD_PLUGIN_SRC_URL . 'Plugins/Editor/tinymce/toc/plugin.min.js';
		//$plugins['fullscreen'] = EHD_PLUGIN_SRC_URL . 'Plugins/Editor/tinymce/fullscreen/plugin.min.js';

		return $plugins;
	}
}
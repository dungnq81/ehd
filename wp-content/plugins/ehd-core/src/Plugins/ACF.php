<?php

namespace EHD_Plugins;

\defined( 'ABSPATH' ) || die;

/**
 * Advanced Custom Fields
 *
 * @author   WEBHD
 */
final class ACF {

	public function __construct() {

		add_filter( 'acf/format_value/type=textarea', 'wp_kses_post', 10, 1 );
		add_filter( 'acf/fields/wysiwyg/toolbars', [ &$this, 'wysiwyg_toolbars' ], 11, 1 );
	}

	// -------------------------------------------------------------

	/**
	 * @param $toolbars
	 *
	 * @return mixed
	 */
	public function wysiwyg_toolbars( $toolbars ) {
		// Add a new toolbar called "Minimal" - this toolbar has only 1 row of buttons
		$toolbars['Minimal']    = [];
		$toolbars['Minimal'][1] = [
			'formatselect',
			'bold',
			//'italic',
			'underline',
			'link',
			'unlink',
			'forecolor',
			'blockquote',
			'table'
		];

		// remove the 'Basic' toolbar completely (if you want)
		//unset( $toolbars['Full'] );
		//unset( $toolbars['Basic'] );

		return $toolbars;
	}
}

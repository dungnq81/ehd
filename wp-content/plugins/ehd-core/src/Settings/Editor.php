<?php

namespace EHD_Settings;

use EHD_Cores\Helper;

/**
 * Editor Class
 *
 * @author eHD
 */

\defined('ABSPATH') || die;

final class Editor {
	public function __construct() {
		add_action( 'wp_enqueue_scripts', [ &$this, 'enqueue_scripts' ], 98 );
	}

	/**
	 * @return void
	 */
	public function enqueue_scripts() {
		$block_editor_options = Helper::getOption( 'block_editor__options' );
		$block_style_off = $block_editor_options['block_style_off'] ?? '';

		/** Remove block CSS */
		if ( $block_style_off ) {
			wp_dequeue_style( 'wp-block-library' );
			wp_dequeue_style( 'wp-block-library-theme' );

			// Remove WooCommerce block CSS
			if ( class_exists( '\WooCommerce' ) ) {
				wp_deregister_style( 'wc-blocks-vendors-style' );
				wp_deregister_style( 'wc-block-style' );
			}
		}

		$use_widgets_block_editor_off           = $block_editor_options['use_widgets_block_editor_off'] ?? '';
		$gutenberg_use_widgets_block_editor_off = $block_editor_options['gutenberg_use_widgets_block_editor_off'] ?? '';
		$use_block_editor_for_post_type_off     = $block_editor_options['use_block_editor_for_post_type_off'] ?? '';

		// Disables the block editor from managing widgets.
		if ( $use_widgets_block_editor_off ) {
			add_filter( 'use_widgets_block_editor', '__return_false' );
		}

		// Disables the block editor from managing widgets in the Gutenberg plugin.
		if ( $gutenberg_use_widgets_block_editor_off ) {
			add_filter( 'gutenberg_use_widgets_block_editor', '__return_false' );
		}

		// Use Classic Editor - Disable Gutenberg Editor
		if ( $use_block_editor_for_post_type_off ) {
			add_filter( 'use_block_editor_for_post_type', '__return_false' );
		}
	}
}
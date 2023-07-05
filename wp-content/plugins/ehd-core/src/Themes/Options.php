<?php

namespace EHD_Themes;

use EHD_Cores\Helper;
use PHPMailer\PHPMailer\Exception;
use MatthiasMullie\Minify;

\defined( 'ABSPATH' ) || die;

/**
 * Options Class
 *
 * @author eHD
 */
final class Options {
	public function __construct() {

		/** Custom Scripts */
		add_action( 'wp_head', [ &$this, 'header_scripts__hook' ], 99 ); // header scripts
		add_action( 'wp_body_open', [ &$this, 'body_scripts_top__hook' ], 99 ); // body scripts - TOP

		add_action( 'wp_footer', [ &$this, 'footer_scripts__hook' ], 1 ); // footer scripts
		add_action( 'wp_footer', [ &$this, 'body_scripts_bottom__hook' ], 998 ); // body scripts - BOTTOM

		/** SMTP Settings */
		if ( Helper::smtpConfigured() ) {
			add_action( 'phpmailer_init', [ &$this, 'setup_phpmailer_init' ], 11 );
		}

		/** Aspect Ratio */
		add_action( 'wp_enqueue_scripts', [ &$this, 'aspect_ratio_enqueue_scripts' ], 11 );
		add_filter( 'ehd_aspect_ratio_post_type', [ &$this, 'aspect_ratio_post_type_default' ], 98, 1 );

		/** Contact info */

		/** Contact Button */

		/** Block editor */
		add_action( 'wp_enqueue_scripts', [ &$this, 'editor_enqueue_scripts' ], 98 );

		/** Security */
		( new Security() );

		/** WooCommerce */

		/** Custom CSS */
		// add_action( 'wp_enqueue_scripts', [ &$this, 'header_custom_css' ], 99 );
		add_action( 'wp_head', [ &$this, 'header_custom_css' ], 98 );
	}

	// ------------------------------------------------------

	/**
	 * @return void
	 */
	public function header_custom_css() {
		/** Custom CSS */
		$css = Helper::getCustomPostContent( 'ehd_css', false );

		if ( $css ) {
			if ( ! WP_DEBUG ) {
				$minifier = new Minify\CSS();
				$minifier->add( $css );
				$css = $minifier->minify();
			}

			echo "<style id='custom-style-inline-css'>" . $css . "</style>";
			// wp_add_inline_style( 'app-style', $css );
		}
	}

	// ------------------------------------------------------

	/**
	 * @return void
	 */
	public function editor_enqueue_scripts() {
		$block_editor_options = Helper::getOption( 'block_editor__options', false, false );

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

	// ------------------------------------------------------

	/**
	 * @return void
	 */
	public function aspect_ratio_enqueue_scripts() {
		$classes = [];
		$styles  = '';

		$ar_post_type_list = apply_filters( 'ehd_aspect_ratio_post_type', [] );

		foreach ( $ar_post_type_list as $ar_post_type ) {
			$ratio_obj   = Helper::getAspectRatioClass( $ar_post_type, 'aspect_ratio__options' );
			$ratio_class = $ratio_obj->class ?? '';
			$ratio_style = $ratio_obj->style ?? '';

			if ( ! in_array( $ratio_class, $classes ) && $ratio_style ) {
				$classes[] = $ratio_class;
				$styles    .= $ratio_style;
			}
		}

		if ( $styles ) {
			wp_add_inline_style( 'ehd-core-style', $styles );
		}
	}

	/**
	 * @param array $arr
	 *
	 * @return array
	 */
	public function aspect_ratio_post_type_default( array $arr ): array {
		$new_arr = array_merge( $arr, [ 'post' ] );
		if ( class_exists( '\WooCommerce' ) ) {
			$new_arr = array_merge( $new_arr, [ 'product' ] );
		}

		return $new_arr;
	}

	// ------------------------------------------------------

	/**
	 * @param $phpmailer
	 *
	 * @return void
	 * @throws Exception
	 */
	public function setup_phpmailer_init( $phpmailer ): void {
		Helper::PHPMailerInit( $phpmailer, 'smtp__options' );
	}

	// ------------------------------------------------------

	/**
	 * Header scripts
	 *
	 * @return void
	 */
	public function header_scripts__hook(): void {
		$html_header = Helper::getCustomPostContent( 'html_header', true );
		if ( $html_header ) {
			echo $html_header;
		}
	}

	/**
	 * Body scripts - TOP
	 *
	 * @return void
	 */
	public function body_scripts_top__hook() {
		$html_body_top = Helper::getCustomPostContent( 'html_body_top', true );
		if ( $html_body_top ) {
			echo $html_body_top;
		}
	}

	/**
	 * Footer scripts
	 *
	 * @return void
	 */
	public function footer_scripts__hook() {
		$html_footer = Helper::getCustomPostContent( 'html_footer', true );
		if ( $html_footer ) {
			echo $html_footer;
		}
	}

	/**
	 * Body scripts - BOTTOM
	 *
	 * @return void
	 */
	public function body_scripts_bottom__hook() {
		$html_body_bottom = Helper::getCustomPostContent( 'html_body_bottom', true );
		if ( $html_body_bottom ) {
			echo $html_body_bottom;
		}
	}
}
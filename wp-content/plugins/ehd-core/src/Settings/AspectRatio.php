<?php

namespace EHD_Settings;

use EHD_Cores\Helper;

/**
 * Aspect Ratio Class
 *
 * @author eHD
 */

\defined( 'ABSPATH' ) || die;

final class AspectRatio {
	public function __construct() {

		// wp_enqueue_scripts
		add_action( 'wp_enqueue_scripts', [ &$this, 'enqueue_scripts' ], 11 );

		// set filter default
		add_filter( 'ehd_aspect_ratio_post_type', [ &$this, 'aspect_ratio_post_type_default' ], 98, 1);
	}

	/**
	 * @return void
	 */
	public function enqueue_scripts() {
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
}

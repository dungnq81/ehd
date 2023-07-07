<?php

namespace EHD_Plugins;

\defined( 'ABSPATH' ) || die;

/**
 * Contact Form 7
 *
 * @author   WEBHD
 */
final class CF7 {
	public function __construct() {
		// remove <p> and <br> contact form 7 plugin
		add_filter( 'wpcf7_autop_or_not', '__return_false' );

		// dynamic taxonomy select
		add_filter( 'wpcf7_form_tag', [ &$this, 'dynamic_select_terms' ], 10, 1 );
	}

	/**
	 * Dynamic Select Terms for Contact Form 7
	 *
	 * @usage [select name taxonomy:{$taxonomy} ...]
	 * @param array $tag
	 *
	 * @return array $tag
	 */
	public function dynamic_select_terms( $tag ): array {
		// Only run on select lists
		if ( 'select' !== $tag['type'] && ( 'select*' !== $tag['type'] ) ) {
			return $tag;
		} else if ( empty( $tag['options'] ) ) {
			return $tag;
		}
		$term_args = [];

		// Loop options to look for our custom options
		foreach ( $tag['options'] as $option ) {
			$matches = explode( ':', $option );
			if ( ! empty( $matches ) ) {
				switch ( $matches[0] ) {
					case 'taxonomy':
						$term_args['taxonomy'] = $matches[1];
						break;
					case 'parent':
						$term_args['parent'] = intval( $matches[1] );
						break;
				}
			}
		}

		// Ensure we have a term arguments to work with
		if ( empty( $term_args ) ) {
			return $tag;
		}

		// Merge dynamic arguments with static arguments
		$term_args = array_merge( $term_args, [
			'hide_empty'   => false,
			'hierarchical' => 1,
		] );
		$terms     = get_terms( $term_args );

		// Add terms to values
		if ( ! empty( $terms ) && ! is_wp_error( $term_args ) ) {
			foreach ( $terms as $term ) {
				$tag['values'][] = $term->name;
			}
		}

		return $tag;
	}
}

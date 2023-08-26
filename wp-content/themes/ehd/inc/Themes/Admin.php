<?php

namespace EHD\Themes;

use EHD_Cores\Helper;

/**
 * Admin Class
 *
 * @author EHD
 */

\defined( 'ABSPATH' ) || die;

final class Admin {
	public function __construct() {

		/** Admin footer text */
		add_filter( 'admin_footer_text', function () {
			printf( '<span id="footer-thankyou">%1$s <a href="https://webhd.vn" target="_blank">%2$s</a>.&nbsp;</span>', __( 'Powered by', EHD_TEXT_DOMAIN ), EHD_AUTHOR );
		} );

		add_action( 'admin_init', [ &$this, 'admin_init' ], 10 );
		add_action( 'admin_menu', [ &$this, 'dashboard_meta_box' ], 11 );
	}

	/** ---------------------------------------- */

	/**
	 * Add admin column
	 */
	public function admin_init(): void {

		// https://wordpress.stackexchange.com/questions/77532/how-to-add-the-category-id-to-admin-page
		$taxonomy_arr = [
			'category',
			'post_tag',
		];
		$taxonomy_arr = apply_filters( 'ehd_term_row_actions', $taxonomy_arr );

		foreach ( $taxonomy_arr as $term ) {
			add_filter( "{$term}_row_actions", [ &$this, 'term_action_links' ], 11, 2 );
		}

		// customize row_actions
		$post_type_arr = [
			'user',
			'post',
			'page',
		];
		$post_type_arr = apply_filters( 'ehd_post_row_actions', $post_type_arr );

		foreach ( $post_type_arr as $post_type ) {
			add_filter( "{$post_type}_row_actions", [ &$this, 'post_type_action_links' ], 11, 2 );
		}

		// customize post page
		add_filter( 'manage_posts_columns', [ &$this, 'post_header' ], 11, 1 );
		add_filter( 'manage_posts_custom_column', [ &$this, 'post_column' ], 11, 2 );

		add_filter( 'manage_pages_columns', [ &$this, 'post_header' ], 5, 1 );
		add_filter( 'manage_pages_custom_column', [ &$this, 'post_column' ], 5, 2 );

		// exclude post columns
		$exclude_thumb_posts = [];
		$exclude_thumb_posts = apply_filters( 'ehd_post_exclude_columns', $exclude_thumb_posts );

		foreach ( $exclude_thumb_posts as $post ) {
			add_filter( "manage_{$post}_posts_columns", [ $this, 'post_exclude_header' ], 12, 1 );
		}

		// thumb terms
		$thumb_terms = [
			'category',
			'post_tag',
		];
		$thumb_terms = apply_filters( 'ehd_term_columns', $thumb_terms );

		foreach ( $thumb_terms as $term ) {
			add_filter( "manage_edit-{$term}_columns", [ &$this, 'term_header' ], 11, 1 );
			add_filter( "manage_{$term}_custom_column", [ &$this, 'term_column' ], 11, 3 );
		}
	}

	/** ---------------------------------------- */

	/**
	 * @param $columns
	 *
	 * @return array|mixed
	 */
	public function term_header( $columns ) {
		if ( class_exists( '\ACF' ) ) {

			// thumb
			$thumb   = [
				"term_thumb" => sprintf( '<span class="wc-image tips">%1$s</span>', __( "Thumb", EHD_TEXT_DOMAIN ) ),
			];
			$columns = Helper::insertBefore( 'name', $columns, $thumb );

			// order
			$menu_order = [
				'term_order' => sprintf( '<span class="term-order tips">%1$s</span>', __( "Order", EHD_TEXT_DOMAIN ) ),
			];

			$columns = array_merge( $columns, $menu_order );
		}

		return $columns;
	}

	/** ---------------------------------------- */

	/**
	 * @param $out
	 * @param $column
	 * @param $term_id
	 *
	 * @return int|mixed|string|null
	 */
	public function term_column( $out, $column, $term_id ) {
		switch ( $column ) {
			case 'term_thumb':
				$term_thumb = Helper::acfTermThumb( $term_id, $column, "thumbnail", true );
				if ( ! $term_thumb ) {
					$term_thumb = Helper::placeholderSrc();
				}

				return $out = $term_thumb;
				break;

			case 'term_order':
				if ( class_exists( '\ACF' ) ) {
					$term_order = \get_field( 'term_order', get_term( $term_id ) );

					return $out = $term_order ?: 0;
				}

				return $out = 0;
				break;

			default:
				return $out;
				break;
		}
	}

	/** ---------------------------------------- */

	/**
	 * @param $columns
	 *
	 * @return mixed
	 */
	public function post_exclude_header( $columns ) {
		unset( $columns['post_thumb'] );

		return $columns;
	}

	/** ---------------------------------------- */

	/**
	 * @param $columns
	 *
	 * @return array
	 */
	public function post_header( $columns ): array {
		$in = [
			"post_thumb" => sprintf( '<span class="wc-image tips">%1$s</span>', __( "Thumb", EHD_TEXT_DOMAIN ) ),
		];

		return Helper::insertBefore( 'title', $columns, $in );
	}

	/** ---------------------------------------- */

	/**
	 * @param $column_name
	 * @param $post_id
	 */
	public function post_column( $column_name, $post_id ): void {
		switch ( $column_name ) {
			case 'post_thumb':
				$post_type = get_post_type( $post_id );
				if ( ! in_array( $post_type, [ 'video', 'product' ] ) ) {
					if ( ! $thumbnail = get_the_post_thumbnail( $post_id, 'thumbnail' ) ) {
						$thumbnail = Helper::placeholderSrc();
					}
					echo $thumbnail;
				} else if ( 'video' == $post_type ) {
					if ( has_post_thumbnail( $post_id ) ) {
						echo get_the_post_thumbnail( $post_id, 'thumbnail' );
					} else if ( function_exists( 'get_field' ) && $url = \get_field( 'url', $post_id ) ) {
						$img_src = Helper::youtubeImage( esc_url( $url ), [ 'default' ] );
						echo "<img alt src=\"" . $img_src . "\" />";
					}
				}

				break;

			default:
				break;
		}
	}

	/** ---------------------------------------- */

	/**
	 * @param $actions
	 * @param $_object
	 *
	 * @return mixed
	 */
	public function post_type_action_links( $actions, $_object ) {
		if ( ! in_array( $_object->post_type, [ 'product', 'site-review' ] ) ) {
			Helper::prepend( $actions, 'Id:' . $_object->ID, 'action_id' );
		}

		return $actions;
	}

	/** ---------------------------------------- */

	/**
	 * @param $actions
	 * @param $_object
	 *
	 * @return mixed
	 */
	public function term_action_links( $actions, $_object ) {
		Helper::prepend( $actions, 'Id: ' . $_object->term_id, 'action_id' );

		return $actions;
	}

	/** ---------------------------------------- */

	/**
	 * Remove dashboard widgets
	 *
	 * @return void
	 */
	public function dashboard_meta_box(): void {
		/*Incoming Links Widget*/
		remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );

		/*Remove WordPress Events and News*/
		remove_meta_box( 'dashboard_primary', 'dashboard', 'normal' );
	}
}

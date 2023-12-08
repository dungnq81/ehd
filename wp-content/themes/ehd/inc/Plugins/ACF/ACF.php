<?php

namespace EHD\Plugins\ACF;

use EHD_Cores\Helper;

\defined( 'ABSPATH' ) || die;

/**
 * Advanced Custom Fields
 *
 * @author WEBHD
 */
final class ACF {
	public function __construct() {

		// Hide the ACF Admin UI
		//add_filter( 'acf/settings/show_admin', '__return_false' );

		// Auto required fields
		$fields_dir = __DIR__ . DIRECTORY_SEPARATOR . 'fields';
		Helper::FQN_Load( $fields_dir, true );

		add_filter( 'wp_nav_menu_objects', [ &$this, 'wp_nav_menu_objects' ], 11, 2 );
	}

	// -------------------------------------------------------------

	/**
	 * @param $items
	 * @param $args
	 *
	 * @return mixed
	 */
	public function wp_nav_menu_objects( $items, $args ): mixed {
		foreach ( $items as &$item ) {

			$title = $item->title;
			$ACF   = \get_fields( $item ) ?? [];

			if ( $ACF ) {

				$ACF = Helper::toObject( $ACF );

				$menu_mega             = $ACF->menu_mega ?? false;
				$menu_glyph            = $ACF->menu_glyph ?? '';
				$menu_image            = $ACF->menu_image ?? '';
				$menu_label_text       = $ACF->menu_label_text ?? '';
				$menu_label_color      = $ACF->menu_label_color ?? '';
				$menu_label_background = $ACF->menu_label_background ?? '';

				if ( $menu_mega ) {
					$item->classes[] = 'menu-mega menu-masonry';
				}

				if ( $menu_glyph ) {
					$item->classes[] = 'menu-glyph';
					$title           = '<span data-glyph="' . esc_attr( $menu_glyph ) . '">' . $title . '</span>';
				}

				if ( $menu_image ) {
					$item->classes[] = 'menu-thumb';
					$title           = wp_get_attachment_image( $menu_image, 'thumbnail' ) . $title;
				}

				if ( $menu_label_text ) {
					$item->classes[] = 'menu-label';

					$_css = '';
					if ( $menu_label_color ) {
						$_css .= 'color:' . $menu_label_color . ';';
					}
					if ( $menu_label_background ) {
						$_css .= 'background-color:' . $menu_label_background . ';';
					}

					$_style = $_css ? ' style="' . Helper::CSS_Minify( $_css, true ) . '"' : '';
					$title  .= '<sup' . $_style . '>' . $menu_label_text . '</sup>';
				}

				$item->title = $title;
				unset( $ACF );
			}
		}

		return $items;
	}
}

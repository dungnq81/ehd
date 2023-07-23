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
		add_filter( 'wp_nav_menu_objects', [ &$this, 'wp_nav_menu_objects' ], 11, 2 );

		// auto required fields
		// **\wp-content\themes\ehd\inc\Plugins\ACF\fields
		$fields_dir = __DIR__ . DIRECTORY_SEPARATOR . 'fields';
		Helper::FQN_Load( $fields_dir, true, false );
	}

	// -------------------------------------------------------------

	/**
	 * @param $items
	 * @param $args
	 *
	 * @return mixed
	 */
	public function wp_nav_menu_objects( $items, $args ) {
		foreach ( $items as &$item ) {

			$title  = $item->title;
			$fields = \get_fields( $item ) ?? '';

			if ( $fields ) {
				$fields = Helper::toObject( $fields );

				//...
				if ( $fields->icon_svg ?? false ) {
					$item->classes[] = 'icon-menu';
					$title           = $fields->icon_svg . '<span>' . $item->title . '</span>';
				} else if ( $fields->icon_image ?? false ) {
					$item->classes[] = 'thumb-menu';
					$title           = '<img width="50px" height="50px" alt="' . esc_attr( $item->title ) . '" src="' . Helper::attachmentImageSrc( $fields->icon_image ) . '" loading="lazy" />' . '<span>' . $item->title . '</span>';
				} else if ( $fields->icon_glyph ?? false ) {
					$item->classes[] = 'glyph-menu';
					$title           = '<span data-glyph="' . $fields->icon_glyph . '">' . $item->title . '</span>';
				}

				//...
				if ( $fields->label_text ?? false ) {

					$_str = '';
					if ( $fields->label_color ?? false ) {
						$_str .= 'color:' . $fields->label_color . ';';
					}
					if ( $fields->label_background ?? false ) {
						$_str .= 'background-color:' . $fields->label_background . ';';
					}

					$_style = $_str ? ' style="' . $_str . '"' : '';
					$title  .= '<sup' . $_style . '>' . $fields->label_text . '</sup>';
				}

				$item->title = $title;
				unset( $fields );
			}
		}

		return $items;
	}
}

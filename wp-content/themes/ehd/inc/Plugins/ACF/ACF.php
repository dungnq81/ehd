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
		add_action( 'wp_loaded', [ &$this, 'widget_css_classes_frontend' ] );
	}

	// -------------------------------------------------------------

	/**
	 * @return void
	 */
	public function widget_css_classes_frontend() {
		if ( ! is_admin() ) {
			add_filter( 'dynamic_sidebar_params', [ &$this, 'add_widget_classes' ], 10, 1 );
		}
	}

	// -------------------------------------------------------------

	/**
	 * Adds the classes to the widget in the front-end
	 *
	 * @param $params
	 *
	 * @return mixed
	 */
	public function add_widget_classes( $params ) {
		global $wp_registered_widgets;

		if ( ! isset( $params[0] ) ) {
			return $params;
		}

		//$arr_registered_widgets = wp_get_sidebars_widgets();
		//$this_id                = $params[0]['id'];
		$widget_id              = $params[0]['widget_id'];
		$widget_obj             = $wp_registered_widgets[ $widget_id ];

		// Skip old single widget (not using WP_Widget).
		if ( ! isset( $widget_obj['params'][0]['number'] ) ) {
			return $params;
		}

		$widget_num = $widget_obj['params'][0]['number'];
		$widget_opt = self::get_widget_opt( $widget_obj );

		// Add id.
		if ( ! empty( $widget_opt[ $widget_num ]['ids'] ) ) {
			$params[0]['before_widget'] = preg_replace(
				'/id="[^"]*/',
				"id=\"{$widget_opt[ $widget_num ]['ids']}",
				$params[0]['before_widget'],
				1
			);
		}

		// Remove empty ID attr.
		$params[0]['before_widget'] = str_replace( 'id="" ', '', $params[0]['before_widget'] );

		// classes array.
		$classes = [];

		// ACF attributes
		$ACF = \get_fields( 'widget_' . $widget_id );
		if ( ! empty( $ACF['css_class'] ) ) {
			$classes = explode( ' ', (string) $ACF['css_class'] );
		}

		// Only unique, non-empty values, separated by space, escaped for HTML attributes.
		$classes = esc_attr( implode( ' ', array_filter( array_unique( $classes ) ) ) );

		if ( ! empty( $classes ) ) {

			// Add the classes.
			$params[0]['before_widget'] = Helper::appendToAttribute(
				$params[0]['before_widget'],
				'class',
				$classes,
				true,
			);
		}

		return $params;
	}

	// -------------------------------------------------------------

	/**
	 * Get the widget option value.
	 *
	 * @param array $widget_obj
	 *
	 * @return mixed
	 */
	private static function get_widget_opt( array $widget_obj ) {
		$widget_opt = null;

		//$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );

		// Default callback.
		if ( null === $widget_opt ) {

			// Check if WP Page Widget is in use.
			global $post;

			$id = ( isset( $post->ID ) ? get_the_ID() : null );
			if ( isset( $id ) && get_post_meta( $id, '_customize_sidebars' ) ) {
				$custom_sidebarcheck = get_post_meta( $id, '_customize_sidebars' );
			}

			$option_name = '';
			if ( isset( $widget_obj['callback'][0]->option_name ) ) {
				$option_name = $widget_obj['callback'][0]->option_name;
			} elseif ( isset( $widget_obj['original_callback'][0]->option_name ) ) {
				$option_name = $widget_obj['original_callback'][0]->option_name;
			}

			if ( isset( $custom_sidebarcheck[0] ) && ( 'yes' === $custom_sidebarcheck[0] ) ) {
				$widget_opt = get_option( 'widget_' . $id . '_' . substr( $option_name, 7 ) );
			} elseif ( $option_name ) {

				// Default.
				$widget_opt = get_option( $option_name );
			}
		}

		return $widget_opt;
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

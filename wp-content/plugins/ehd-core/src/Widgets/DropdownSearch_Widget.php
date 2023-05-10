<?php

namespace EHD_Widgets;

use EHD_Cores\Abstract_Widget;
use EHD_Cores\Helper;

\defined( 'ABSPATH' ) || die;

class DropdownSearch_Widget extends Abstract_Widget {
	/**
	 * Sets up a widget instance.
	 */
	public function __construct() {
		$this->widget_description = __( 'Display the dropdown search box', EHD_PLUGIN_TEXT_DOMAIN );
		$this->widget_name        = __( 'W - Dropdown Search', EHD_PLUGIN_TEXT_DOMAIN );
		$this->settings           = [
			'title'     => [
				'type'  => 'text',
				'std'   => __( 'Search' ),
				'label' => __( 'Title' ),
			],
			'css_class' => [
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Css class', EHD_PLUGIN_TEXT_DOMAIN ),
			],
		];

		parent::__construct();
	}

	/**
	 * Creating widget front-end
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		if ( $this->get_cached_widget( $args ) ) {
			return;
		}

		$title     = apply_filters( 'widget_title', $this->get_instance_title( $instance ), $instance, $this->id_base );
		$css_class = ! empty( $instance['css_class'] ) ? sanitize_title( $instance['css_class'] ) : '';

		$shortcode_content = Helper::doShortcode(
			'dropdown_search',
			apply_filters(
				'dropdown_search_widget_shortcode_args',
				[
					'title' => $title,
					'class' => $this->widget_classname . ' ' . $css_class,
					'id'    => '',
				]
			)
		);

		echo $this->cache_widget( $args, $shortcode_content ); // WPCS: XSS ok.
	}
}

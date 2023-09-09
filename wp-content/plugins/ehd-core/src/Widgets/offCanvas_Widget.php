<?php

namespace EHD_Widgets;

use EHD_Cores\Abstract_Widget;
use EHD_Cores\Helper;

\defined( 'ABSPATH' ) || die;

class offCanvas_Widget extends Abstract_Widget {
	public function __construct() {
		$this->widget_description = __( 'Display offCanvas Button', EHD_PLUGIN_TEXT_DOMAIN );
		$this->widget_name        = __( 'W - OffCanvas Button', EHD_PLUGIN_TEXT_DOMAIN );
		$this->settings           = [
			'hide_if_desktop' => [
				'type'  => 'checkbox',
				'std'   => 1,
				'label' => __( 'Hide if desktop devices', EHD_PLUGIN_TEXT_DOMAIN ),
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

		$ACF = $this->acfFields( 'widget_' . $args['widget_id'] );
		$hide_if_desktop = empty( $instance['hide_if_desktop'] ) ? 0 : 1;
		$css_class = ! empty( $ACF->css_class ) ? sanitize_title( $ACF->css_class ) : '';

		$shortcode_content = Helper::doShortcode(
			'off_canvas_button',
			apply_filters(
				'off_canvas_widget_shortcode_args',
				[
					'title'           => '',
					'hide_if_desktop' => $hide_if_desktop,
					'class' => $css_class,
				]
			)
		);

		echo $this->cache_widget( $args, $shortcode_content ); // WPCS: XSS ok.
	}
}

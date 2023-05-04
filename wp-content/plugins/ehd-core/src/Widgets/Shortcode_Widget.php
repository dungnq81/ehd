<?php

namespace EHD_Widgets;

use EHD_Cores\Widget;

\defined( 'ABSPATH' ) || die;

class Shortcode_Widget extends Widget {
	public function __construct() {
		$this->widget_description = __( "Display a shortcode section.", EHD_PLUGIN_TEXT_DOMAIN );
		$this->widget_name        = __( 'W - Shortcode', EHD_PLUGIN_TEXT_DOMAIN );
		$this->settings           = [
			'title'             => [
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Title', EHD_PLUGIN_TEXT_DOMAIN ),
			],
			'desc'              => [
				'type'  => 'textarea',
				'std'   => '',
				'label' => __( 'Description', EHD_PLUGIN_TEXT_DOMAIN ),
				'desc'  => __( 'Short description of widget', EHD_PLUGIN_TEXT_DOMAIN ),
			],
			'shortcode_content' => [
				'type'  => 'textarea',
				'std'   => '',
				'label' => __( 'Shortcode Content', EHD_PLUGIN_TEXT_DOMAIN ),
				'desc'  => __( 'Display shortcode content.', EHD_PLUGIN_TEXT_DOMAIN ),
			],
			'css_class'         => [
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

		ob_start();

		?>

		<?php
		echo $this->cache_widget( $args, ob_get_clean() ); // WPCS: XSS ok.
	}
}

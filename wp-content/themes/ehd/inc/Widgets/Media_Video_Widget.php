<?php

namespace EHD\Widgets;

use EHD_Cores\Helper;

use WP_Widget_Media;
use WP_Widget_Media_Video;

\defined( 'ABSPATH' ) || die;

class Media_Video_Widget extends WP_Widget_Media_Video {

	/**
	 * @param $args
	 * @param $instance
	 *
	 * @return void
	 */
	public function widget( $args, $instance ) {
		$instance = wp_parse_args( $instance, wp_list_pluck( $this->get_instance_schema(), 'default' ) );

		// Short-circuit if no media is selected.
		if ( ! $this->has_content( $instance ) ) {
			return;
		}

		// ACF attributes
		$ACF = $this->acfFields( 'widget_' . $args['widget_id'] );

		$container = $ACF->container ?? false;
		$heading_tag = ! empty( $ACF->heading_tag ) ? $ACF->heading_tag : 'span';
		$heading_class = ! empty( $ACF->heading_class ) ? $ACF->heading_tag : 'heading-title';
		$css_class = ! empty( $ACF->css_class ) ? ' ' . $ACF->css_class : '';

		$args['before_widget'] = '<div class="section widget_media_video' . $css_class . '">';
		$args['after_widget'] = '</div>';

		echo $args['before_widget'];
		if ( $container ) {
			echo '<div class="grid-container">';
		}

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = $instance['title'] ?? '';
		if ( $title ) {
			$args['before_title'] = '<' . $heading_tag . ' class="' . $heading_class . '">';
			$args['after_title'] = '</' . $heading_tag . '>';

			echo $args['before_title'] . $title . $args['after_title'];
		}

		/**
		 * Filters the media widget instance prior to rendering the media.
		 *
		 * @param array $instance Instance data.
		 * @param array $args Widget args.
		 * @param WP_Widget_Media $widget Widget object.
		 */
		$instance = apply_filters( "widget_{$this->id_base}_instance", $instance, $args, $this );

		$this->render_media( $instance, $ACF );

		if ( $container ) {
			echo '</div>';
		}

		echo $args['after_widget'];
	}

	/**
	 * @param $id
	 *
	 * @return object
	 */
	protected function acfFields( $id ): ?object {
		if ( ! class_exists( '\ACF' ) ) {
			return (object) [];
		}

		$_fields = \get_fields( $id ) ?? [];

		return Helper::toObject( $_fields );
	}
}

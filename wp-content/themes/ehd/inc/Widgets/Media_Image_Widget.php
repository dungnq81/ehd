<?php

namespace EHD\Widgets;

use EHD_Cores\Helper;

use WP_Widget_Media;
use WP_Widget_Media_Image;

\defined( 'ABSPATH' ) || die;

class Media_Image_Widget extends WP_Widget_Media_Image {

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

		$args['before_widget'] = '<div class="section widget_media_image' . $css_class . '">';
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

		$this->acf_render_media( $instance, $ACF );

		if ( $container ) {
			echo '</div>';
		}

		echo $args['after_widget'];
	}

	/**
	 * @param $instance
	 * @param $ACF
	 *
	 * @return void
	 */
	public function acf_render_media( $instance, $ACF ): void {
		$instance = array_merge( wp_list_pluck( $this->get_instance_schema(), 'default' ), $instance );
		$instance = wp_parse_args(
			$instance,
			[
				'size' => 'thumbnail',
			]
		);

		$attachment = null;

		if ( $this->is_attachment_with_mime_type( $instance['attachment_id'], $this->widget_options['mime_type'] ) ) {
			$attachment = get_post( $instance['attachment_id'] );
		}

		if ( $attachment ) {
			$caption = '';
			if ( ! isset( $instance['caption'] ) ) {
				$caption = $attachment->post_excerpt;
			} elseif ( trim( $instance['caption'] ) ) {
				$caption = $instance['caption'];
			}

			$image_attributes = [
				'class' => sprintf( 'image wp-image-%d %s', $attachment->ID, $instance['image_classes'] ),
				'style' => 'max-width: 100%; height: auto;',
			];
			if ( ! empty( $instance['image_title'] ) ) {
				$image_attributes['title'] = $instance['image_title'];
			}

			if ( $instance['alt'] ) {
				$image_attributes['alt'] = $instance['alt'];
			}

			$size = $instance['size'];

			if ( 'custom' === $size || ! in_array( $size, array_merge( get_intermediate_image_sizes(), [ 'full' ] ), true ) ) {
				$size  = [ $instance['width'], $instance['height'] ];
				$width = $instance['width'];
			} else {
				$caption_size = _wp_get_image_size_from_meta( $instance['size'], wp_get_attachment_metadata( $attachment->ID ) );
				$width        = empty( $caption_size[0] ) ? 0 : $caption_size[0];
			}

			$image_attributes['class'] .= sprintf( ' attachment-%1$s size-%1$s', is_array( $size ) ? implode( 'x', $size ) : $size );
			$image = wp_get_attachment_image( $attachment->ID, $size, false, $image_attributes );

		} else {
			if ( empty( $instance['url'] ) ) {
				return;
			}

			$instance['size'] = 'custom';
			$caption          = $instance['caption'];
			$width            = $instance['width'];
			$classes          = 'image ' . $instance['image_classes'];
			if ( 0 === $instance['width'] ) {
				$instance['width'] = '';
			}
			if ( 0 === $instance['height'] ) {
				$instance['height'] = '';
			}

			$attr = [
				'class'    => $classes,
				'src'      => $instance['url'],
				'alt'      => $instance['alt'],
				'width'    => $instance['width'],
				'height'   => $instance['height'],
				'decoding' => 'async',
			];

			$loading_optimization_attr = wp_get_loading_optimization_attributes( 'img', $attr, 'widget_media_image' );
			$attr                      = array_merge( $attr, $loading_optimization_attr );
			$attr                      = array_map( 'esc_attr', $attr );

			$image = '<img';
			foreach ( $attr as $name => $value ) {
				$image .= ' ' . $name . '="' . $value . '"';
			}

			$image .= ' />';
		} // End if().

		$acf_att    = \get_fields( $attachment->ID ) ?? false;
		$src_mobile = $acf_att['src_mobile'] ?? '';

		$picture = '';
		if ( $src_mobile ) {
			$picture .= '<picture>';
			$picture .= '<source media="(max-width: 639.98px)" srcset="' . Helper::attachmentImageSrc( $src_mobile, 'medium' ) . '">';
			$picture .= $image;
			$picture .= '</picture>';

			$image = $picture;
		}

		if ( $caption ) {
			$caption = '<p class="caption-text">' . $caption . '</p>';
		}

		$url = '';
		if ( 'file' === $instance['link_type'] ) {
			$url = $attachment ? wp_get_attachment_url( $attachment->ID ) : $instance['url'];
		} elseif ( $attachment && 'post' === $instance['link_type'] ) {
			$url = get_attachment_link( $attachment->ID );
		} elseif ( 'custom' === $instance['link_type'] && ! empty( $instance['link_url'] ) ) {
			$url = $instance['link_url'];
		}

		if ( $url ) {
			$_a_title = '';
			if ( $instance['alt'] ) {
				$_a_title = 'aria-label="' . esc_attr( $instance['alt'] ) . '"';
			}

			$link = sprintf( '<a href="%1$s" %2$s', esc_url( $url ), $_a_title );
			if ( ! empty( $instance['link_classes'] ) ) {
				$link .= sprintf( ' class="%s"', esc_attr( $instance['link_classes'] ) );
			}
			if ( ! empty( $instance['link_rel'] ) ) {
				$link .= sprintf( ' rel="%s"', esc_attr( $instance['link_rel'] ) );
			}

			$_link_blank = $instance['link_target_blank'];
			$_blank = $ACF->target_blank ?? false;
			if ( $_blank ) {
				$_link_blank = true;
			}

			if ( ! empty( $_link_blank ) ) {
				$link .= ' target="_blank"';
			}

			$link .= '>';
			$link .= $image;
			$link .= $caption;
			$link .= '</a>';
			$image = wp_targeted_link_rel( $link );
		}

		echo '<div class="overlay">' . $image . '</div>';
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
<?php

namespace EHD\Widgets;

use EHD_Cores\Abstract_Media_Widget;

\defined( 'ABSPATH' ) || die;

class Media_Image_Widget extends Abstract_Media_Widget {

	/** Construct */
	public function __construct() {
		$this->widget_name        = __( 'W - Image', EHD_TEXT_DOMAIN );
		$this->widget_description = __( 'Displays an image.' );
		$this->widget_mime_type   = 'image';

		parent::__construct();

		$this->l10n = array_merge(
			$this->l10n,
			[
				'no_media_selected'          => __( 'No image selected' ),
				'add_media'                  => _x( 'Add Image', 'label for button in the image widget' ),
				'replace_media'              => _x( 'Replace Image', 'label for button in the image widget; should preferably not be longer than ~13 characters long' ),
				'edit_media'                 => _x( 'Edit Image', 'label for button in the image widget; should preferably not be longer than ~13 characters long' ),
				'missing_attachment'         => sprintf(
				/* translators: %s: URL to media library. */
					__( 'That image cannot be found. Check your <a href="%s">media library</a> and make sure it was not deleted.' ),
					esc_url( admin_url( 'upload.php' ) )
				),
				/* translators: %d: Widget count. */
				'media_library_state_multi'  => _n_noop( 'Image Widget (%d)', 'Image Widget (%d)' ),
				'media_library_state_single' => __( 'Image Widget' ),
			]
		);
	}

	/**
	 * Get schema for properties of a widget instance (item).
	 *
	 * @return array Schema for properties.
	 * @see WP_REST_Controller::get_item_schema()
	 * @see WP_REST_Controller::get_additional_fields()
	 * @link https://core.trac.wordpress.org/ticket/35574
	 */
	public function get_instance_schema(): array {
		return array_merge(
			[
				'size'   => [
					'type'        => 'string',
					'enum'        => array_merge( get_intermediate_image_sizes(), [ 'full', 'custom' ] ),
					'default'     => 'medium',
					'description' => __( 'Size' ),
				],
				'width'  => [ // Via 'customWidth', only when size=custom; otherwise via 'width'.
					'type'        => 'integer',
					'minimum'     => 0,
					'default'     => 0,
					'description' => __( 'Width' ),
				],
				'height' => [ // Via 'customHeight', only when size=custom; otherwise via 'height'.
					'type'        => 'integer',
					'minimum'     => 0,
					'default'     => 0,
					'description' => __( 'Height' ),
				],

				'caption'           => [
					'type'                  => 'string',
					'default'               => '',
					'sanitize_callback'     => 'wp_kses_post',
					'description'           => __( 'Caption' ),
					'should_preview_update' => false,
				],
				'alt'               => [
					'type'              => 'string',
					'default'           => '',
					'sanitize_callback' => 'sanitize_text_field',
					'description'       => __( 'Alternative Text' ),
				],
				'link_type'         => [
					'type'                  => 'string',
					'enum'                  => [ 'none', 'file', 'post', 'custom' ],
					'default'               => 'custom',
					'media_prop'            => 'link',
					'description'           => __( 'Link To' ),
					'should_preview_update' => true,
				],
				'link_url'          => [
					'type'                  => 'string',
					'default'               => '',
					'format'                => 'uri',
					'media_prop'            => 'linkUrl',
					'description'           => __( 'URL' ),
					'should_preview_update' => true,
				],
				'image_classes'     => [
					'type'                  => 'string',
					'default'               => '',
					'sanitize_callback'     => [ $this, 'sanitize_token_list' ],
					'media_prop'            => 'extraClasses',
					'description'           => __( 'Image CSS Class' ),
					'should_preview_update' => false,
				],
				'link_classes'      => [
					'type'                  => 'string',
					'default'               => '',
					'sanitize_callback'     => [ $this, 'sanitize_token_list' ],
					'media_prop'            => 'linkClassName',
					'should_preview_update' => false,
					'description'           => __( 'Link CSS Class' ),
				],
				'link_rel'          => [
					'type'                  => 'string',
					'default'               => '',
					'sanitize_callback'     => [ $this, 'sanitize_token_list' ],
					'media_prop'            => 'linkRel',
					'description'           => __( 'Link Rel' ),
					'should_preview_update' => false,
				],
				'link_target_blank' => [
					'type'                  => 'boolean',
					'default'               => false,
					'media_prop'            => 'linkTargetBlank',
					'description'           => __( 'Open link in a new tab' ),
					'should_preview_update' => false,
				],
				'image_title'       => [
					'type'                  => 'string',
					'default'               => '',
					'sanitize_callback'     => 'sanitize_text_field',
					'media_prop'            => 'title',
					'description'           => __( 'Image Title Attribute' ),
					'should_preview_update' => false,
				],

				/*
				 * There are two additional properties exposed by the PostImage modal
				 * that don't seem to be relevant, as they may only be derived read-only
				 * values:
				 * - originalUrl
				 * - aspectRatio
				 * - height (redundant when size is not custom)
				 * - width (redundant when size is not custom)
				 */
			],
			parent::get_instance_schema()
		);
	}

	/**
	 * Render the media on the frontend.
	 *
	 * @param array $instance Widget instance props.
	 */
	public function render_media( $instance ) {

	}
}
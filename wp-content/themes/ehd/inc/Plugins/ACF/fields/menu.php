<?php

use EHD_Cores\Helper;

add_action( 'acf/include_fields', function () {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$location                = [];
	$ehd_location_menu_items = apply_filters( 'ehd_location_menu_items', [] );

	foreach ( $ehd_location_menu_items as $menu_items ) {
		if ( $menu_items ) {
			$location[] = [
				[
					'param'    => 'nav_menu_item',
					'operator' => '==',
					'value'    => 'location/' . Helper::toString( $menu_items ),
				]
			];
		}
	}

	acf_add_local_field_group( [
		'key'                   => 'group_64bd0aafbaa3a',
		'title'                 => 'Attributes of Menu Items',
		'fields'                => [
			[
				'key'               => 'field_64bd134b8bca9',
				'label'             => 'Awesome Glyph',
				'name'              => 'menu_glyph',
				'aria-label'        => '',
				'type'              => 'text',
				'instructions'      => '',
				'required'          => 0,
				'conditional_logic' => 0,
				'wrapper'           => [
					'width' => '50',
					'class' => '',
					'id'    => '',
				],
				'default_value'     => '',
				'maxlength'         => '',
				'placeholder'       => '',
				'prepend'           => '',
				'append'            => '',
			],
			[
				'key'               => 'field_64bd0ab0ea1d7',
				'label'             => 'Image',
				'name'              => 'menu_image',
				'aria-label'        => '',
				'type'              => 'image',
				'instructions'      => '',
				'required'          => 0,
				'conditional_logic' => 0,
				'wrapper'           => [
					'width' => '',
					'class' => '',
					'id'    => '',
				],
				'return_format'     => 'id',
				'library'           => 'all',
				'min_width'         => '',
				'min_height'        => '',
				'min_size'          => '',
				'max_width'         => '',
				'max_height'        => '',
				'max_size'          => '',
				'mime_types'        => 'png,svg,jpg,jpeg,gif,webp',
				'preview_size'      => 'medium',
			],
			[
				'key'               => 'field_64bd139df7dfd',
				'label'             => 'Label',
				'name'              => 'menu_label_text',
				'aria-label'        => '',
				'type'              => 'text',
				'instructions'      => '',
				'required'          => 0,
				'conditional_logic' => 0,
				'wrapper'           => [
					'width' => '',
					'class' => '',
					'id'    => '',
				],
				'default_value'     => '',
				'maxlength'         => '',
				'placeholder'       => '"New", "Hot", "Featured" ...',
				'prepend'           => '',
				'append'            => '',
			],
			[
				'key'               => 'field_64bd13ccf7dfe',
				'label'             => 'Label Color',
				'name'              => 'menu_label_color',
				'aria-label'        => '',
				'type'              => 'color_picker',
				'instructions'      => '',
				'required'          => 0,
				'conditional_logic' => [
					[
						[
							'field'    => 'field_64bd139df7dfd',
							'operator' => '!=empty',
						],
					],
				],
				'wrapper'           => [
					'width' => '',
					'class' => '',
					'id'    => '',
				],
				'default_value'     => '',
				'enable_opacity'    => 1,
				'return_format'     => 'string',
			],
			[
				'key'               => 'field_64bd1488092dc',
				'label'             => 'Label Background',
				'name'              => 'menu_label_background',
				'aria-label'        => '',
				'type'              => 'color_picker',
				'instructions'      => '',
				'required'          => 0,
				'conditional_logic' => [
					[
						[
							'field'    => 'field_64bd139df7dfd',
							'operator' => '!=empty',
						],
					],
				],
				'wrapper'           => [
					'width' => '',
					'class' => '',
					'id'    => '',
				],
				'default_value'     => '',
				'enable_opacity'    => 1,
				'return_format'     => 'string',
			],
		],
		'location'              => $location,
		'menu_order'            => 0,
		'position'              => 'normal',
		'style'                 => 'default',
		'label_placement'       => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen'        => '',
		'active'                => true,
		'description'           => '',
		'show_in_rest'          => 1,
	] );
} );

<?php

use EHD_Cores\Helper;

\defined( 'ABSPATH' ) || die;

add_action( 'acf/include_fields', function () {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$location = [];
	$ehd_term_columns = apply_filters( 'ehd_term_columns', [] );

	foreach ( $ehd_term_columns as $term_column ) {
		if ( $term_column ) {
			$location[] = [
				[
					'param'    => 'taxonomy',
					'operator' => '==',
					'value'    => Helper::toString( $term_column ),
				]
			];
		}
	}

	$location = array_values( $location );

	acf_add_local_field_group( [
		'key'                   => 'group_64b3b263d91cc',
		'title'                 => 'Taxonomy',
		'fields'                => [
			[
				'key'               => 'field_64b3b26480fb4',
				'label'             => 'Thumbnail',
				'name'              => 'term_thumb',
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
				'mime_types'        => 'png,jpeg,jpg,svg,gif',
				'preview_size'      => 'thumbnail',
			],
			[
				'key'               => 'field_64bb499383eb3',
				'label'             => 'Order',
				'name'              => 'term_order',
				'aria-label'        => '',
				'type'              => 'number',
				'instructions'      => '',
				'required'          => 0,
				'conditional_logic' => 0,
				'wrapper'           => [
					'width' => '',
					'class' => '',
					'id'    => '',
				],
				'default_value'     => 0,
				'min'               => '',
				'max'               => '',
				'placeholder'       => '',
				'step'              => '',
				'prepend'           => '',
				'append'            => '',
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
}, 11 );

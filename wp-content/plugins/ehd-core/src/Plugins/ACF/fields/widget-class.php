<?php

\defined( 'ABSPATH' ) || die;

add_action( 'acf/include_fields', function () {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group( [
		'key'                   => 'group_64f1a97c27901',
		'title'                 => '__Widget_Classes',
		'fields'                => [
			[
				'key'               => 'field_64f1a97cb531b',
				'label'             => 'Css classes',
				'name'              => 'css_class',
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
				'placeholder'       => '',
				'prepend'           => '',
				'append'            => '',
			],
		],
		'location'              => [
			[
				[
					'param'    => 'widget',
					'operator' => '==',
					'value'    => 'all',
				],
			],
		],
		'menu_order'            => 999,
		'position'              => 'normal',
		'style'                 => 'default',
		'label_placement'       => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen'        => '',
		'active'                => true,
		'description'           => 'Thêm Css class vào tất cả widget',
		'show_in_rest'          => 0,
	] );
} );



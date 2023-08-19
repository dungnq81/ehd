<?php

\defined( 'ABSPATH' ) || die;

/** ----------------------------------------------- */

if ( ! function_exists( 'dump_query' ) ) {
	/**
	 * @return void
	 */
	function dump_query() {
		global $wpdb;
		if ( function_exists( 'dump' ) ) {
			dump( $wpdb->last_query );
		} else {
			var_dump( $wpdb->last_query );
		}
	}
}

/** ----------------------------------------------- */

if ( ! function_exists( 'sanitize_image' ) ) {
	/**
	 * @param $file
	 * @param $setting - WP_Customize_Image_Control
	 *
	 * @return mixed
	 */
	function sanitize_image( $file, $setting ) {
		$mimes = [
			'jpg|jpeg|jpe' => 'image/jpeg',
			'gif'          => 'image/gif',
			'png'          => 'image/png',
			'bmp'          => 'image/bmp',
			'webp'          => 'image/webp',
			'tif|tiff'     => 'image/tiff',
			'ico'          => 'image/x-icon'
		];

		//check file type from file name
		$file_ext = wp_check_filetype( $file, $mimes );

		//if file has a valid mime type return it, otherwise return default
		return ( $file_ext['ext'] ? $file : $setting->default );
	}
}



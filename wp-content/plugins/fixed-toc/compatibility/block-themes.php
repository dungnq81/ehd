<?php

/**
 * Compatible with block themes
 *
 * @since 3.1.25
 */
add_filter( 'fixedtoc_frontend_init_hook', function ( $hook ) {
	if ( function_exists( 'wp_is_block_theme' ) && wp_is_block_theme() ) {
		return 'wp';
	} else {
		return $hook;
	}
} );

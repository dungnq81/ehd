<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package eHD
 * @since 1.0.0
 */

\defined( 'ABSPATH' ) || die;

get_header();

echo 'pages';

// homepage widget
if (is_active_sidebar('w-home-sidebar')) :
	dynamic_sidebar('w-home-sidebar');
endif;

get_footer();

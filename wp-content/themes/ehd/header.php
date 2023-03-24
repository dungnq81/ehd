<?php

/**
 * The template for displaying the header
 * This is the template that displays all of the <head> section, opens the <body> tag and adds the site's header.
 * @package hd
 */

use EHD\Cores\Helper;

\defined( 'ABSPATH' ) || die;

?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?> <?php echo Helper::microdata( 'body' ); ?>>
    <?php
    /**
     * ehd_before_header hook.
     *
     * @see __ehd_before_header - 2
     */
    do_action( 'ehd_before_header' );


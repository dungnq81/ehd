<?php

/**
 * The template for displaying the header
 * This is the template that displays all of the <head> section, opens the <body> tag and adds the site's header.
 * @package hd
 */

\defined( '\WPINC' ) || die; // Exit if accessed directly.

?>
<!doctype html>
<html <?php language_attributes(); ?> class="no-js">
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
    <link rel="profile" href="http://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <?php do_action( 'before_header' );?>
    <div class="site-outer site-page">
    <?php

    if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'header' ) ) :
        do_action( 'off_canvas' );
        ?>
        <header id="masthead" class="site-header">
            <?php do_action( 'header' ); ?>
        </header><!-- #masthead -->

        <div class="grid-container">
            <div class="grid-x grid-gap">
                <div class="cell">sdsdasd</div>
                <div class="cell">sdsdasd</div>
                <div class="cell">sdsdasd</div>
                <div class="cell">sdsdasd</div>
            </div>
        </div>

    <?php
    endif;

<?php

/**
 * The template for displaying the header
 * This is the template that displays all of the <head> section, opens the <body> tag and adds the site's header.
 * @package hd
 */

use EHD_Cores\Helper;

\defined( 'ABSPATH' ) || die;

?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>" />

	<?php wp_head(); ?>

</head>
<body <?php body_class(); ?> <?php echo Helper::microdata( 'body' ); ?>>
    <?php

    /**
     * @see \EHD_Themes\Options::body_scripts_top__hook - 99
     */
    do_action( 'wp_body_open' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- core WP hook.

    /**
     * @see __ehd_skip_to_content_link - 2
     * @see __off_canvas_menu - 10
     */
    do_action( 'ehd_before_header' );

    ?>
    <div class="site-outer">
        <?php

        /**
         * @see __ehd_construct_header - 10
         */
        do_action( 'ehd_header' );

        do_action( 'ehd_after_header' );

        ?>
        <div class="site-page">
	        <?php

	        do_action( 'ehd_inside_site_page' );

	        ?>
            <div class="site-content">
                <?php

                do_action( 'ehd_inside_site_content' );



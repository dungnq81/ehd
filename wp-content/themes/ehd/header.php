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
     * Triggered after the opening body tag.
     *
     * @see \EHD_Settings\CustomScripts::body_scripts_top__hook - 99
     */
    do_action( 'wp_body_open' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- core WP hook.

    /**
     * ehd_before_header hook.
     *
     * @see __ehd_skip_to_content_link - 2
     * @see __off_canvas_menu - 10
     */
    do_action( 'ehd_before_header' );

    ?>
    <div class="site-outer">
        <?php

        /**
         * ehd_header hook.
         *
         * @see __ehd_construct_header - 10
         */
        do_action( 'ehd_header' );

        /**
         * ehd_after_header hook.
         *
         */
        do_action( 'ehd_after_header' );

        ?>
        <div class="site-page">
	        <?php

	        /**
	         * ehd_inside_site_page hook.
	         *
	         */
	        do_action( 'ehd_inside_site_page' );

	        ?>
            <div class="site-content">
                <?php

                /**
                 * ehd_inside_site_content hook.
                 *
                 */
                do_action( 'ehd_inside_site_content' );



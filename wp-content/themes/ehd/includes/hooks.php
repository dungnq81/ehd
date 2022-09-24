<?php

/**
 * Hook functions
 *
 * @author   WEBHD
 */

use EHD\Helpers\Cast;
use EHD\Themes\Css;
use EHD\Themes\Func;

\defined('\WPINC') || die;

// ------------------------------------------



// ------------------------------------------

/** inline css */
if (!function_exists('ehd_enqueue_inline_css')) {
    add_action( 'wp_enqueue_scripts', 'ehd_enqueue_inline_css', 31 ); // After WooCommerce.

    /**
     * Add CSS for third-party plugins.
     *
     * @return void
     */
    function ehd_enqueue_inline_css() {
        $css = new Css;

        // breadcrumbs bg
        $breadcrumb_bg = Func::getThemeMod( 'breadcrumb_bg_setting' );
        if ( $breadcrumb_bg ) {
            $css->set_selector( 'section.section-title>.title-bg' );
            $css->add_property( 'background-image', 'url(' . $breadcrumb_bg . ')' );
        }

        if ( $css->css_output() ) {
            wp_add_inline_style( 'app-style', $css->css_output() );
        }
    }
}


// ------------------------------------------

/** SMTP Settings **/
if (!function_exists('ehd_phpmailer_init')) {
    add_action('phpmailer_init', 'ehd_phpmailer_init', 11);

    /**
     * @param $phpmailer
     * @return void
     */
    function ehd_phpmailer_init($phpmailer)
    {
        if (!is_object($phpmailer)) {
            $phpmailer = Cast::toObject($phpmailer);
        }

        $phpmailer->isSMTP();
        $phpmailer->Host = 'smtp.gmail.com';
        $phpmailer->SMTPAuth = true;
        $phpmailer->Username = 'official.webhd@gmail.com';
        $phpmailer->Password = 'obvyigyczmcbxgji';

        // Additional settings
        $phpmailer->SMTPSecure = 'tls';
        $phpmailer->Port = 587;
        $phpmailer->From = 'official.webhd@gmail.com';
        $phpmailer->FromName = get_option('blogname');
    }
}

// ------------------------------------------
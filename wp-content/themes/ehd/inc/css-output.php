<?php
/**
 * CSS Output functions
 *
 * @author   WEBHD
 */

use EHD\Cores\Helper;

\defined( 'ABSPATH' ) || die;

// ------------------------------------------

/** inline css */
add_action('wp_enqueue_scripts', 'ehd_enqueue_inline_css', 31); // After WooCommerce.
if (!function_exists('ehd_enqueue_inline_css')) {
    /**
     * Add CSS for third-party plugins.
     *
     * @return void
     */
    function ehd_enqueue_inline_css()
    {
        $css = new Css();

        // breadcrumbs bg
        $breadcrumb_bg = Helper::getThemeMod('breadcrumb_bg_setting');
        if ($breadcrumb_bg) {
            $css->set_selector('section.section-title>.title-bg');
            $css->add_property('background-image', 'url(' . $breadcrumb_bg . ')');
        }

        if ($css->css_output()) {
            wp_add_inline_style('app-style', $css->css_output());
        }
    }
}

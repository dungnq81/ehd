<?php
/**
 * Hook functions
 * @author WEBHD
 */

use EHD\Cores\Helper;
use EHD\Themes\Css;

\defined( 'ABSPATH' ) || die;

// ------------------------------------------

// add class to anchor link
add_filter('nav_menu_link_attributes', function ($atts) {
    //$atts['class'] = "nav-link";
    return $atts;
}, 100, 1);

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

// ------------------------------------------

/** SMTP Settings **/
add_action('phpmailer_init', 'ehd_phpmailer_init', 11);
if (!function_exists('ehd_phpmailer_init')) {
    /**
     * @param $phpmailer
     * @return void
     */
    function ehd_phpmailer_init($phpmailer)
    {
        if (!is_object($phpmailer)) {
            $phpmailer = Helper::toObject($phpmailer);
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
        $phpmailer->FromName = get_bloginfo('name');
    }
}

// -------------------------------------------------------------
// optimize load
// -------------------------------------------------------------

add_filter('defer_script_loader_tag', 'ehd_defer_script_loader_tag', 10, 1);
if (!function_exists('ehd_defer_script_loader_tag')) {
    /**
     * @param $arr
     * @return string[]
     */
    function ehd_defer_script_loader_tag($arr)
    {
        $arr = [
            'woo-variation-swatches' => 'defer',
            'wc-single-product' => 'defer',
            'wc-add-to-cart' => 'defer',
            'contact-form-7' => 'defer',
            'comment-reply' => 'delay',
            'wp-embed' => 'delay',
            'admin-bar' => 'delay',
            'fixedtoc-js' => 'delay',
            'backtop' => 'delay',
            'shares' => 'delay',
            'o-draggable' => 'delay',
        ];
        return $arr;
    }
}

// ------------------------------------------

add_filter('defer_style_loader_tag', 'ehd_defer_style_loader_tag', 10, 1);
if (!function_exists('ehd_defer_style_loader_tag')) {
    /**
     * @param $arr
     * @return string[]
     */
    function ehd_defer_style_loader_tag($arr)
    {
        $arr = [
            'dashicons',
            'fixedtoc-style',
            'contact-form-7',
            'rank-math',
        ];
        return $arr;
    }
}

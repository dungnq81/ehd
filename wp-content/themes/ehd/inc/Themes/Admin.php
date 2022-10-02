<?php

namespace EHD\Themes;

/**
 * Admin Class
 *
 * @author EHD
 */

\defined('\WPINC') || die;

class Admin
{
    public function __construct()
    {
        add_filter('admin_footer_text', [&$this, 'admin_footer_text']);
        add_action('admin_enqueue_scripts', [&$this, 'admin_enqueue_scripts'], 31);
    }

    /** ---------------------------------------- */

    /**
     * @return void
     */
    public function admin_enqueue_scripts()
    {
        wp_enqueue_style("admin-style", get_stylesheet_directory_uri() . "/assets/css/admin.css", [], EHD_THEME_VERSION);
        wp_enqueue_script("admin", get_stylesheet_directory_uri() . "/assets/js/admin.js", ["jquery"], EHD_THEME_VERSION, true);
    }

    /** ---------------------------------------- */

    public function admin_footer_text()
    {
        printf('<span id="footer-thankyou">%1$s <a href="https://webhd.vn" target="_blank">%2$s</a>.&nbsp;</span>', __('Powered by', 'ehd'), EHD_AUTHOR);
    }
}
<?php

namespace EHD\Ins\Elementor;

\defined('\WPINC') || die;

// If plugin - 'Elementor' not exist then return.
if (!class_exists('\Elementor\Plugin')) {
    return;
}

if (!class_exists('Elementor_Ins')) {
    require __DIR__ . '/elementor.php';

    class Elementor_Ins
    {
        public function __construct()
        {
            add_filter('elementor/frontend/print_google_fonts', '__return_false');
            add_action("wp_enqueue_scripts", [&$this, 'enqueue_scripts'], 100);
        }

        /**
         * Elementor Enqueue styles and scripts
         */
        public function enqueue_scripts()
        {
            wp_enqueue_style("elementor-style", get_stylesheet_directory_uri() . '/assets/css/elementor.css', ["layout-style", "elementor-frontend"], EHD_THEME_VERSION);

            // load awesome font later
            wp_deregister_style("elementor-icons-fa-solid");
            wp_deregister_style("elementor-icons-fa-regular");
            wp_deregister_style("elementor-icons-fa-brands");

            wp_deregister_style("fontawesome");
        }
    }
}
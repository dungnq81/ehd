<?php
/**
 * themes functions
 * @author   WEBHD
 */

use EHD\Cores\Helper;

\defined('ABSPATH') || die;

/** ---------------------------------------- */

if (!function_exists('__after_setup_theme')) {
    add_action('after_setup_theme', '__after_setup_theme', 11);

    /**
     * @link http://codex.wordpress.org/Function_Reference/register_nav_menus#Examples
     *
     * @return void
     */
    function __after_setup_theme() : void
    {
        register_nav_menus(
            [
                'main-nav'   => __('Primary Menu', EHD_TEXT_DOMAIN),
                'second-nav' => __('Secondary Menu', EHD_TEXT_DOMAIN),
                'mobile-nav' => __('Handheld Menu', EHD_TEXT_DOMAIN),
                'social-nav' => __('Social menu', EHD_TEXT_DOMAIN),
                'policy-nav' => __('Terms menu', EHD_TEXT_DOMAIN),
            ]
        );
    }
}

/** ---------------------------------------- */

if (!function_exists('__register_sidebars')) {
    add_action('widgets_init', '__register_sidebars', 11);

    /**
     * Register widget area.
     *
     * @link https://codex.wordpress.org/Function_Reference/register_sidebar
     */
    function __register_sidebars() : void
    {
        /** homepage */
        register_sidebar(
            [
                'container'     => false,
                'id'            => 'w-home-sidebar',
                'name'          => __('Home Page', EHD_TEXT_DOMAIN),
                'description'   => __('Widgets added here will appear in homepage.', EHD_TEXT_DOMAIN),
                'before_widget' => '<div class="%2$s">',
                'after_widget'  => '</div>',
                'before_title'  => '<span>',
                'after_title'   => '</span>',
            ]
        );

        /** header sidebar */
        register_sidebar(
            [
                'container'     => false,
                'id'            => 'w-header-sidebar',
                'name'          => __('Header', EHD_TEXT_DOMAIN),
                'description'   => __('Widgets added here will appear in header.', EHD_TEXT_DOMAIN),
                'before_widget' => '<div class="header-widgets %2$s">',
                'after_widget'  => '</div>',
                'before_title'  => '<span>',
                'after_title'   => '</span>',
            ]
        );

        /** ---------------------------------------- */

        // footer columns
        $footer_args = [];

        $rows = (int) Helper::getThemeMod('footer_row_setting');
        $regions = (int) Helper::getThemeMod('footer_col_setting');

        for ($row = 1; $row <= $rows; $row++) {
            for ($region = 1; $region <= $regions; $region++) {
                $footer_n = $region + $regions * ($row - 1); // Defines footer sidebar ID.
                $footer = sprintf('footer_%d', $footer_n);
                if (1 === $rows) {

                    /* translators: 1: column number */
                    $footer_region_name = sprintf(__('Footer Column %1$d', EHD_TEXT_DOMAIN), $region);

                    /* translators: 1: column number */
                    $footer_region_description = sprintf(__('Widgets added here will appear in column %1$d of the footer.', EHD_TEXT_DOMAIN), $region);
                } else {

                    /* translators: 1: row number, 2: column number */
                    $footer_region_name = sprintf(__('Footer Row %1$d - Column %2$d', EHD_TEXT_DOMAIN), $row, $region);

                    /* translators: 1: column number, 2: row number */
                    $footer_region_description = sprintf(__('Widgets added here will appear in column %1$d of footer row %2$d.', EHD_TEXT_DOMAIN), $region, $row);
                }

                $footer_args[$footer] = [
                    'name'        => $footer_region_name,
                    'id'          => sprintf('w-footer-%d', $footer_n),
                    'description' => $footer_region_description,
                ];
            }
        }

        foreach ($footer_args as $args) {
            $footer_tags = [
                'container'     => false,
                'before_widget' => '<div class="widget %2$s">',
                'after_widget'  => '</div>',
                'before_title'  => '<p class="widget-title h6">',
                'after_title'   => '</p>',
            ];

            register_sidebar($args + $footer_tags);
        }
    }
}



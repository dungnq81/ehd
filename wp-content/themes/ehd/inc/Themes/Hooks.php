<?php

namespace EHD\Sites\Themes;

use EHD\Cores\Helper;

\defined( 'ABSPATH' ) || die;

/**
 * Hooks Class
 *
 * @author EHD
 */
final class Hooks
{
    public function __construct()
    {
        $this->_doFilters();
    }

    // ------------------------------------------------------
    // Filters hook
    // ------------------------------------------------------

    /**
     * @return void
     */
    protected function _doFilters()
    {
        add_filter('body_class', [&$this, 'body_classes'], 11, 1);
        add_filter('post_class', [&$this, 'post_classes'], 11, 1);

        add_filter('nav_menu_css_class', [&$this, 'nav_menu_css_classes'], 11, 2);

        // Add support for buttons in the top-bar menu
        add_filter('wp_nav_menu', function ($ulclass) {
            $find = ['/<a rel="button"/', '/<a title=".*?" rel="button"/'];
            $replace = ['<a rel="button" class="button"', '<a rel="button" class="button"'];
            return preg_replace($find, $replace, $ulclass, 1);
        });

        /**
         * @param $orders
         * @return array
         */
        add_filter('widget_tag_cloud_args', function (array $args) {
            $args['smallest'] = '10';
            $args['largest'] = '19';
            $args['unit'] = 'px';
            $args['number'] = 12;

            return $args;
        });

        /**
         * Use the is-active class of ZURB Foundation on wp_list_pages output.
         * From required+ Foundation http://themes.required.ch.
         */
        add_filter('wp_list_pages', function ($input) {
            $pattern = '/current_page_item/';
            $replace = 'current_page_item is-active';
            return preg_replace($pattern, $replace, $input);
        }, 10, 2);

        // add multiple for category dropdown
        add_filter('wp_dropdown_cats', [&$this, 'dropdown_cats_multiple'], 10, 2);

        // Admin footer text
        add_filter('admin_footer_text', function () {
            printf('<span id="footer-thankyou">%1$s <a href="https://webhd.vn" target="_blank">%2$s</a>.&nbsp;</span>', __('Powered by', EHD_TEXT_DOMAIN), EHD_AUTHOR);
        });
    }

    // ------------------------------------------------------
    // ------------------------------------------------------

    /**
     * Adds custom classes to the array of body classes.
     *
     * @param array $classes Classes for the body element.
     *
     * @return array
     */
    public function body_classes($classes)
    {
        // Check whether we're in the customizer preview.
        if (is_customize_preview()) {
            $classes[] = 'customizer-preview';
        }

        foreach ($classes as $class) {
            if (
                str_contains($class, 'wp-custom-logo')
                || str_contains($class, 'page-template-templates')
                || str_contains($class, 'page-template-default')
                || str_contains($class, 'no-customize-support')
                || str_contains($class, 'page-id-')
                || str_contains($class, 'wvs-theme-')
            ) {
                $classes = array_diff($classes, [$class]);
            }
        }

        if ((is_home() || is_front_page()) && class_exists('\WooCommerce')) {
            $classes[] = 'woocommerce';
        }

        // dark mode func
        $classes[] = 'default-mode';

        return $classes;
    }

    /** ---------------------------------------- */

    /**
     * Adds custom classes to the array of post classes.
     *
     * @param array $classes Classes for the post element.
     *
     * @return array
     */
    public function post_classes($classes)
    {
        // remove_sticky_class
        if (in_array('sticky', $classes)) {
            $classes = array_diff($classes, ["sticky"]);
            $classes[] = 'wp-sticky';
        }

        // remove tag-, category- classes
        foreach ($classes as $class) {
            if (
                str_contains($class, 'tag-')
                || str_contains($class, 'category-')
            ) {
                $classes = array_diff($classes, [$class]);
            }
        }

        return $classes;
    }

    /** ---------------------------------------- */

    /**
     * @param $classes
     * @param $item
     *
     * @return array
     */
    public function nav_menu_css_classes($classes, $item)
    {
        if (!is_array($classes)) {
            $classes = [];
        }

        // remove menu-item-type-, menu-item-object- classes
        foreach ($classes as $class) {
            if (str_contains($class, 'menu-item-type-')
                || str_contains($class, 'menu-item-object-')
            ) {
                $classes = array_diff($classes, [$class]);
            }
        }

        if (1 == $item->current
            || $item->current_item_ancestor
            || $item->current_item_parent
        ) {
            //$classes[] = 'is-active';
            $classes[] = 'active';
        }

        return $classes;
    }

    // ------------------------------------------------------

    /**
     * @param $output
     * @param $r
     *
     * @return mixed|string|string[]
     */
    public function dropdown_cats_multiple($output, $r)
    {
        if (isset($r['multiple']) && $r['multiple']) {
            $output = preg_replace('/^<select/i', '<select multiple', $output);
            $output = str_replace("name='{$r['name']}'", "name='{$r['name']}[]'", $output);
            foreach (array_map('trim', explode(",", $r['selected'])) as $value) {
                $output = str_replace("value=\"{$value}\"", "value=\"{$value}\" selected", $output);
            }
        }

        return $output;
    }
}

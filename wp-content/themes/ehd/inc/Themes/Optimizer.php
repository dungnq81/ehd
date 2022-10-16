<?php

namespace EHD\Themes;

use EHD\Plugins\Core\Helper;

\defined('\WPINC') || die;

/**
 * Optimizer Class
 *
 * @author EHD
 */
final class Optimizer
{
    public function __construct()
    {
        $this->_doActions();
        $this->_doFilters();
    }

    // ------------------------------------------------------
    // Actions hook
    // ------------------------------------------------------

    /**
     * @return void
     */
    protected function _doActions()
    {
        add_action('wp_default_scripts', [&$this, 'default_scripts']);
        add_action('wp_enqueue_scripts', [&$this, 'enqueue_scripts'], 1001);

        add_action('wp_footer', [&$this, 'back_to_top'], 98);
        add_action('wp_footer', [&$this, 'deferred_scripts'], 999);

        // wp_print_footer_scripts
        add_action('wp_print_footer_scripts', [&$this, 'print_footer_scripts'], 99);

        // hide admin bar
        add_action("user_register", function ($user_id) {
            update_user_meta($user_id, 'show_admin_bar_front', false);
            update_user_meta($user_id, 'show_admin_bar_admin', false);
        }, 10, 1);
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

        // comment off default
        add_filter('wp_insert_post_data', function ($data) {
            if ($data['post_status'] == 'auto-draft') {
                $data['comment_status'] = 0;
                $data['ping_status'] = 0;
            }
            return $data;
        }, 11, 1);

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

        //...
        // Prevent Specific Plugins from Deactivation
        add_filter('plugin_action_links', function ($actions, $plugin_file, $plugin_data, $context) {
            if (array_key_exists('deactivate', $actions)
                && in_array(
                    $plugin_file,
                    [
                        'ehd-core/ehd-core.php',
                        'advanced-custom-fields-pro/acf.php',
                    ])
            ) {
                unset($actions['deactivate']);
            }

            return $actions;

        }, 10, 4);

        //...
        add_filter('admin_footer_text', function () {
            printf('<span id="footer-thankyou">%1$s <a href="https://webhd.vn" target="_blank">%2$s</a>.&nbsp;</span>', __('Powered by', EHD_TEXT_DOMAIN), EHD_AUTHOR);
        });
    }

    // ------------------------------------------------------
    // ------------------------------------------------------

    /**
     * This does not enqueue the script because it is tiny and because it is only for IE11,
     * thus it does not warrant having an entire dedicated blocking script being loaded.
     *
     * @link https://git.io/vWdr2
     */
    public function print_footer_scripts()
    {
        ?>
        <script>document.documentElement.classList.remove("no-js");
            if (-1 !== navigator.userAgent.indexOf('MSIE') || -1 !== navigator.appVersion.indexOf('Trident/')) {
                document.documentElement.classList.add('is-IE');
            }</script>
        <?php
        if (file_exists($skip_link = get_stylesheet_directory() . '/assets/js/plugins/skip-link-focus-fix.js')) {
            echo '<script>';
            include $skip_link;
            echo '</script>';
        }

        if (file_exists($flex_gap = get_stylesheet_directory() . '/assets/js/plugins/flex-gap.js')) {
            echo '<script>';
            include $flex_gap;
            echo '</script>';
        }

        // The following is minified via `npx terser --compress --mangle -- assets/js/skip-link-focus-fix.js`.
    }

    // ------------------------------------------------------

    /**
     * Build the back to top button
     *
     * @return void
     */
    public function back_to_top()
    {
        $back_to_top = apply_filters('back_to_top', true);
        if ($back_to_top) {
            echo apply_filters( // phpcs:ignore
                'back_to_top_output',
                sprintf(
                    '<a title="%1$s" aria-label="%1$s" rel="nofollow" href="#" class="back-to-top toTop o_draggable" style="opacity:0;visibility:hidden;" data-scroll-speed="%2$s" data-start-scroll="%3$s" data-glyph=""></a>',
                    esc_attr__('Scroll back to top', EHD_TEXT_DOMAIN),
                    absint(apply_filters('back_to_top_scroll_speed', 400)),
                    absint(apply_filters('back_to_top_start_scroll', 300)),
                )
            );
        }
    }

    /** ---------------------------------------- */

    /**
     * @return void
     */
    public function enqueue_scripts()
    {
        /*extra scripts*/
        wp_enqueue_script("o-draggable", get_stylesheet_directory_uri() . "/assets/js/plugins/draggable.js", [], false, true);
        wp_enqueue_script("backtop", get_stylesheet_directory_uri() . "/assets/js/plugins/backtop.js", [], false, true);
        wp_enqueue_script("shares", get_stylesheet_directory_uri() . "/assets/js/plugins/shares.min.js", ["jquery"], false, true);

        //$widgets_block_editor_off = Helper::getThemeMod('use_widgets_block_editor_setting');
        $gutenberg_widgets_off = Helper::getThemeMod('gutenberg_use_widgets_block_editor_setting');
        $gutenberg_off = Helper::getThemeMod('use_block_editor_for_post_type_setting');
        if ($gutenberg_widgets_off && $gutenberg_off) {
            wp_dequeue_style('wp-block-library');
            wp_dequeue_style('wp-block-library-theme');
        }
    }

    /** ---------------------------------------- */

    /**
     * @param $scripts
     * @return void
     */
    public function default_scripts($scripts)
    {
        if (!is_admin() && isset($scripts->registered['jquery'])) {
            $script = $scripts->registered['jquery'];
            if ($script->deps) {
                // Check whether the script has any dependencies
                $script->deps = array_diff($script->deps, ['jquery-migrate']);
            }
        }
    }

    /** ---------------------------------------- */

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
                str_contains($class, 'page-template-default')
                || str_contains($class, 'page-template-templates')
                || str_contains($class, 'page-template-templatespage-homepage-php')
                || str_contains($class, 'wp-custom-logo')
                || str_contains($class, 'no-customize-support')
                || str_contains($class, 'theme-hello-elementor')
                || str_contains($class, 'elementor-kit-')
                || str_contains($class, 'wvs-theme-')
            ) {
                $classes = array_diff($classes, [$class]);
            }
        }

        if (is_home() || is_front_page() && class_exists('\WooCommerce')) {
            $classes[] = 'woocommerce';
        }

        // dark mode func
        //$classes[] = 'light-mode';

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

    /** ---------------------------------------- */

    /**
     * @return void
     */
    public function deferred_scripts()
    {
        // Facebook
        $fb_appid = Helper::getThemeMod('fb_menu_setting');
        if ($fb_appid) {
            echo "<script>";
            echo "window.fbAsyncInit = function() {FB.init({appId:'" . $fb_appid . "',status:true,xfbml:true,autoLogAppEvents:true,version:'v15.0'});};";
            echo "</script>";
            echo "<script async defer crossorigin=\"anonymous\" data-type='lazy' data-src=\"https://connect.facebook.net/en_US/sdk.js\"></script>";
        }

        $fb_pageid = Helper::getThemeMod('fbpage_menu_setting');
        $fb_livechat = Helper::getThemeMod('fb_chat_setting');
        if ($fb_appid && $fb_pageid && $fb_livechat && !is_customize_preview()) {
            if ($fb_pageid) {
                echo '<script async defer data-type="lazy" data-src="https://connect.facebook.net/en_US/sdk/xfbml.customerchat.js"></script>';
                $_fb_message = __('If you need assistance, please leave a message here. Thanks', EHD_TEXT_DOMAIN);
                echo '<div class="fb-customerchat" attribution="setup_tool" page_id="' . $fb_pageid . '" theme_color="#CC3366" logged_in_greeting="' . esc_attr($_fb_message) . '" logged_out_greeting="' . esc_attr($_fb_message) . '"></div>';
            }
        }

        // Zalo
        $zalo_oaid = Helper::getThemeMod('zalo_oa_menu_setting');
        $zalo_livechat = Helper::getThemeMod('zalo_chat_setting');
        if ($zalo_oaid) {
            if ($zalo_livechat) {
                echo '<div class="zalo-chat-widget" data-oaid="' . $zalo_oaid . '" data-welcome-message="' . __('Rất vui khi được hỗ trợ bạn.', EHD_TEXT_DOMAIN) . '" data-autopopup="0" data-width="350" data-height="420"></div>';
            }

            echo "<script defer data-type='lazy' data-src=\"https://sp.zalo.me/plugins/sdk.js\"></script>";
        }

        /** Set delay timeout milisecond **/
        $timeout = 5000;
        $inline_js = 'const loadScriptsTimer=setTimeout(loadScripts,' . $timeout . ');const userInteractionEvents=["mouseover","keydown","touchstart","touchmove","wheel"];userInteractionEvents.forEach(function(event){window.addEventListener(event,triggerScriptLoader,{passive:!0})});function triggerScriptLoader(){loadScripts();clearTimeout(loadScriptsTimer);userInteractionEvents.forEach(function(event){window.removeEventListener(event,triggerScriptLoader,{passive:!0})})}';
        $inline_js .= "function loadScripts(){document.querySelectorAll(\"script[data-type='lazy']\").forEach(function(elem){elem.setAttribute(\"src\",elem.getAttribute(\"data-src\"));elem.removeAttribute(\"data-src\");})}";
        //echo "\n";
        echo '<script src="data:text/javascript;base64,' . base64_encode($inline_js) . '"></script>';
        //echo "\n";
    }
}

<?php
/**
 * register_widget functions
 *
 * @author   WEBHD
 */

use EHD\Plugins\Widgets\offCanvas_Widget;

\defined( 'ABSPATH' ) || die;

if (!function_exists('w_register_widgets')) {
    add_action('widgets_init', 'w_register_widgets', 10);

    /**
     * Registers a WP_Widget widget
     *
     * @return void
     */
    function w_register_widgets() : void
    {
        class_exists(offCanvas_Widget::class) && register_widget(new offCanvas_Widget);
    }
}
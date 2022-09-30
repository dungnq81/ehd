<?php
/**
 * register_widget functions
 *
 * @author   WEBHD
 */

use EHD\Themes\Func;
use EHD\Widgets\offCanvas_Widget;

\defined('\WPINC') || die;

if (!function_exists('_register_widgets')) {

    /**
     * Registers a WP_Widget widget
     *
     * @return void
     */
    function _register_widgets()
    {
        class_exists(offCanvas_Widget::class) && register_widget(new offCanvas_Widget);
    }

    /** */
    $widgets_block_editor_off = Func::getThemeMod('use_widgets_block_editor_setting');
    if ($widgets_block_editor_off) {
        add_action('widgets_init', '_register_widgets', 10);
    }
}
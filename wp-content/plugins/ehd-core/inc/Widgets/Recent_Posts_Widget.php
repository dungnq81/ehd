<?php

namespace EHD\Plugins\Widgets;

use EHD\Plugins\Core\Widget;

\defined('ABSPATH') || die;

if (!class_exists('Recent_Posts_Widget')) {
    class Recent_Posts_Widget extends Widget
    {
        public function __construct()
        {
            $this->widget_description = __('Your site&#8217;s most recent Posts.', EHD_PLUGIN_TEXT_DOMAIN);
            $this->widget_name = __('Recent Posts', EHD_PLUGIN_TEXT_DOMAIN);
            $this->settings = [
                'title' => [
                    'type' => 'text',
                    'std' => __('Recent Posts', EHD_PLUGIN_TEXT_DOMAIN),
                    'label' => __('Title', EHD_PLUGIN_TEXT_DOMAIN),
                ],
                'number' => [
                    'type' => 'number',
                    'min' => 0,
                    'max' => 99,
                    'std' => 5,
                    'class' => 'tiny-text',
                    'label' => __('Number of posts to show', EHD_PLUGIN_TEXT_DOMAIN),
                ],
                'show_cat' => [
                    'type' => 'checkbox',
                    'std' => '',
                    'class' => 'checkbox',
                    'label' => __('Display post categories?', EHD_PLUGIN_TEXT_DOMAIN),
                ],
                'show_thumbnail' => [
                    'type' => 'checkbox',
                    'std' => '',
                    'class' => 'checkbox',
                    'label' => __('Display post thumbnails?', EHD_PLUGIN_TEXT_DOMAIN),
                ],
                'show_date' => [
                    'type' => 'checkbox',
                    'std' => '',
                    'class' => 'checkbox',
                    'label' => __('Display post date?', EHD_PLUGIN_TEXT_DOMAIN),
                ],
                'css_class' => [
                    'type' => 'text',
                    'std' => '',
                    'label' => __('Css class', EHD_PLUGIN_TEXT_DOMAIN),
                ],
            ];

            parent::__construct();
        }

        /**
         * Outputs the content for the current Recent Posts widget instance.
         *
         * @param array $args
         * @param array $instance
         */
        public function widget($args, $instance)
        {

        }
    }
}

<?php

namespace EHD\Plugins\Widgets;

use EHD\Plugins\Core\Helper;
use EHD\Plugins\Core\Widget;

\defined('ABSPATH') || die;

if (!class_exists('Search_Widget')) {
    class Search_Widget extends Widget
    {
        public function __construct()
        {
            $this->widget_description = __('A search form for your site.');
            $this->widget_name = __('Search');
            $this->settings = [
                'title'     => [
                    'type'  => 'text',
                    'std'   => __('Search'),
                    'label' => __('Title'),
                ],
                'css_class' => [
                    'type'  => 'text',
                    'std'   => '',
                    'label' => __('Css class', EHD_PLUGIN_TEXT_DOMAIN),
                ],
            ];

            parent::__construct();
        }

        /**
         * Creating widget front-end
         *
         * @param array $args
         * @param array $instance
         */
        public function widget($args, $instance)
        {
            if ($this->get_cached_widget($args)) {
                return;
            }

            $title = apply_filters('widget_title', $this->get_instance_title($instance), $instance, $this->id_base);
            $css_class = !empty($instance['css_class']) ? sanitize_title($instance['css_class']) : '';

            $shortcode_content = Helper::doShortcode(
                'inline_search',
                [
                    'title' => $title,
                    'class' => $this->widget_classname . ' ' . $css_class,
                    'id'    => '',
                ]
            );

            echo $this->cache_widget($args, $shortcode_content); // WPCS: XSS ok.
        }
    }
}

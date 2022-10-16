<?php

namespace EHD\Plugins\Widgets;

use EHD\Plugins\Core\Widget;

\defined('ABSPATH') || die;

if (!class_exists('Products_Widget')) {
    class Products_Widget extends Widget
    {
        public function __construct()
        {
            $this->widget_description = __("A list of your store's products.", 'woocommerce');
            $this->widget_name = __('Products list', 'woocommerce');
            $this->settings = [
                'title'                 => [
                    'type'  => 'text',
                    'std'   => __('Products', 'woocommerce'),
                    'label' => __('Title', 'woocommerce'),
                ],
                'number'                => [
                    'type'  => 'number',
                    'min'   => 1,
                    'max'   => '',
                    'std'   => 8,
                    'class' => 'tiny-text',
                    'label' => __('Number of products to show', 'woocommerce'),
                ],
                'columns'               => [
                    'type'  => 'number',
                    'min'   => 1,
                    'max'   => '',
                    'std'   => 4,
                    'class' => 'tiny-text',
                    'label' => __('Products per row', 'woocommerce'),
                ],
                'show'                  => [
                    'type'    => 'select',
                    'std'     => '',
                    'label'   => __('Show', 'woocommerce'),
                    'options' => [
                        ''         => __('All products', 'woocommerce'),
                        'featured' => __('Featured products', 'woocommerce'),
                        'onsale'   => __('On-sale products', 'woocommerce'),
                    ],
                ],
                'orderby'               => [
                    'type'    => 'select',
                    'std'     => '',
                    'label'   => __('Order by', 'woocommerce'),
                    'options' => [
                        ''      => __('Default', 'woocommerce'),
                        'date'  => __('Date', 'woocommerce'),
                        'price' => __('Price', 'woocommerce'),
                        'rand'  => __('Random', 'woocommerce'),
                        'sales' => __('Sales', 'woocommerce'),
                    ],
                ],
                'hide_free'             => [
                    'type'  => 'checkbox',
                    'std'   => 0,
                    'label' => __('Hide free products', 'woocommerce'),
                ],
                'product_cat_ids'       => [
                    'type'  => 'text',
                    'std'   => '',
                    'label' => __('Product cat Ids, separated by commas', EHD_PLUGIN_TEXT_DOMAIN),
                ],
                'include_children'      => [
                    'type'  => 'checkbox',
                    'std'   => 0,
                    'label' => __('Includes products of children cat', EHD_PLUGIN_TEXT_DOMAIN),
                ],
                'show_viewmore_button'  => [
                    'type'  => 'checkbox',
                    'std'   => 0,
                    'label' => __('Show view more button', EHD_PLUGIN_TEXT_DOMAIN),
                ],
                'viewmore_button_title' => [
                    'type'  => 'text',
                    'std'   => __('View more', EHD_PLUGIN_TEXT_DOMAIN),
                    'label' => __('View more title', EHD_PLUGIN_TEXT_DOMAIN),
                ],
                'viewmore_button_link'  => [
                    'type'  => 'text',
                    'std'   => '#',
                    'label' => __('View more link', EHD_PLUGIN_TEXT_DOMAIN),
                ],
                'css_class'             => [
                    'type'  => 'text',
                    'std'   => '',
                    'label' => __('Css class', EHD_PLUGIN_TEXT_DOMAIN),
                ],
            ];

            parent::__construct();
        }

        /**
         * Output widget.
         *
         * @param array $args     Arguments.
         * @param array $instance Widget instance.
         */
        public function widget($args, $instance)
        {
            if ($this->get_cached_widget($args)) {
                return;
            }

            $title = apply_filters('widget_title', $this->get_instance_title($instance), $instance, $this->id_base);
            $number = !empty($instance['number']) ? absint($instance['number']) : 0;
            $css_class = !empty($instance['css_class']) ? sanitize_title($instance['css_class']) : '';

            $args = apply_filters(
                'widget_products_args',
                [
                    'limit'   => $number,
                    'columns' => 4,
                    'orderby' => 'date',
                    'order'   => 'desc',
                    'on_sale' => 'true',
                    'title'   => __('On Sale', 'storefront'),
                ]
            );
        }
    }
}

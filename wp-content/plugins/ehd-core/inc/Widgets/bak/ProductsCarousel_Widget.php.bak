<?php

namespace EHD\Plugins\Widgets;

use EHD\Plugins\Core\Helper;
use WC_Widget;
use WP_Query;

\defined('ABSPATH') || die;

if (!class_exists('ProductsCarousel_Widget')) {
    class ProductsCarousel_Widget extends WC_Widget
    {
        public function __construct()
        {
            $this->widget_description = __("A slideshow list of your store's products.", EHD_PLUGIN_TEXT_DOMAIN);
            $this->widget_name = __('Products slideshow', EHD_PLUGIN_TEXT_DOMAIN);
            $this->widget_cssclass = 'products-slideshow';
            $this->widget_id = 'w-products-slideshow';
            $this->settings = [
                'title' => [
                    'type' => 'text',
                    'std' => __('Products', 'woocommerce'),
                    'label' => __('Title', 'woocommerce'),
                ],
                'number' => [
                    'type' => 'number',
                    'step' => 1,
                    'min' => 1,
                    'max' => '',
                    'std' => 8,
                    'class' => 'tiny-text',
                    'label' => __('Number of products to show', 'woocommerce'),
                ],
            ];

            parent::__construct();
        }

        /**
         * @param $id
         *
         * @return object|null
         */
        protected function acfFields($id)
        {
            if (!class_exists('\ACF')) {
                return null;
            }

            return Helper::toObject(get_fields($id));
        }
    }
}

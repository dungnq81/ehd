<?php

namespace EHD\Plugins\Widgets;

use EHD\Plugins\Core\Helper;
use WC_Widget;
use WP_Query;

\defined('ABSPATH') || die;

if (!class_exists('Products_Widget')) {
    class Products_Widget extends WC_Widget
    {
        public function __construct()
        {
            $this->widget_description = __("A list of your store's products.", 'woocommerce');
            $this->widget_name = __('Products list', 'woocommerce');
            $this->widget_cssclass = 'woocommerce products-list';
            $this->widget_id = 'w-products-list';
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
                'show' => [
                    'type' => 'select',
                    'std' => '',
                    'label' => __('Show', 'woocommerce'),
                    'options' => [
                        '' => __('All products', 'woocommerce'),
                        'featured' => __('Featured products', 'woocommerce'),
                        'onsale' => __('On-sale products', 'woocommerce'),
                    ],
                ],
                'orderby' => [
                    'type' => 'select',
                    'std' => '',
                    'label' => __('Order by', 'woocommerce'),
                    'options' => [
                        '' => __('Default', 'woocommerce'),
                        'date' => __('Date', 'woocommerce'),
                        'price' => __('Price', 'woocommerce'),
                        'rand' => __('Random', 'woocommerce'),
                        'sales' => __('Sales', 'woocommerce'),
                    ],
                ],
                'order' => [
                    'type' => 'select',
                    'std' => 'desc',
                    'label' => __('Sorting order', EHD_PLUGIN_TEXT_DOMAIN),
                    'options' => [
                        'asc' => __('ASC', EHD_PLUGIN_TEXT_DOMAIN),
                        'desc' => __('DESC', EHD_PLUGIN_TEXT_DOMAIN),
                    ],
                ],
                'hide_free' => [
                    'type' => 'checkbox',
                    'std' => 0,
                    'label' => __('Hide free products', 'woocommerce'),
                ],
                'show_hidden' => [
                    'type' => 'checkbox',
                    'std' => 0,
                    'label' => __('Show hidden products', 'woocommerce'),
                ],
                //...
                'product_cat_ids' => [
                    'type' => 'text',
                    'std' => '',
                    'label' => __('Product cat Ids, separated by commas', EHD_PLUGIN_TEXT_DOMAIN),
                ],
                'include_children' => [
                    'type' => 'checkbox',
                    'std' => 0,
                    'label' => __('Includes products of children cat', EHD_PLUGIN_TEXT_DOMAIN),
                ],
                'desktop_columns' => [
                    'type' => 'number',
                    'step' => 1,
                    'min' => 0,
                    'max' => '',
                    'std' => 1,
                    'class' => 'tiny-text',
                    'label' => __('Desktop column(s)', EHD_PLUGIN_TEXT_DOMAIN),
                ],
                'tablet_columns' => [
                    'type' => 'number',
                    'step' => 1,
                    'min' => 0,
                    'max' => '',
                    'std' => 1,
                    'class' => 'tiny-text',
                    'label' => __('Tablet column(s)', EHD_PLUGIN_TEXT_DOMAIN),
                ],
                'mobile_columns' => [
                    'type' => 'number',
                    'step' => 1,
                    'min' => 0,
                    'max' => '',
                    'std' => 1,
                    'class' => 'tiny-text',
                    'label' => __('Mobile column(s)', EHD_PLUGIN_TEXT_DOMAIN),
                ],
                'display_type' => [
                    'type' => 'select',
                    'std' => 'list',
                    'label' => __('Display Type', EHD_PLUGIN_TEXT_DOMAIN),
                    'options' => [
                        'list' => __('List', EHD_PLUGIN_TEXT_DOMAIN),
                        'slideshow' => __('Slideshow', EHD_PLUGIN_TEXT_DOMAIN),
                    ],
                ],
                'desktop_gutter' => [
                    'type' => 'number',
                    'step' => 1,
                    'min' => 0,
                    'max' => '',
                    'std' => 30,
                    'class' => 'tiny-text',
                    'label' => __('Desktop gutter', EHD_PLUGIN_TEXT_DOMAIN),
                ],
                'mobile_gutter' => [
                    'type' => 'number',
                    'step' => 1,
                    'min' => 0,
                    'max' => '',
                    'std' => 20,
                    'class' => 'tiny-text',
                    'label' => __('Mobile gutter', EHD_PLUGIN_TEXT_DOMAIN),
                ],
                'show_viewmore_button' => [
                    'type' => 'checkbox',
                    'std' => 0,
                    'label' => __('Show view more button', EHD_PLUGIN_TEXT_DOMAIN),
                ],
                'title_viewmore_button' => [
                    'type' => 'text',
                    'std' => __('View more', EHD_PLUGIN_TEXT_DOMAIN),
                    'label' => __('View more title', EHD_PLUGIN_TEXT_DOMAIN),
                ],
                'title_viewmore_link' => [
                    'type' => 'text',
                    'std' => '#',
                    'label' => __('View more link', EHD_PLUGIN_TEXT_DOMAIN),
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
         * Query the products and return them.
         *
         * @param array $args     Arguments.
         * @param array $instance Widget instance.
         *
         * @return WP_Query
         */
        public function get_products($args, $instance)
        {
            $number = !empty($instance['number']) ? absint($instance['number']) : $this->settings['number']['std'];

            $show = !empty($instance['show']) ? sanitize_title($instance['show']) : $this->settings['show']['std'];
            $orderby = !empty($instance['orderby']) ? sanitize_title($instance['orderby']) : $this->settings['orderby']['std'];
            $order = !empty($instance['order']) ? sanitize_title($instance['order']) : $this->settings['order']['std'];

            $product_visibility_term_ids = wc_get_product_visibility_term_ids();
            $query_args = [
                'update_post_meta_cache' => false,
                'update_post_term_cache' => false,
                'posts_per_page' => $number,
                'post_status' => 'publish',
                'post_type' => 'product',
                'no_found_rows' => true,
                'ignore_sticky_posts' => true,
                'order' => $order,
                'tax_query' => ['relation' => 'AND'],
            ]; // WPCS: slow query ok.

            // show_hidden
            if (empty($instance['show_hidden'])) {
                $query_args['tax_query'][] = [
                    'taxonomy' => 'product_visibility',
                    'field' => 'term_taxonomy_id',
                    'terms' => is_search() ? $product_visibility_term_ids['exclude-from-search'] : $product_visibility_term_ids['exclude-from-catalog'],
                    'operator' => 'NOT IN',
                ];
                $query_args['post_parent'] = 0;
            }

            // hide_free
            if (!empty($instance['hide_free'])) {
                $query_args['meta_query'][] = [
                    'key' => '_price',
                    'value' => 0,
                    'compare' => '>',
                    'type' => 'DECIMAL',
                ];
            }

            // woocommerce_hide_out_of_stock_items
            if ('yes' === get_option('woocommerce_hide_out_of_stock_items')) {
                $query_args['tax_query'][] = [
                    [
                        'taxonomy' => 'product_visibility',
                        'field' => 'term_taxonomy_id',
                        'terms' => $product_visibility_term_ids['outofstock'],
                        'operator' => 'NOT IN',
                    ],
                ]; // WPCS: slow query ok.
            }

            // show
            switch ($show) {
                case 'featured':
                    $query_args['tax_query'][] = [
                        'taxonomy' => 'product_visibility',
                        'field' => 'term_taxonomy_id',
                        'terms' => $product_visibility_term_ids['featured'],
                    ];
                    break;
                case 'onsale':
                    $product_ids_on_sale = wc_get_product_ids_on_sale();
                    $product_ids_on_sale[] = 0;
                    $query_args['post__in'] = $product_ids_on_sale;
                    break;
            }

            // orderby
            switch ($orderby) {
                case 'price':
                    $query_args['meta_key'] = '_price'; // WPCS: slow query ok.
                    $query_args['orderby'] = 'meta_value_num';
                    break;
                case 'rand':
                    $query_args['orderby'] = 'rand';
                    break;
                case 'sales':
                    $query_args['meta_key'] = 'total_sales'; // WPCS: slow query ok.
                    $query_args['orderby'] = 'meta_value_num';
                    break;
                case 'date':
                    $query_args['orderby'] = 'date';
                    break;
            }

            // product_cat_ids
            if (!empty($instance['product_cat_ids'])) {

                $include_children = !empty($instance['show_hidden']);
                $term_ids = array_filter(Helper::toArray($instance['product_cat_ids']), 'is_int');

                $query_args['tax_query'][] = [
                    [
                        'taxonomy' => 'product_cat',
                        'terms' => $term_ids,
                        'include_children' => (bool)$include_children,
                    ],
                ]; // WPCS: slow query ok.
            }

            return new WP_Query(apply_filters('woocommerce_products_widget_query_args', $query_args));
        }

        /**
         * Output widget.
         *
         * @param array $args     Arguments.
         * @param array $instance Widget instance.
         *
         * @see WP_Widget
         */
        public function widget($args, $instance)
        {
            if ($this->get_cached_widget($args)) {
                return;
            }

            ob_start();

            wc_set_loop_prop( 'name', 'widget' );
            $products = $this->get_products( $args, $instance );
            if ( $products && $products->have_posts() ) {

                $title = apply_filters('widget_title', $this->get_instance_title($instance), $instance, $this->id_base);

                $number = !empty($instance['number']) ? absint($instance['number']) : 0;
                $desktop_columns = !empty($instance['desktop_columns']) ? absint($instance['desktop_columns']) : 0;
                $tablet_columns = !empty($instance['tablet_columns']) ? absint($instance['tablet_columns']) : 0;
                $mobile_columns = !empty($instance['mobile_columns']) ? absint($instance['mobile_columns']) : 0;

                $display_type = sanitize_title($instance['display_type']);
                $desktop_gutter = !empty($instance['desktop_gutter']) ? absint($instance['desktop_gutter']) : 0;
                $mobile_gutter = !empty($instance['mobile_gutter']) ? absint($instance['mobile_gutter']) : 0;

                $css_class = !empty($instance['css_class']) ? sanitize_title($instance['css_class']) : '';

                if (!empty($instance['show_viewmore_button'])) {
                    $title_viewmore_button = $instance['title_viewmore_button'] ?: '';
                    $title_viewmore_link = filter_var($instance['title_viewmore_link'], FILTER_VALIDATE_URL) ? $instance['title_viewmore_link'] : '#';
                }
            }

            wp_reset_postdata();
            echo $this->cache_widget( $args, ob_get_clean() ); // WPCS: XSS ok.
        }
    }
}

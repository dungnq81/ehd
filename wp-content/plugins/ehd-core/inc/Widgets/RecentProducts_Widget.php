<?php

namespace EHD\Widgets;

use EHD\Cores\Widget;
use WP_Query;

\defined('ABSPATH') || die;

if (!class_exists('RecentProducts_Widget')) {
    class RecentProducts_Widget extends Widget
    {
        public function __construct()
        {
            $this->widget_description = __("Display a list of recent products from your store.", EHD_PLUGIN_TEXT_DOMAIN);
            $this->widget_name = __('W - Recent Products', EHD_PLUGIN_TEXT_DOMAIN);
            $this->settings = [
                'title'     => [
                    'type'  => 'text',
                    'std'   => __('Recent Products', EHD_PLUGIN_TEXT_DOMAIN),
                    'label' => __('Title'),
                ],
                'number'    => [
                    'type'  => 'number',
                    'min'   => 0,
                    'max'   => 99,
                    'std'   => 5,
                    'class' => 'tiny-text',
                    'label' => __('Number of products to show', 'woocommerce'),
                ],
                'show'      => [
                    'type'    => 'select',
                    'std'     => '',
                    'label'   => __('Show', 'woocommerce'),
                    'options' => [
                        ''         => __('All products', 'woocommerce'),
                        'featured' => __('Featured products', 'woocommerce'),
                        'onsale'   => __('On-sale products', 'woocommerce'),
                    ],
                ],
                'orderby'   => [
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
                'order'     => [
                    'type'    => 'select',
                    'std'     => 'desc',
                    'label'   => __('Sorting order', EHD_PLUGIN_TEXT_DOMAIN),
                    'options' => [
                        'asc'  => __('ASC', EHD_PLUGIN_TEXT_DOMAIN),
                        'desc' => __('DESC', EHD_PLUGIN_TEXT_DOMAIN),
                    ],
                ],
                'hide_free' => [
                    'type'  => 'checkbox',
                    'std'   => 0,
                    'label' => __('Hide free products', 'woocommerce'),
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

                'posts_per_page'      => $number,
                'post_status'         => 'publish',
                'post_type'           => 'product',
                'no_found_rows'       => true,
                'ignore_sticky_posts' => true,
                'order'               => $order,
                'tax_query'           => ['relation' => 'AND'],
            ]; // WPCS: slow query ok.

            // hide_free
            if (!empty($instance['hide_free'])) {
                $query_args['meta_query'][] = [
                    'key'     => '_price',
                    'value'   => 0,
                    'compare' => '>',
                    'type'    => 'DECIMAL',
                ];
            }

            // woocommerce_hide_out_of_stock_items
            if ('yes' === get_option('woocommerce_hide_out_of_stock_items')) {
                $query_args['tax_query'][] = [
                    [
                        'taxonomy' => 'product_visibility',
                        'field'    => 'term_taxonomy_id',
                        'terms'    => $product_visibility_term_ids['outofstock'],
                        'operator' => 'NOT IN',
                    ],
                ]; // WPCS: slow query ok.
            }

            // show
            switch ($show) {
                case 'featured':
                    $query_args['tax_query'][] = [
                        'taxonomy' => 'product_visibility',
                        'field'    => 'term_taxonomy_id',
                        'terms'    => $product_visibility_term_ids['featured'],
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

            return new WP_Query(apply_filters('recent_products_widget_query_args', $query_args));
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

            $title = apply_filters('widget_title', $this->get_instance_title($instance), $instance, $this->id_base);
            $number = !empty($instance['number']) ? absint($instance['number']) : 0;
            $css_class = !empty($instance['css_class']) ? sanitize_title($instance['css_class']) : '';

            $products = $this->get_products($args, $instance);
            if (!$products || !$products->have_posts()) {
                return;
            }

            $uniqid = esc_attr(uniqid($this->widget_classname . '-'));

            // has products
            wc_set_loop_prop('name', 'recent_products_widget');

            ob_start();

            ?>
            <section class="section recent-products-section <?= $css_class ?>" id="<?= $uniqid ?>">

                <?php if ($title) echo '<h2 class="heading-title">' . $title . '</h2>'; ?>

                <div class="<?= $uniqid ?>" aria-labelledby="<?php echo esc_attr($title); ?>">
                    <div class="grid-products">
                        <?php
                        $i = 0;

                        $template_args = [
                            'widget_id'   => $args['widget_id'] ?? $this->widget_id,
                            'show_rating' => true,
                        ];

                        // Load slides loop
                        while ($products->have_posts() && $i < $number) : $products->the_post();
                            global $product;

                            if (empty($product) || FALSE === wc_get_loop_product_visibility($product->get_id()) || !$product->is_visible()) {
                                continue;
                            }

                            echo '<div class="cell cell-' . $i . '">';
                            wc_get_template('content-widget-product.php', $template_args);
                            echo '</div>';

                            ++$i;
                        endwhile;
                        wp_reset_postdata();
                        ?>
                    </div>
                </div>
            </section>
            <?php

            echo $this->cache_widget($args, ob_get_clean()); // WPCS: XSS ok.
        }
    }
}

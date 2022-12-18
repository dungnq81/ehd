<?php

namespace EHD\Plugins\Widgets;

use EHD\Plugins\Core\Helper;
use EHD\Plugins\Core\Widget;

\defined('ABSPATH') || die;

if (!class_exists('ProductsCarousel_Widget')) {
    class ProductsCarousel_Widget extends Widget
    {
        public function __construct()
        {
            $this->widget_description = __("A slideshow list of your store's products.", EHD_PLUGIN_TEXT_DOMAIN);
            $this->widget_name = __('W - Products Carousels', EHD_PLUGIN_TEXT_DOMAIN);
            $this->settings = [
                'title'                 => [
                    'type'  => 'text',
                    'std'   => __('Products Slideshow', EHD_PLUGIN_TEXT_DOMAIN),
                    'label' => __('Title', 'woocommerce'),
                ],
                'number'                => [
                    'type'  => 'number',
                    'min'   => 1,
                    'max'   => '',
                    'std'   => 10,
                    'class' => 'tiny-text',
                    'label' => __('Number of products to show', 'woocommerce'),
                ],
                'columns_dektop' => [
                    'type'  => 'number',
                    'min'   => 0,
                    'max'   => '',
                    'std'   => 5,
                    'class' => 'tiny-text',
                    'label' => __('Products per row (desktop)', EHD_PLUGIN_TEXT_DOMAIN),
                ],
                'columns_tablet' => [
                    'type'  => 'number',
                    'min'   => 0,
                    'max'   => '',
                    'std'   => 3,
                    'class' => 'tiny-text',
                    'label' => __('Products per row (tablet)', EHD_PLUGIN_TEXT_DOMAIN),
                ],
                'columns_mobile' => [
                    'type'  => 'number',
                    'min'   => 0,
                    'max'   => '',
                    'std'   => 2,
                    'class' => 'tiny-text',
                    'label' => __('Products per row (mobile)', EHD_PLUGIN_TEXT_DOMAIN),
                ],
                'rows' => [
                    'type'  => 'number',
                    'min'   => 1,
                    'max'   => '',
                    'std'   => 1,
                    'class' => 'tiny-text',
                    'label' => __('Number of rows to show', EHD_PLUGIN_TEXT_DOMAIN),
                ],
                'category'              => [
                    'type'  => 'text',
                    'std'   => '',
                    'label' => __('Product Categories (Id or Slug), separated by commas', EHD_PLUGIN_TEXT_DOMAIN),
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
                'full_width'            => [
                    'type'  => 'checkbox',
                    'std'   => 0,
                    'label' => __('Full Width', EHD_PLUGIN_TEXT_DOMAIN),
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

            // title
            $title = apply_filters('widget_title', $this->get_instance_title($instance), $instance, $this->id_base);

            // class
            $css_class = !empty($instance['css_class']) ? sanitize_title($instance['css_class']) : '';

            $products = $this->get_products( $args, $instance );
            if (!$products || !$products->have_posts()) {
                return;
            }

            $uniqid = esc_attr(uniqid($this->widget_classname . '-'));

            // has products
            wc_set_loop_prop( 'name', 'products_carousel_widget' );

            ob_start();

            ?>
            <section class="section carousel-section products-carousel-section <?= $css_class ?>" id="<?= $uniqid ?>">

                <?php if ($title) echo '<h2 class="heading-title">' . $title . '</h2>'; ?>

                <div class="<?= $uniqid ?>" aria-labelledby="<?php echo esc_attr($title); ?>">

                </div>
            </section>
            <?php

            echo $this->cache_widget($args, ob_get_clean()); // WPCS: XSS ok.
        }
    }
}

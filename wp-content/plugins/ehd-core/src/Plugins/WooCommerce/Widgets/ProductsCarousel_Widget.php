<?php

namespace EHD_Plugins\WooCommerce\Widgets;

use EHD_Cores\Abstract_Widget;
use EHD_Cores\Helper;
use WP_Query;

\defined('ABSPATH') || die;

class ProductsCarousel_Widget extends Abstract_Widget {
    public function __construct() {
        $this->widget_description = __( "A slideshow list of your store's products.", EHD_PLUGIN_TEXT_DOMAIN );
        $this->widget_name        = __( 'W - Products Carousels', EHD_PLUGIN_TEXT_DOMAIN );
        $this->settings           = [
            'title'                 => [
                'type'  => 'text',
                'std'   => __( 'Products Slideshow', EHD_PLUGIN_TEXT_DOMAIN ),
                'label' => __( 'Title', 'woocommerce' ),
            ],
            'desc'                  => [
                'type'  => 'textarea',
                'std'   => '',
                'label' => __( 'Description', EHD_PLUGIN_TEXT_DOMAIN ),
                'desc'  => __( 'Short description of widget', EHD_PLUGIN_TEXT_DOMAIN ),
            ],
            'number'                => [
                'type'  => 'number',
                'min'   => 0,
                'max'   => 99,
                'std'   => 8,
                'class' => 'tiny-text',
                'label' => __( 'Number of products to show', 'woocommerce' ),
            ],
            'columns_number'        => [
                'type'  => 'text',
                'std'   => '4-3-2',
                'label' => __( 'Posts per row', EHD_PLUGIN_TEXT_DOMAIN ),
                'desc'  => __( 'Separated by dashes "-" (3 values)', EHD_PLUGIN_TEXT_DOMAIN ),
            ],
            'gap'                   => [
                'type'  => 'text',
                'std'   => '0-0',
                'label' => __( 'Gap', EHD_PLUGIN_TEXT_DOMAIN ),
                'desc'  => __( 'Separated by dashes "-" (2 values)', EHD_PLUGIN_TEXT_DOMAIN ),
            ],
            'rows'                  => [
                'type'  => 'number',
                'min'   => 1,
                'max'   => '',
                'std'   => 1,
                'class' => 'tiny-text',
                'label' => __( 'Number of rows to show', EHD_PLUGIN_TEXT_DOMAIN ),
            ],
            'category'              => [
                'type'  => 'text',
                'std'   => '',
                'label' => __( 'Product Categories Ids, separated by commas', EHD_PLUGIN_TEXT_DOMAIN ),
                'desc'  => __( 'Separated by dashes (-)', EHD_PLUGIN_TEXT_DOMAIN ),
            ],
            'include_children'      => [
                'type'  => 'checkbox',
                'std'   => 0,
                'label' => __( 'Include Children', EHD_PLUGIN_TEXT_DOMAIN ),
            ],
            'full_width'            => [
                'type'  => 'checkbox',
                'std'   => 0,
                'label' => __( 'Full Width', EHD_PLUGIN_TEXT_DOMAIN ),
            ],
            'navigation'            => [
                'type'  => 'checkbox',
                'std'   => 0,
                'label' => __( 'Navigation', EHD_PLUGIN_TEXT_DOMAIN ),
            ],
            'autoplay'              => [
                'type'  => 'checkbox',
                'std'   => 0,
                'label' => __( 'Autoplay', EHD_PLUGIN_TEXT_DOMAIN ),
            ],
            'loop'                  => [
                'type'  => 'checkbox',
                'std'   => 0,
                'label' => __( 'Loop', EHD_PLUGIN_TEXT_DOMAIN ),
            ],
            'marquee'               => [
                'type'  => 'checkbox',
                'std'   => 0,
                'label' => __( 'Marquee', EHD_PLUGIN_TEXT_DOMAIN ),
            ],
            'scrollbar'             => [
                'type'  => 'checkbox',
                'std'   => 0,
                'label' => __( 'Scrollbar', EHD_PLUGIN_TEXT_DOMAIN ),
            ],
            'direction'             => [
                'type'    => 'select',
                'std'     => '',
                'label'   => __( 'Direction', EHD_PLUGIN_TEXT_DOMAIN ),
                'options' => [
                    ''           => __( 'Default', EHD_PLUGIN_TEXT_DOMAIN ),
                    'horizontal' => __( 'Horizontal', EHD_PLUGIN_TEXT_DOMAIN ),
                    'vertical'   => __( 'Vertical', EHD_PLUGIN_TEXT_DOMAIN ),
                ],
            ],
            'pagination'            => [
                'type'    => 'select',
                'std'     => '',
                'label'   => __( 'Pagination', EHD_PLUGIN_TEXT_DOMAIN ),
                'options' => [
                    ''            => __( 'None', EHD_PLUGIN_TEXT_DOMAIN ),
                    'bullets'     => __( 'Bullets', EHD_PLUGIN_TEXT_DOMAIN ),
                    'fraction'    => __( 'Fraction', EHD_PLUGIN_TEXT_DOMAIN ),
                    'progressbar' => __( 'Progressbar', EHD_PLUGIN_TEXT_DOMAIN ),
                    'custom'      => __( 'Custom', EHD_PLUGIN_TEXT_DOMAIN ),
                ],
            ],
            'effect'                => [
                'type'    => 'select',
                'std'     => '',
                'label'   => __( 'Effect', EHD_PLUGIN_TEXT_DOMAIN ),
                'options' => [
                    ''          => __( 'Default', EHD_PLUGIN_TEXT_DOMAIN ),
                    'slide'     => __( 'Slide', EHD_PLUGIN_TEXT_DOMAIN ),
                    'fade'      => __( 'Fade', EHD_PLUGIN_TEXT_DOMAIN ),
                    'cube'      => __( 'Cube', EHD_PLUGIN_TEXT_DOMAIN ),
                    'coverflow' => __( 'Coverflow', EHD_PLUGIN_TEXT_DOMAIN ),
                    'flip'      => __( 'Flip', EHD_PLUGIN_TEXT_DOMAIN ),
                    'creative'  => __( 'Creative', EHD_PLUGIN_TEXT_DOMAIN ),
                    'cards'     => __( 'Cards', EHD_PLUGIN_TEXT_DOMAIN ),
                ],
            ],
            'delay'                 => [
                'type'  => 'number',
                'min'   => 0,
                'max'   => '',
                'std'   => 6000,
                'label' => __( 'Delay', EHD_PLUGIN_TEXT_DOMAIN ),
            ],
            'speed'                 => [
                'type'  => 'number',
                'min'   => 0,
                'max'   => '',
                'std'   => 1000,
                'label' => __( 'Speed', EHD_PLUGIN_TEXT_DOMAIN ),
            ],
            'show'                  => [
                'type'    => 'select',
                'std'     => '',
                'label'   => __( 'Show', 'woocommerce' ),
                'options' => [
                    ''         => __( 'All products', 'woocommerce' ),
                    'featured' => __( 'Featured products', 'woocommerce' ),
                    'onsale'   => __( 'On-sale products', 'woocommerce' ),
                ],
            ],
            'orderby'               => [
                'type'    => 'select',
                'std'     => '',
                'label'   => __( 'Order by', 'woocommerce' ),
                'options' => [
                    ''      => __( 'Default', 'woocommerce' ),
                    'date'  => __( 'Date', 'woocommerce' ),
                    'price' => __( 'Price', 'woocommerce' ),
                    'rand'  => __( 'Random', 'woocommerce' ),
                    'sales' => __( 'Sales', 'woocommerce' ),
                ],
            ],
            'order'                 => [
                'type'    => 'select',
                'std'     => 'desc',
                'label'   => __( 'Sorting order', EHD_PLUGIN_TEXT_DOMAIN ),
                'options' => [
                    'asc'  => __( 'ASC', EHD_PLUGIN_TEXT_DOMAIN ),
                    'desc' => __( 'DESC', EHD_PLUGIN_TEXT_DOMAIN ),
                ],
            ],
            'hide_free'             => [
                'type'  => 'checkbox',
                'std'   => 0,
                'label' => __( 'Hide free products', 'woocommerce' ),
            ],
            'show_viewmore_button'  => [
                'type'  => 'checkbox',
                'std'   => 0,
                'label' => __( 'Show view more button', EHD_PLUGIN_TEXT_DOMAIN ),
            ],
            'viewmore_button_title' => [
                'type'  => 'text',
                'std'   => __( 'View more', EHD_PLUGIN_TEXT_DOMAIN ),
                'label' => __( 'View more title', EHD_PLUGIN_TEXT_DOMAIN ),
            ],
            'viewmore_button_link'  => [
                'type'  => 'text',
                'std'   => '#',
                'label' => __( 'View more link', EHD_PLUGIN_TEXT_DOMAIN ),
            ],
            'limit_time'            => [
                'type'  => 'text',
                'std'   => '',
                'label' => __( 'Time limit', EHD_PLUGIN_TEXT_DOMAIN ),
                'desc'  => __( 'Restrict to only posts within a specific time period.', EHD_PLUGIN_TEXT_DOMAIN ),
            ],
            'css_class'             => [
                'type'  => 'text',
                'std'   => '',
                'label' => __( 'Css class', EHD_PLUGIN_TEXT_DOMAIN ),
            ],
        ];

        parent::__construct();
    }

    /**
     * @param $number
     *
     * @return void
     */
    public function _register_one( $number = - 1 ): void {
        parent::_register_one( $number );
        if ( $this->registered ) {
            return;
        }
        $this->registered = true;

        // load styles and scripts
        if ( is_active_widget( false, false, $this->id_base ) ) {
            add_action( 'wp_enqueue_scripts', [ &$this, 'styles_and_scripts' ], 12 );
        }
    }

    /**
     * Output widget.
     *
     * @param array $args Arguments.
     * @param array $instance Widget instance.
     */
    public function widget( $args, $instance ) {
        if ( $this->get_cached_widget( $args ) ) {
            return;
        }

        // title
        $title = apply_filters( 'widget_title', $this->get_instance_title( $instance ), $instance, $this->id_base );

        $products = $this->get_products( $args, $instance );
        if ( ! $products || ! $products->have_posts() ) {
            return;
        }

        // class
        $_class    = $this->widget_classname;
        $css_class = ! empty( $instance['css_class'] ) ? sanitize_title( $instance['css_class'] ) : '';

        if ( $css_class ) {
            $_class = $_class . ' ' . $css_class;
        }

        $full_width = ! empty( $instance['full_width'] );
        $uniqid     = esc_attr( uniqid( $this->widget_classname . '-' ) );

        // has products
        wc_set_loop_prop( 'name', 'products_carousel_widget' );

        ob_start();

        ?>
        <section class="section carousel-section products-carousel-section <?= $_class ?>" id="<?= $uniqid ?>">

            <?php if (!$full_width) echo '<div class="grid-container">'; ?>

            <?php if ($title) echo '<h2 class="heading-title">' . $title . '</h2>'; ?>

            <div class="<?= $uniqid ?>" aria-label="<?php echo esc_attr($title); ?>">
                <div class="swiper-section carousel-products grid-products">
                    <?php

                    $number = !empty($instance['number']) ? absint($instance['number']) : $this->settings['number']['std'];
                    $_data = $this->swiperOptions($instance, $this->settings);

                    $swiper_class = $_data['class'];
                    $swiper_data = $_data["data"];

                    ?>
                    <div class="w-swiper swiper">
                        <div class="swiper-wrapper<?= $swiper_class ?>" data-options='<?= $swiper_data ?>'>
                            <?php
                            $i = 0;

                            // Load slides loop
                            while ($products->have_posts() && $i < $number) : $products->the_post();
                                global $product;

                                if (empty($product) || FALSE === wc_get_loop_product_visibility($product->get_id()) || !$product->is_visible()) {
                                    continue;
                                }

                                echo '<div class="swiper-slide">';
                                wc_get_template_part('content', 'product');
                                echo '</div>';
                                ++$i;

                            endwhile;
                            wp_reset_postdata();

                            ?>
                        </div>
                    </div>
                </div>
                <?php
                $show_viewmore_button = !empty($instance['show_viewmore_button']);

                if ($show_viewmore_button) {

                    $viewmore_button_title = $instance['viewmore_button_title'] ?: '';
                    $viewmore_button_link = filter_var($instance['viewmore_button_link'], FILTER_VALIDATE_URL) ? $instance['viewmore_button_link'] : '#';

                    if ($viewmore_button_title) {
                        echo '<a href="' . esc_url($viewmore_button_link) . '" class="viewmore button" title="' . esc_attr($viewmore_button_title) . '">' . $viewmore_button_title . '</a>';
                    }
                }
                ?>
            </div>

            <?php if (!$full_width) echo '</div>'; ?>

        </section>
        <?php

        echo $this->cache_widget($args, ob_get_clean()); // WPCS: XSS ok.
    }

    /**
     * @return void
     */
    public function styles_and_scripts() : void
    {
        wp_enqueue_style( 'ehd-swiper-style' );

        wp_enqueue_script( 'ehd-swiper' );
        wp_script_add_data("ehd-swiper", "defer", true);
    }

    /**
     * Query the products and return them.
     *
     * @param array $args     Arguments.
     * @param array $instance Widget instance.
     *
     * @return WP_Query
     */
    public function get_products($args, $instance) : WP_Query
    {
        $number = !empty($instance['number']) ? absint($instance['number']) : $this->settings['number']['std'];
        $show = !empty($instance['show']) ? sanitize_title($instance['show']) : $this->settings['show']['std'];
        $orderby = !empty($instance['orderby']) ? sanitize_title($instance['orderby']) : $this->settings['orderby']['std'];
        $order = !empty($instance['order']) ? sanitize_title($instance['order']) : $this->settings['order']['std'];
        $limit_time = $instance['limit_time'] ? trim($instance['limit_time']) : $this->settings['limit_time']['std'];

        $product_visibility_term_ids = wc_get_product_visibility_term_ids();

        $query_args = [
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
            'posts_per_page'         => $number,
            'post_status'            => 'publish',
            'post_type'              => 'product',
            'no_found_rows'          => true,
            'ignore_sticky_posts'    => true,
            'order'                  => $order,
            'tax_query'              => ['relation' => 'AND'],
        ]; // WPCS: slow query ok.

        // ...
        if ($limit_time) {

            // constrain to just posts in $limit_time
            $recent = strtotime($limit_time);
            if (Helper::isInteger($recent)) {
                $query_args['date_query'] = [
                    'after' => [
                        'year'  => date('Y', $recent),
                        'month' => date('n', $recent),
                        'day'   => date('j', $recent),
                    ],
                ];
            }
        }

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
        if ('yes' === Helper::getOption('woocommerce_hide_out_of_stock_items')) {
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

        // Product Categories
        $include_children = !empty($instance['include_children']);

        $term_ids = $instance['category'] ?: $this->settings['category']['std'];
        $term_ids = Helper::separatedToArray($term_ids, '-');

        //...
        if ($term_ids) {
            $query_args['tax_query'][] = [
                'taxonomy'         => 'product_cat',
                'field'            => 'term_id',
                'terms'            => $term_ids,
                'include_children' => (bool) $include_children,
            ];
        }

        //...
        return new WP_Query(apply_filters('products_carousel_widget_query_args', $query_args));
    }
}

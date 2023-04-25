<?php

namespace EHD\Plugins\WooCommerce\Widgets;

use EHD\Cores\Helper;
use EHD\Cores\Widget;

\defined('ABSPATH') || die;

class Products_Widget extends Widget {
    public function __construct() {
        $this->widget_description = __( "A list of your store's products.", 'woocommerce' );
        $this->widget_name        = __( 'W - Products', EHD_PLUGIN_TEXT_DOMAIN );
        $this->settings           = [
            'title'                 => [
                'type'  => 'text',
                'std'   => __( 'Products', 'woocommerce' ),
                'label' => __( 'Title', 'woocommerce' ),
            ],
            'number'                => [
                'type'  => 'number',
                'min'   => 0,
                'max'   => 99,
                'std'   => 12,
                'class' => 'tiny-text',
                'label' => __( 'Number of products to show', 'woocommerce' ),
            ],
            'columns'               => [
                'type'  => 'number',
                'min'   => 1,
                'max'   => '',
                'std'   => 4,
                'class' => 'tiny-text',
                'label' => __( 'Products per row', 'woocommerce' ),
            ],
            'orderby'               => [
                'type'    => 'select',
                'std'     => '',
                'label'   => __( 'Order by', 'woocommerce' ),
                'options' => [
                    ''           => __( 'Default', 'woocommerce' ),
                    'menu_order' => __( 'Menu Order', EHD_PLUGIN_TEXT_DOMAIN ),
                    'date'       => __( 'Date', 'woocommerce' ),
                    'rand'       => __( 'Random', 'woocommerce' ),
                    'price'      => __( 'Price', 'woocommerce' ),
                    'popularity' => __( 'Popularity', EHD_PLUGIN_TEXT_DOMAIN ),
                    'rating'     => __( 'Rating', EHD_PLUGIN_TEXT_DOMAIN ),
                    'id'         => __( 'ID', EHD_PLUGIN_TEXT_DOMAIN ),
                ],
            ],
            'order'                 => [
                'type'    => 'select',
                'std'     => 'desc',
                'label'   => __( 'Order', EHD_PLUGIN_TEXT_DOMAIN ),
                'options' => [
                    'asc'  => __( 'ASC', EHD_PLUGIN_TEXT_DOMAIN ),
                    'desc' => __( 'DESC', EHD_PLUGIN_TEXT_DOMAIN ),
                ],
            ],
            'product_attributes'    => [
                'type'    => 'select',
                'std'     => '',
                'label'   => __( 'Display Product Attributes', 'woocommerce' ),
                'options' => [
                    ''             => __( 'Default', 'woocommerce' ),
                    'on_sale'      => __( 'On-sale products', 'woocommerce' ),
                    'best_selling' => __( 'Best-selling products', EHD_PLUGIN_TEXT_DOMAIN ),
                    'top_rated'    => __( 'Top-rated products', EHD_PLUGIN_TEXT_DOMAIN ),
                ],
            ],
            'visibility'            => [
                'type'    => 'select',
                'std'     => '',
                'label'   => __( 'Visibility', 'woocommerce' ),
                'options' => [
                    ''         => __( 'Default', 'woocommerce' ),
                    'catalog'  => __( 'Catalog', EHD_PLUGIN_TEXT_DOMAIN ),
                    'search'   => __( 'Search', EHD_PLUGIN_TEXT_DOMAIN ),
                    'hidden'   => __( 'Hidden', EHD_PLUGIN_TEXT_DOMAIN ),
                    'featured' => __( 'Featured Products', EHD_PLUGIN_TEXT_DOMAIN ),
                ],
            ],
            'category'              => [
                'type'  => 'text',
                'std'   => '',
                'label' => __( 'Product Categories (Id or Slug), separated by commas', EHD_PLUGIN_TEXT_DOMAIN ),
            ],
            'cat_operator'          => [
                'type'    => 'select',
                'std'     => '',
                'label'   => __( 'Cat Operator', EHD_PLUGIN_TEXT_DOMAIN ),
                'options' => [
                    ''       => __( 'Default', 'woocommerce' ),
                    'AND'    => __( 'AND', EHD_PLUGIN_TEXT_DOMAIN ),
                    'IN'     => __( 'IN', EHD_PLUGIN_TEXT_DOMAIN ),
                    'NOT IN' => __( 'NOT IN', EHD_PLUGIN_TEXT_DOMAIN ),
                ],
            ],
            'ids'                   => [
                'type'  => 'text',
                'std'   => '',
                'label' => __( 'Product IDs, separated by commas', EHD_PLUGIN_TEXT_DOMAIN ),
            ],
            'paginate'              => [
                'type'  => 'checkbox',
                'std'   => 0,
                'label' => __( 'Pagination', EHD_PLUGIN_TEXT_DOMAIN ),
            ],
            'full_width'            => [
                'type'  => 'checkbox',
                'std'   => 0,
                'label' => __( 'Full Width', EHD_PLUGIN_TEXT_DOMAIN ),
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
            'css_class'             => [
                'type'  => 'text',
                'std'   => '',
                'label' => __( 'Css class', EHD_PLUGIN_TEXT_DOMAIN ),
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
    public function widget( $args, $instance ) {
        if ( $this->get_cached_widget( $args ) ) {
            return;
        }

        $title   = apply_filters( 'widget_title', $this->get_instance_title( $instance ), $instance, $this->id_base );
        $number  = ! empty( $instance['number'] ) ? absint( $instance['number'] ) : 0;
        $columns = ! empty( $instance['columns'] ) ? absint( $instance['columns'] ) : 1;
        $order   = ! empty( $instance['order'] ) ? sanitize_title( $instance['order'] ) : $this->settings['order']['std'];;

        $query_args = [
            'limit'   => $number,
            'columns' => $columns,
            'order'   => $order,
            'title'   => wp_kses_post( $title ),
        ];

        // Orderby
        $orderby = ! empty( $instance['orderby'] ) ? sanitize_title( $instance['orderby'] ) : $this->settings['orderby']['std'];
        if ( $orderby ) {
            $query_args['orderby'] = $orderby;
        }

        // Display Product Attributes
        $product_attributes = ! empty( $instance['product_attributes'] ) ? sanitize_title( $instance['product_attributes'] ) : $this->settings['product_attributes']['std'];
        if ( $product_attributes ) {
            $query_args[ $product_attributes ] = $product_attributes;
        }

        // Visibility
        $visibility = ! empty( $instance['visibility'] ) ? sanitize_title( $instance['visibility'] ) : $this->settings['visibility']['std'];
        if ( $visibility ) {
            $query_args['visibility '] = $visibility;
        }

        // Product Categories
        $category = ! empty( $instance['category'] ) ? sanitize_title( $instance['category'] ) : $this->settings['category']['std'];
        if ( $category ) {
            $query_args['category '] = $category;
        }

        // Cat Operator
        $cat_operator = ! empty( $instance['cat_operator'] ) ? sanitize_title( $instance['cat_operator'] ) : $this->settings['cat_operator']['std'];
        if ( $cat_operator ) {
            $query_args['cat_operator'] = $cat_operator;
        }

        // Product IDs
        $ids = ! empty( $instance['ids'] ) ? sanitize_title( $instance['ids'] ) : $this->settings['ids']['std'];
        if ( $ids ) {
            $query_args['ids'] = $ids;
        }

        // Toggle Pagination
        $paginate = empty( $instance['paginate'] ) ? 'false' : 'true';
        if ( 'true' == $paginate ) {
            $query_args['paginate'] = $paginate;
        }

        // class
        $_class    = $this->widget_classname . ' ' . $this->id;
        $css_class = ! empty( $instance['css_class'] ) ? sanitize_title( $instance['css_class'] ) : '';

        if ( $css_class ) {
            $_class = $_class . ' ' . $css_class;
        }

        //...
        $full_width           = ! empty( $instance['full_width'] );
        $uniqid = esc_attr( uniqid( $this->widget_classname . '-' ) );

        // has products
        wc_set_loop_prop( 'name', 'products_widget' );

        ob_start();

        ?>
        <section class="section products-section <?= $_class ?>" id="<?= $uniqid ?>">

            <?php if (!$full_width) echo '<div class="grid-container">'; ?>

            <?php if ($title) echo '<h2 class="heading-title">' . $title . '</h2>'; ?>

            <div class="<?= $uniqid ?>" aria-label="<?php echo esc_attr($title); ?>">
                <?php
                echo Helper::doShortcode(
                    'products',
                    $query_args
                );
                ?>
            </div>

            <?php
            $show_viewmore_button = ! empty( $instance['show_viewmore_button'] );

            if ($show_viewmore_button) {
                $viewmore_button_title = $instance['viewmore_button_title'] ?: '';
                $viewmore_button_link = filter_var($instance['viewmore_button_link'], FILTER_VALIDATE_URL) ? $instance['viewmore_button_link'] : '#';
                if ($viewmore_button_title) {
                    echo '<a href="' . esc_url($viewmore_button_link) . '" class="viewmore button" title="' . esc_attr($viewmore_button_title) . '">' . $viewmore_button_title . '</a>';
                }
            }
            ?>

            <?php if (!$full_width) echo '</div>'; ?>

        </section>
        <?php

        echo $this->cache_widget($args, ob_get_clean()); // WPCS: XSS ok.
    }
}

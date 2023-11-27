<?php

namespace EHD_Plugins\WooCommerce\Widgets;

use EHD_Cores\Abstract_Widget;
use EHD_Cores\Helper;

\defined('ABSPATH') || die;

class Products_Widget extends Abstract_Widget {
    public function __construct() {
	    $this->widget_description = __( "A list of your store's products.", EHD_PLUGIN_TEXT_DOMAIN );
	    $this->widget_name        = __( 'Products *', EHD_PLUGIN_TEXT_DOMAIN );
	    $this->settings           = [
		    'title'              => [
			    'type'  => 'text',
			    'std'   => __( 'Products', EHD_PLUGIN_TEXT_DOMAIN ),
			    'label' => __( 'Title', EHD_PLUGIN_TEXT_DOMAIN ),
		    ],
		    'limit'              => [
			    'type'  => 'number',
			    'min'   => 0,
			    'max'   => '',
			    'std'   => 12,
			    'label' => __( 'Maximum number of products (limit)', EHD_PLUGIN_TEXT_DOMAIN ),
			    'desc'  => __( 'The number of products to display, -1 (display all)', EHD_PLUGIN_TEXT_DOMAIN ),
		    ],
		    'columns'            => [
			    'type'  => 'number',
			    'min'   => 1,
			    'max'   => '',
			    'std'   => 4,
			    'label' => __( 'Number of products per Row (columns)', EHD_PLUGIN_TEXT_DOMAIN ),
			    'desc'  => __( 'The number of columns to display. Defaults to 4', EHD_PLUGIN_TEXT_DOMAIN )
		    ],
		    'container'          => [
			    'type'  => 'checkbox',
			    'std'   => 0,
			    'label' => __( 'Container layout', EHD_PLUGIN_TEXT_DOMAIN ),
		    ],
		    'paginate'           => [
			    'type'  => 'checkbox',
			    'std'   => 0,
			    'class' => 'checkbox',
			    'label' => __( 'Toggles pagination (paginate)', EHD_PLUGIN_TEXT_DOMAIN ),
		    ],
		    'visibility_featured'          => [
			    'type'  => 'checkbox',
			    'std'   => 0,
			    'label' => __( 'Featured products', EHD_PLUGIN_TEXT_DOMAIN ),
		    ],
		    'product_attributes' => [
			    'type'    => 'select',
			    'std'     => 'none',
			    'label'   => __( 'Show', EHD_PLUGIN_TEXT_DOMAIN ),
			    'options' => [
				    'none'      => __( 'No selection', EHD_PLUGIN_TEXT_DOMAIN ),
				    'on_sale'      => 'on_sale',
				    'best_selling' => 'best_selling',
				    'top_rated'    => 'top_rated',
			    ],
		    ],
		    'orderby'            => [
			    'type'    => 'select',
			    'std'     => 'title',
			    'label'   => __( 'Order by', EHD_PLUGIN_TEXT_DOMAIN ),
			    'options' => [
				    'title'      => 'title',
				    'date'       => 'date',
				    'id'         => 'id',
				    'menu_order' => 'menu_order',
				    'popularity' => 'popularity',
				    'rand'       => 'rand',
				    'rating'     => 'rating',
			    ],
		    ],
		    'order'              => [
			    'type'    => 'select',
			    'std'     => 'desc',
			    'label'   => __( 'Order', EHD_PLUGIN_TEXT_DOMAIN ),
			    'options' => [
				    'asc'  => __( 'ASC', EHD_PLUGIN_TEXT_DOMAIN ),
				    'desc' => __( 'DESC', EHD_PLUGIN_TEXT_DOMAIN ),
			    ],
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

	    $title = $this->get_instance_title( $instance );

        $limit  = ! empty( $instance['limit'] ) ? absint( $instance['limit'] ) : $this->settings['limit']['std'];
        $columns = ! empty( $instance['columns'] ) ? absint( $instance['columns'] ) : $this->settings['columns']['std'];
        $order   = ! empty( $instance['order'] ) ? sanitize_title( $instance['order'] ) : $this->settings['order']['std'];

        $query_args = [
            'limit'   => $limit,
            'columns' => $columns,
            'order'   => $order,
            'title'   => wp_kses_post( $title ),
        ];

	    $container = ! empty( $instance['container'] );

        // orderby
        $orderby = ! empty( $instance['orderby'] ) ? sanitize_title( $instance['orderby'] ) : $this->settings['orderby']['std'];
        if ( $orderby ) {
            $query_args['orderby'] = $orderby;
        }

        // visibility featured
        $visibility_featured = ! empty( $instance['visibility_featured'] ) ? sanitize_title( $instance['visibility_featured'] ) : $this->settings['visibility_featured']['std'];
        if ( $visibility_featured ) {
            $query_args['visibility'] = 'featured';
        }

	    // Product Attributes
	    $product_attributes = ! empty( $instance['product_attributes'] ) ? sanitize_title( $instance['product_attributes'] ) : $this->settings['product_attributes']['std'];
	    if ( 'none' != $product_attributes ) {
		    $query_args[ $product_attributes ] = true;
	    }

	    // Toggle Pagination
	    $paginate = empty( $instance['paginate'] ) ? 'false' : 'true';
	    if ( 'true' == $paginate ) {
		    $query_args['paginate'] = $paginate;
	    }

        //-----------------------------------------------------

	    // ACF
	    $ACF = $this->acfFields( 'widget_' . $args['widget_id'] );

	    $categories_arr     = $ACF->category ?? [];
	    $ids_arr     = $ACF->ids ?? [];

        if ( $categories_arr ) {
            $category = [];
            foreach ( $categories_arr as $cat ) {
                $category[] = $cat->slug;
            }

            if ( $category ) {
	            $query_args['category '] = implode( ',', $category);
	            $query_args['cat_operator'] = $ACF->cat_operator ?? 'IN';
            }
        }

        if ( $ids_arr ) {
	        $query_args['ids'] = implode( ',', $ids_arr);
        }

	    $heading_tag   = ! empty( $ACF->title_tag ) ? $ACF->title_tag : 'span';
	    $heading_class = ! empty( $ACF->title_classes ) ? $ACF->title_classes : 'heading-title';

	    $show_view_more_button = $ACF->show_view_more_button ?? false;
	    $view_more_link        = $ACF->view_more_link ?? '';
	    $view_more_link        = Helper::ACF_Link( $view_more_link );

	    $css_class = ! empty( $ACF->css_class ) ? ' ' . sanitize_title( $ACF->css_class ) : '';
	    $css_class = $this->widget_classname . $css_class;
	    $uniqid    = esc_attr( uniqid( $this->widget_classname . '-' ) );

	    //-----------------------------------------------------

	    wc_set_loop_prop( 'name', 'products_widget' );

	    ob_start();

        ?>
        <section class="section products-section <?= $css_class ?>">
            <?php
            if ( $container ) echo '<div class="grid-container">';

            if ( $title ) {
	            $args['before_title'] = '<' . $heading_tag . ' class="' . $heading_class . '">';
	            $args['after_title'] = '</' . $heading_tag . '>';

	            echo $args['before_title'] . $title . $args['after_title'];
            }
            ?>
            <div class="<?= $uniqid ?>" aria-label="<?php echo esc_attr( $title ); ?>">
                <?php
                echo Helper::doShortcode(
                    'products',
                    $query_args
                );
                ?>
            </div>
	        <?php

	        if ( $show_view_more_button ) echo $view_more_link;
	        if ( $container ) echo '</div>';

	        ?>
        </section>
        <?php
        echo $this->cache_widget($args, ob_get_clean()); // WPCS: XSS ok.
    }
}

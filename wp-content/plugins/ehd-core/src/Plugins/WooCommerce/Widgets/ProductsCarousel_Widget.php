<?php

namespace EHD_Plugins\WooCommerce\Widgets;

use EHD_Cores\Abstract_Widget;
use EHD_Cores\Helper;
use WP_Query;

\defined( 'ABSPATH' ) || die;

class ProductsCarousel_Widget extends Abstract_Widget {
	public function __construct() {
		$this->widget_description = __( "A slideshow list of your store's products.", EHD_PLUGIN_TEXT_DOMAIN );
		$this->widget_name        = __( 'Products Carousels *', EHD_PLUGIN_TEXT_DOMAIN );
		$this->settings           = [
			'title'                 => [
				'type'  => 'text',
				'std'   => __( 'Products slideshow', EHD_PLUGIN_TEXT_DOMAIN ),
				'label' => __( 'Title', EHD_PLUGIN_TEXT_DOMAIN ),
			],
			'number'     => [
				'type'  => 'number',
				'min'   => 0,
				'max'   => 99,
				'std'   => 5,
				'class' => 'tiny-text',
				'label' => __( 'Number of products to show', EHD_PLUGIN_TEXT_DOMAIN ),
			],
			'container'          => [
				'type'  => 'checkbox',
				'std'   => 0,
				'label' => __( 'Container layout', EHD_PLUGIN_TEXT_DOMAIN ),
			],
			'show'       => [
				'type'    => 'select',
				'std'     => '',
				'label'   => __( 'Show', EHD_PLUGIN_TEXT_DOMAIN ),
				'options' => [
					''         => __( 'All', EHD_PLUGIN_TEXT_DOMAIN ),
					'featured' => __( 'Featured', EHD_PLUGIN_TEXT_DOMAIN ),
					'on_sale'   => __( 'On-sale', EHD_PLUGIN_TEXT_DOMAIN ),
				],
			],
			'orderby'    => [
				'type'    => 'select',
				'std'     => '',
				'label'   => __( 'Order by', EHD_PLUGIN_TEXT_DOMAIN ),
				'options' => [
					''      => __( 'Default', EHD_PLUGIN_TEXT_DOMAIN ),
					'date'  => __( 'Date', EHD_PLUGIN_TEXT_DOMAIN ),
					'price' => __( 'Price', EHD_PLUGIN_TEXT_DOMAIN ),
					'rand'  => __( 'Random', EHD_PLUGIN_TEXT_DOMAIN ),
					'sales' => __( 'Sales', EHD_PLUGIN_TEXT_DOMAIN ),
				],
			],
			'order'      => [
				'type'    => 'select',
				'std'     => 'desc',
				'label'   => __( 'Sorting order', EHD_PLUGIN_TEXT_DOMAIN ),
				'options' => [
					'asc'  => __( 'ASC', EHD_PLUGIN_TEXT_DOMAIN ),
					'desc' => __( 'DESC', EHD_PLUGIN_TEXT_DOMAIN ),
				],
			],
			'limit_time' => [
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Time limit', EHD_PLUGIN_TEXT_DOMAIN ),
				'desc'  => __( 'Restrict to only posts within a specific time period.', EHD_PLUGIN_TEXT_DOMAIN ),
			],
		];

		parent::__construct();
	}

	/**
	 * Query the products and return them.
	 *
	 * @param array $args Arguments.
	 * @param array $instance Widget instance.
	 * @param $ACF
	 *
	 * @return WP_Query
	 */
	public function get_products( $args, $instance, $ACF ): WP_Query {

		$number     = ! empty( $instance['number'] ) ? absint( $instance['number'] ) : $this->settings['number']['std'];
		$show       = ! empty( $instance['show'] ) ? sanitize_title( $instance['show'] ) : $this->settings['show']['std'];
		$orderby    = ! empty( $instance['orderby'] ) ? sanitize_title( $instance['orderby'] ) : $this->settings['orderby']['std'];
		$order      = ! empty( $instance['order'] ) ? sanitize_title( $instance['order'] ) : $this->settings['order']['std'];

		$limit_time = $instance['limit_time'] ? trim( $instance['limit_time'] ) : $this->settings['limit_time']['std'];
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
			'tax_query'              => [ 'relation' => 'AND' ],
		]; // WPCS: slow query ok.

		// ...
		if ( $limit_time ) {

			// constrain to just posts in $limit_time
			$recent = strtotime( $limit_time );
			if ( Helper::isInteger( $recent ) ) {
				$query_args['date_query'] = [
					'after' => [
						'year'  => date( 'Y', $recent ),
						'month' => date( 'n', $recent ),
						'day'   => date( 'j', $recent ),
					],
				];
			}
		}

		// woocommerce_hide_out_of_stock_items
		if ( 'yes' === Helper::getOption( 'woocommerce_hide_out_of_stock_items' ) ) {
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
		switch ( $show ) {
			case 'featured':
				$query_args['tax_query'][] = [
					'taxonomy' => 'product_visibility',
					'field'    => 'term_taxonomy_id',
					'terms'    => $product_visibility_term_ids['featured'],
				];
				break;
			case 'on_sale':
				$product_ids_on_sale    = wc_get_product_ids_on_sale();
				$product_ids_on_sale[]  = 0;
				$query_args['post__in'] = $product_ids_on_sale;
				break;
		}

		// orderby
		switch ( $orderby ) {
			case 'price':
				$query_args['meta_key'] = '_price'; // WPCS: slow query ok.
				$query_args['orderby']  = 'meta_value_num';
				break;
			case 'rand':
				$query_args['orderby'] = 'rand';
				break;
			case 'sales':
				$query_args['meta_key'] = 'total_sales'; // WPCS: slow query ok.
				$query_args['orderby']  = 'meta_value_num';
				break;
			case 'date':
				$query_args['orderby'] = 'date';
				break;
		}

		//-----------------------------------------------------

		$term_ids = $ACF->product_category_ids ?? [];
		if ( $term_ids ) {
			$query_args['tax_query'][] = [
				'taxonomy'         => 'product_cat',
				'field'            => 'term_id',
				'terms'            => $term_ids,
				'include_children' => true,
			];
		}

		//...
		return new WP_Query( apply_filters( 'products_carousel_widget_query_args', $query_args ) );
	}

	/**
	 * @return void
	 */
	public function styles_and_scripts(): void {
		wp_enqueue_style( 'ehd-swiper-style' );

		wp_enqueue_script( 'ehd-swiper' );
		wp_script_add_data( "ehd-swiper", "defer", true );
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

		$title = $this->get_instance_title( $instance );

		$number  = ! empty( $instance['number'] ) ? absint( $instance['number'] ) : $this->settings['number']['std'];
		$container = ! empty( $instance['container'] );

		// ACF
		$ACF = $this->acfFields( 'widget_' . $args['widget_id'] );

		$heading_tag   = ! empty( $ACF->title_tag ) ? $ACF->title_tag : 'span';
		$heading_class = ! empty( $ACF->title_classes ) ? $ACF->title_classes : 'heading-title';

		$show_view_more_button = $ACF->show_view_more_button ?? false;
		$view_more_link        = $ACF->view_more_link ?? '';
		$view_more_link        = Helper::ACF_Link( $view_more_link );

		$css_class = ! empty( $ACF->css_class ) ? ' ' . sanitize_title( $ACF->css_class ) : '';
		$css_class = $this->widget_classname . $css_class;
		$uniqid    = esc_attr( uniqid( $this->widget_classname . '-' ) );

        // products list
		$products = $this->get_products( $args, $instance, $ACF );
		if ( ! $products->have_posts() ) {
			return;
		}

		//-----------------------------------------------------

		wc_set_loop_prop( 'name', 'products_carousel_widget' );

		ob_start();

		?>
        <section class="section carousel-section products-carousel-section <?= $css_class ?>">
	        <?php
	        if ( $container ) echo '<div class="grid-container">';

	        if ( $title ) {
		        $args['before_title'] = '<' . $heading_tag . ' class="' . $heading_class . '">';
		        $args['after_title'] = '</' . $heading_tag . '>';

		        echo $args['before_title'] . $title . $args['after_title'];
	        }

	        ?>
            <div class="<?= $uniqid ?>" aria-label="<?php echo esc_attr( $title ); ?>">
                <div class="swiper-section carousel-products grid-products">
	                <?php
	                $_data = $this->swiper_acf_options( $instance, $ACF );

	                $swiper_class = $_data['class'] ?? '';
	                $swiper_data  = $_data['data'] ?? json_encode( [], JSON_FORCE_OBJECT );

	                ?>
                    <div class="w-swiper swiper">
                        <div class="swiper-wrapper<?= $swiper_class ?>" data-options='<?= $swiper_data ?>'>
							<?php
							$i = 0;

							// Load loop
							while ( $products->have_posts() && $i < $number ) : $products->the_post();
								global $product;

								if ( empty( $product ) || false === wc_get_loop_product_visibility( $product->get_id() ) || ! $product->is_visible() ) {
									continue;
								}

								echo '<div class="swiper-slide">';
								wc_get_template_part( 'content', 'product' );
								echo '</div>';
								++ $i;

							endwhile;
							wp_reset_postdata();

							?>
                        </div>
                    </div>
                </div>
            </div>
	        <?php

	        if ( $show_view_more_button ) echo $view_more_link;
	        if ( $container ) echo '</div>';

	        ?>
        </section>
		<?php
		echo $this->cache_widget( $args, ob_get_clean() ); // WPCS: XSS ok.
	}
}

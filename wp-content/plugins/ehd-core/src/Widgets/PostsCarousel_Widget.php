<?php

namespace EHD_Widgets;

use EHD_Cores\Abstract_Widget;
use EHD_Cores\Helper;

\defined( 'ABSPATH' ) || die;

class PostsCarousel_Widget extends Abstract_Widget {
	public function __construct() {
		$this->widget_description = __( 'Your site&#8217;s Posts Carousels.' );
		$this->widget_name        = __( 'Posts Carousels *', EHD_PLUGIN_TEXT_DOMAIN );
		$this->settings           = [
			'title'                 => [
				'type'  => 'text',
				'std'   => __( 'Posts slideshow', EHD_PLUGIN_TEXT_DOMAIN ),
				'label' => __( 'Title', EHD_PLUGIN_TEXT_DOMAIN ),
			],
			'desc'                  => [
				'type'  => 'textarea',
				'std'   => '',
                'rows' => 3,
				'label' => __( 'Description', EHD_PLUGIN_TEXT_DOMAIN ),
				//'desc'  => __( 'Short description of widget', EHD_PLUGIN_TEXT_DOMAIN ),
			],
			'container'            => [
				'type'  => 'checkbox',
				'std'   => 0,
				'label' => __( 'Container', EHD_PLUGIN_TEXT_DOMAIN ),
			],
			'include_children'      => [
				'type'  => 'checkbox',
				'std'   => 0,
				'label' => __( 'Include children', EHD_PLUGIN_TEXT_DOMAIN ),
			],
			'show_cat'              => [
				'type'  => 'checkbox',
				'std'   => '',
				'class' => 'checkbox',
				'label' => __( 'Display post categories', EHD_PLUGIN_TEXT_DOMAIN ),
			],
			'show_thumbnail'        => [
				'type'  => 'checkbox',
				'std'   => '',
				'class' => 'checkbox',
				'label' => __( 'Display post thumbnails', EHD_PLUGIN_TEXT_DOMAIN ),
			],
			'show_date'             => [
				'type'  => 'checkbox',
				'std'   => '',
				'class' => 'checkbox',
				'label' => __( 'Display post date', EHD_PLUGIN_TEXT_DOMAIN ),
			],
			'show_desc'             => [
				'type'  => 'checkbox',
				'std'   => '',
				'class' => 'checkbox',
				'label' => __( 'Display post description', EHD_PLUGIN_TEXT_DOMAIN ),
			],
			'show_detail_button'             => [
				'type'  => 'checkbox',
				'std'   => '',
				'class' => 'checkbox',
				'label' => __( 'Display detail button', EHD_PLUGIN_TEXT_DOMAIN ),
			],
			'number'                => [
				'type'  => 'number',
				'min'   => 0,
				'max'   => 99,
				'std'   => 12,
				'class' => 'tiny-text',
				'label' => __( 'Maximum number of posts', EHD_PLUGIN_TEXT_DOMAIN ),
			],
			'limit_time'            => [
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Time limit', EHD_PLUGIN_TEXT_DOMAIN ),
				'desc'  => __( 'Restrict to only posts within a specific time period.', EHD_PLUGIN_TEXT_DOMAIN ),
			],
		];

		parent::__construct();
	}

	/**
	 * @return void
	 */
	public function styles_and_scripts() {
		wp_enqueue_style( 'ehd-swiper-style' );

		wp_enqueue_script( 'ehd-swiper' );
		wp_script_add_data( "ehd-swiper", "defer", true );
	}

	/**
	 * Outputs the content for the posts widget instance.
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		if ( $this->get_cached_widget( $args ) ) {
			return;
		}

		$ACF = $this->acfFields( 'widget_' . $args['widget_id'] );

		$title = $this->get_instance_title( $instance );
		$desc  = $instance['desc'] ? trim( $instance['desc'] ) : '';

		$container            = ! empty( $instance['container'] );
		$number               = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : $this->settings['number']['std'];
		$show_cat             = ! empty( $instance['show_cat'] );
		$show_thumbnail       = ! empty( $instance['show_thumbnail'] );
		$show_date            = ! empty( $instance['show_date'] );
		$show_desc            = ! empty( $instance['show_desc'] );
		$show_viewmore_button = ! empty( $instance['show_viewmore_button'] );

		$include_children = ! empty( $instance['include_children'] );
		$limit_time       = $instance['limit_time'] ? trim( $instance['limit_time'] ) : $this->settings['limit_time']['std'];

		// ACF fields
		$heading_tag   = ! empty( $ACF->title_tag ) ? $ACF->title_tag : 'span';
		$heading_class = ! empty( $ACF->title_classes ) ? $ACF->title_classes : 'heading-title';

		$term_ids = $ACF->post_category_ids ?? [];

		$show_view_more_button = $ACF->show_view_more_button ?? false;
		$view_more_link        = $ACF->view_more_link ?? '';
		$view_more_link        = Helper::ACF_Link( $view_more_link );

		$query_args = [
			'term_ids'         => $term_ids,
			'include_children' => $include_children,
			'posts_per_page'   => $number,
			'limit_time'       => $limit_time,
			'wrapper'          => 'div',
			'wrapper_class'    => 'swiper-slide',
			'show'             => [
				'thumbnail' => Helper::toBool( $show_thumbnail ),
				//'thumbnail_size' => 'medium',
				//'scale' => true,
				'time'      => Helper::toBool( $show_date ),
				'term'      => Helper::toBool( $show_cat ),
				'desc'      => Helper::toBool( $show_desc ),
				'more'      => Helper::toBool( $show_viewmore_button ),
			],
		];

		$css_class = ! empty( $ACF->css_class ) ? ' ' . sanitize_title( $ACF->css_class ) : '';
		$css_class = $this->widget_classname . $css_class;
		$uniqid    = esc_attr( uniqid( $this->widget_classname . '-' ) );

		ob_start();

		?>
        <section class="section carousel-section posts-carousel-section posts-section <?= $css_class ?>">
			<?php
            if ( $container ) echo '<div class="grid-container">';

            if ( $title ) {
	            $args['before_title'] = '<' . $heading_tag . ' class="' . $heading_class . '">';
	            $args['after_title'] = '</' . $heading_tag . '>';

	            echo $args['before_title'] . $title . $args['after_title'];
            }

			if ( $desc ) echo '<p class="heading-desc">' . $desc . '</p>';

			?>
            <div class="<?= $uniqid ?>" aria-label="<?php echo esc_attr( $title ); ?>">
                <div class="swiper-section carousel-posts grid-posts">
					<?php
					$_data = $this->swiperOptions( $instance, $this->settings );

					$swiper_class = $_data['class'];
					$swiper_data  = $_data['data'];

					?>
                    <div class="w-swiper swiper">
                        <div class="swiper-wrapper<?= $swiper_class ?>" data-options='<?= $swiper_data ?>'>
							<?php
							echo Helper::doShortcode(
								'posts',
								$query_args
							);
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

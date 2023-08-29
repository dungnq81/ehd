<?php

namespace EHD_Widgets;

use EHD_Cores\Abstract_Widget;
use EHD_Cores\Helper;

\defined('ABSPATH') || die;

class Posts_Widget extends Abstract_Widget {
	public function __construct() {
		$this->widget_description = __( 'Your site&#8217;s Posts.' );
		$this->widget_name        = __( 'W - Posts', EHD_PLUGIN_TEXT_DOMAIN );
		$this->settings           = [
			'title'                 => [
				'type'  => 'text',
				'std'   => __( 'Posts', EHD_PLUGIN_TEXT_DOMAIN ),
				'label' => __( 'Title', EHD_PLUGIN_TEXT_DOMAIN ),
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
				'std'   => 12,
				'class' => 'tiny-text',
				'label' => __( 'Number of posts to show', EHD_PLUGIN_TEXT_DOMAIN ),
			],
			'category'              => [
				'type'  => 'text',
				'std'   => '',
				'class' => '',
				'label' => __( 'Posts categories ids, separated by commas', EHD_PLUGIN_TEXT_DOMAIN ),
				'desc'  => __( 'Separated by commas (,)', EHD_PLUGIN_TEXT_DOMAIN ),
			],
			'full_width'            => [
				'type'  => 'checkbox',
				'std'   => 0,
				'label' => __( 'Full width', EHD_PLUGIN_TEXT_DOMAIN ),
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
				'label' => __( 'Display post categories?', EHD_PLUGIN_TEXT_DOMAIN ),
			],
			'show_thumbnail'        => [
				'type'  => 'checkbox',
				'std'   => '',
				'class' => 'checkbox',
				'label' => __( 'Display post thumbnails?', EHD_PLUGIN_TEXT_DOMAIN ),
			],
			'show_date'             => [
				'type'  => 'checkbox',
				'std'   => '',
				'class' => 'checkbox',
				'label' => __( 'Display post date?', EHD_PLUGIN_TEXT_DOMAIN ),
			],
			'show_desc'             => [
				'type'  => 'checkbox',
				'std'   => '',
				'class' => 'checkbox',
				'label' => __( 'Display post description?', EHD_PLUGIN_TEXT_DOMAIN ),
			],
			'limit_time'            => [
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Time limit', EHD_PLUGIN_TEXT_DOMAIN ),
				'desc'  => __( 'Restrict to only posts within a specific time period.', EHD_PLUGIN_TEXT_DOMAIN ),
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
     * Outputs the content for the posts widget instance.
     *
     * @param array $args
     * @param array $instance
     */
    public function widget( $args, $instance ) {
        if ( $this->get_cached_widget( $args ) ) {
            return;
        }

        $title = apply_filters( 'widget_title', $this->get_instance_title( $instance ), $instance, $this->id_base );
        $desc  = $instance['desc'] ? trim( $instance['desc'] ) : '';

        $number               = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 12;
        $show_cat             = ! empty( $instance['show_cat'] );
        $show_thumbnail       = ! empty( $instance['show_thumbnail'] );
        $show_date            = ! empty( $instance['show_date'] );
        $show_desc            = ! empty( $instance['show_desc'] );
        $show_viewmore_button = ! empty( $instance['show_viewmore_button'] );

        $include_children = ! empty( $instance['include_children'] );
        $limit_time       = $instance['limit_time'] ? trim( $instance['limit_time'] ) : '';

        $term_ids = $instance['category'] ?: $this->settings['category']['std'];
        $term_ids = Helper::separatedToArray( $term_ids, ',' );

        $query_args = [
            'term_ids'         => $term_ids,
            'include_children' => $include_children,
            'posts_per_page'   => $number,
            'limit_time'       => $limit_time,
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

        //$_class = $this->widget_classname . ' ' . $this->id;
        $_class    = $this->widget_classname;
        $css_class = ( ! empty( $instance['css_class'] ) ) ? sanitize_title( $instance['css_class'] ) : '';
        if ( $css_class ) {
            $_class = $_class . ' ' . $css_class;
        }

        $full_width = ! empty( $instance['full_width'] );
        $uniqid     = esc_attr( uniqid( $this->widget_classname . '-' ) );

        ob_start();

        ?>
    <section class="section posts-section <?= $_class ?>" id="<?= $uniqid ?>">

        <?php if (!$full_width) echo '<div class="grid-container">'; ?>

        <?php if ($title) echo '<h2 class="heading-title">' . $title . '</h2>'; ?>
        <?php if ($desc) echo '<p class="heading-desc">' . $desc . '</p>'; ?>

        <div class="<?= $uniqid ?>" aria-label="<?php echo esc_attr($title); ?>">
            <div class="grid-posts grid-x">
                <?php
                echo Helper::doShortcode(
                    'posts',
                    $query_args
                );
                ?>
            </div>
        </div>
        <?php

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

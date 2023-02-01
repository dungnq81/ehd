<?php

namespace EHD\Widgets;

use EHD\Cores\Helper;
use EHD\Cores\Widget;
use WP_Query;

\defined('ABSPATH') || die;

if (!class_exists('PostsCarousel_Widget')) {
    class PostsCarousel_Widget extends Widget
    {
        public function __construct()
        {
            $this->widget_description = __('Your site&#8217;s Posts Carousels.');
            $this->widget_name = __('W - Posts Carousels', EHD_PLUGIN_TEXT_DOMAIN);
            $this->settings = [
                'title'                 => [
                    'type'  => 'text',
                    'std'   => __('Posts Slideshow', EHD_PLUGIN_TEXT_DOMAIN),
                    'label' => __('Title', EHD_PLUGIN_TEXT_DOMAIN),
                ],
                'desc'                  => [
                    'type'  => 'textarea',
                    'std'   => '',
                    'label' => __('Description', EHD_PLUGIN_TEXT_DOMAIN),
                    'desc'  => __('Short description of widget', EHD_PLUGIN_TEXT_DOMAIN),
                ],
                'number'                => [
                    'type'  => 'number',
                    'min'   => 0,
                    'max'   => 99,
                    'std'   => 8,
                    'class' => 'tiny-text',
                    'label' => __('Number of posts to show', EHD_PLUGIN_TEXT_DOMAIN),
                ],
                'columns_number'       => [
                    'type'  => 'text',
                    'std'   => '4-3-2',
                    'label' => __('Posts per row', EHD_PLUGIN_TEXT_DOMAIN),
                    'desc' => __('Separated by dashes "-" (3 values)', EHD_PLUGIN_TEXT_DOMAIN),
                ],
                'gap'           => [
                    'type'  => 'text',
                    'std'   => '0-0',
                    'label' => __('Gap', EHD_PLUGIN_TEXT_DOMAIN),
                    'desc' => __('Separated by dashes "-" (2 values)', EHD_PLUGIN_TEXT_DOMAIN),
                ],
                'rows'                  => [
                    'type'  => 'number',
                    'min'   => 1,
                    'max'   => '',
                    'std'   => 1,
                    'class' => 'tiny-text',
                    'label' => __('Rows', EHD_PLUGIN_TEXT_DOMAIN),
                ],
                'category'              => [
                    'type'  => 'text',
                    'std'   => '',
                    'label' => __('Posts Categories Ids', EHD_PLUGIN_TEXT_DOMAIN),
                    'desc' => __('Separated by dashes (-)', EHD_PLUGIN_TEXT_DOMAIN),
                ],
                'include_children'      => [
                    'type'  => 'checkbox',
                    'std'   => 0,
                    'label' => __('Include Children', EHD_PLUGIN_TEXT_DOMAIN),
                ],
                'full_width'            => [
                    'type'  => 'checkbox',
                    'std'   => 0,
                    'label' => __('Full Width', EHD_PLUGIN_TEXT_DOMAIN),
                ],
                'navigation'            => [
                    'type'  => 'checkbox',
                    'std'   => 0,
                    'label' => __('Navigation', EHD_PLUGIN_TEXT_DOMAIN),
                ],
                'autoplay'              => [
                    'type'  => 'checkbox',
                    'std'   => 0,
                    'label' => __('Autoplay', EHD_PLUGIN_TEXT_DOMAIN),
                ],
                'loop'                  => [
                    'type'  => 'checkbox',
                    'std'   => 0,
                    'label' => __('Loop', EHD_PLUGIN_TEXT_DOMAIN),
                ],
                'pagination'            => [
                    'type'    => 'select',
                    'std'     => '',
                    'label'   => __('Pagination', EHD_PLUGIN_TEXT_DOMAIN),
                    'options' => [
                        ''         => __('None', EHD_PLUGIN_TEXT_DOMAIN),
                        'dynamic'  => __('Dynamic', EHD_PLUGIN_TEXT_DOMAIN),
                        'progress' => __('Progress', EHD_PLUGIN_TEXT_DOMAIN),
                        'fraction' => __('Fraction', EHD_PLUGIN_TEXT_DOMAIN),
                        'custom'   => __('Custom', EHD_PLUGIN_TEXT_DOMAIN),
                    ],
                ],
                'delay'                 => [
                    'type'  => 'number',
                    'min'   => 0,
                    'max'   => '',
                    'std'   => 6000,
                    'label' => __('Delay', EHD_PLUGIN_TEXT_DOMAIN),
                ],
                'speed'                 => [
                    'type'  => 'number',
                    'min'   => 0,
                    'max'   => '',
                    'std'   => 1000,
                    'label' => __('Speed', EHD_PLUGIN_TEXT_DOMAIN),
                ],
                'show_cat'              => [
                    'type'  => 'checkbox',
                    'std'   => '',
                    'class' => 'checkbox',
                    'label' => __('Display post categories?', EHD_PLUGIN_TEXT_DOMAIN),
                ],
                'show_thumbnail'        => [
                    'type'  => 'checkbox',
                    'std'   => '',
                    'class' => 'checkbox',
                    'label' => __('Display post thumbnails?', EHD_PLUGIN_TEXT_DOMAIN),
                ],
                'show_date'             => [
                    'type'  => 'checkbox',
                    'std'   => '',
                    'class' => 'checkbox',
                    'label' => __('Display post date?', EHD_PLUGIN_TEXT_DOMAIN),
                ],
                'show_desc'             => [
                    'type'  => 'checkbox',
                    'std'   => '',
                    'class' => 'checkbox',
                    'label' => __('Display post description?', EHD_PLUGIN_TEXT_DOMAIN),
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
                'limit_time'          => [
                    'type'  => 'text',
                    'std'   => '',
                    'label' => __('Time limit', EHD_PLUGIN_TEXT_DOMAIN),
                    'desc'  => __('Constrain to just posts in a period of time', EHD_PLUGIN_TEXT_DOMAIN),
                ],
                'css_class'             => [
                    'type'  => 'text',
                    'std'   => '',
                    'label' => __('Css class', EHD_PLUGIN_TEXT_DOMAIN),
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
        public function widget($args, $instance)
        {
            if ($this->get_cached_widget($args)) {
                return;
            }

            $title = apply_filters('widget_title', $this->get_instance_title($instance), $instance, $this->id_base);
            $desc = $instance['desc'] ? trim($instance['desc']) : '';

            $number = (!empty($instance['number'])) ? absint($instance['number']) : $this->settings['number']['std'];

            $term_ids = $instance['category'] ?: $this->settings['category']['std'];
            $include_children = !empty($instance['include_children']);
            $limit_time = $instance['limit_time'] ? trim($instance['limit_time']) : $this->settings['limit_time']['std'];

            $show_cat = !empty($instance['show_cat']);
            $show_thumbnail = !empty($instance['show_thumbnail']);
            $show_date = !empty($instance['show_date']);
            $show_desc = !empty($instance['show_desc']);
            $show_viewmore_button = !empty($instance['show_viewmore_button']);

            $query_args = [
                'term_ids' => $term_ids,
                'include_children' => $include_children,
                'posts_per_page' => $number,
                'limit_time' => $limit_time,
                'wrapper' => 'div',
                'wrapper_class' => 'swiper-slide',
                'show' => [
                    'thumbnail' => Helper::toBool($show_thumbnail),
                    //'thumbnail_size' => 'medium',
                    //'scale' => true,
                    'time' => Helper::toBool($show_date),
                    'term' => Helper::toBool($show_cat),
                    'desc' => Helper::toBool($show_desc),
                    'more' => Helper::toBool($show_viewmore_button),
                ],
            ];

            // class
            $_class = $this->widget_classname . ' ' . $this->id;
            $css_class = (!empty($instance['css_class'])) ? sanitize_title($instance['css_class']) : '';
            if ($css_class) {
                $_class = $_class . ' ' . $css_class;
            }

            $full_width = !empty($instance['full_width']);
            $uniqid = esc_attr(uniqid($this->widget_classname . '-'));

            ob_start();

            ?>
            <section class="section carousel-section posts-carousel-section posts-section <?= $_class ?>" id="<?= $uniqid ?>">

                <?php if (!$full_width) echo '<div class="grid-container">'; ?>

                <?php if ($title) echo '<h2 class="heading-title">' . $title . '</h2>'; ?>
                <?php if ($desc) echo '<p class="heading-desc">' . $desc . '</p>'; ?>

                <div class="<?= $uniqid ?>" aria-label="<?php echo esc_attr($title); ?>">
                    <div class="swiper-section carousel-posts grid-posts">
                        <?php

                        $rows = !empty($instance['rows']) ? absint($instance['rows']) : $this->settings['rows']['std'];

                        $pagination = !empty($instance['pagination']) ? sanitize_title($instance['pagination']) : $this->settings['pagination']['std'];
                        $navigation = !empty($instance['navigation']);
                        $autoplay = !empty($instance['autoplay']);
                        $loop = !empty($instance['loop']);

                        $delay = !empty($instance['delay']) ? absint($instance['delay']) : $this->settings['delay']['std'];
                        $speed = !empty($instance['speed']) ? absint($instance['speed']) : $this->settings['speed']['std'];

                        $_columns_number = $instance['columns_number'] ?: $this->settings['columns_number']['std'];
                        $_gap = $instance['gap'] ?: $this->settings['gap']['std'];

                        $_columns_number = Helper::separatedToArray($_columns_number, '-');
                        $_gap = Helper::separatedToArray($_gap, '-');

                        //...
                        $desktop_gap = $_gap[0] ?? 0;
                        $mobile_gap = $_gap[1] ?? 0;
                        $columns_desktop = $_columns_number[0] ?? 0;
                        $columns_tablet = $_columns_number[1] ?? 0;
                        $columns_mobile = $_columns_number[2] ?? 0;

                        //...
                        $swiper_class = '';
                        $_data = [];

                        if ($desktop_gap > 0) $_data['desktop_gap'] = $desktop_gap;
                        if ($mobile_gap > 0) $_data['mobile_gap'] = $mobile_gap;

                        if ($delay > 0) $_data['delay'] = $delay;
                        if ($speed > 0) $_data['speed'] = $speed;

                        if ($pagination) $_data['pagination'] = $pagination;

                        if ($navigation) $_data['navigation'] = true;
                        if ($autoplay) $_data['autoplay'] = true;
                        if ($loop) $_data['loop'] = true;

                        if (!$columns_desktop && !$columns_tablet && !$columns_mobile) {
                            $_data['autoview'] = true;
                            $swiper_class .= ' autoview';
                        } else {
                            $_data['desktop'] = $columns_desktop;
                            $_data['tablet'] = $columns_tablet;
                            $_data['mobile'] = $columns_mobile;
                        }

                        if ($rows > 1) {
                            $_data['row'] = $rows;
                            $_data['loop'] = false;
                        }

                        $_data = json_encode($_data, JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE);

                        ?>
                        <div class="w-swiper swiper">
                            <div class="swiper-wrapper<?= $swiper_class ?>" data-options='<?= $_data; ?>'>
                                <?php
                                echo Helper::doShortcode(
                                    'posts',
                                    apply_filters(
                                        'posts_carousel_widget_shortcode_args',
                                        $query_args
                                    )
                                );
                                ?>
                            </div>
                        </div>
                    </div>
                    <?php

                    $show_viewmore_button = !empty($instance['show_viewmore_button']);

                    if ($show_viewmore_button) {
                        $viewmore_button_title = $instance['viewmore_button_title'] ?: '';

                        if ($viewmore_button_title) {

                            $viewmore_button_link = filter_var($instance['viewmore_button_link'], FILTER_VALIDATE_URL) ? $instance['viewmore_button_link'] : '#';
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
    }
}

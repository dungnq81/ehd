<?php

namespace EHD\Widgets;

use EHD\Cores\Helper;
use EHD\Cores\Widget;
use EHD\Themes\CSS;
use WP_Query;

\defined('ABSPATH') || die;

if (!class_exists('RecentPosts_Widget')) {
    class RecentPosts_Widget extends Widget
    {
        public function __construct()
        {
            $this->widget_description = __( 'Your site&#8217;s most recent Posts.' );
            $this->widget_name        = __( 'W - Recent Posts', EHD_PLUGIN_TEXT_DOMAIN );
            $this->settings           = [
                'title'          => [
                    'type'  => 'text',
                    'std'   => __( 'Recent Posts' ),
                    'label' => __( 'Title' ),
                ],
                'number'         => [
                    'type'  => 'number',
                    'min'   => 0,
                    'max'   => 99,
                    'std'   => 5,
                    'class' => 'tiny-text',
                    'label' => __( 'Number of posts to show:' ),
                ],
                'show_cat'       => [
                    'type'  => 'checkbox',
                    'std'   => '',
                    'class' => 'checkbox',
                    'label' => __( 'Display post categories?', EHD_PLUGIN_TEXT_DOMAIN ),
                ],
                'show_thumbnail' => [
                    'type'  => 'checkbox',
                    'std'   => '',
                    'class' => 'checkbox',
                    'label' => __( 'Display post thumbnails?', EHD_PLUGIN_TEXT_DOMAIN ),
                ],
                'show_date'      => [
                    'type'  => 'checkbox',
                    'std'   => '',
                    'class' => 'checkbox',
                    'label' => __( 'Display post date?' ),
                ],
                'show_desc'      => [
                    'type'  => 'checkbox',
                    'std'   => '',
                    'class' => 'checkbox',
                    'label' => __( 'Display post description?', EHD_PLUGIN_TEXT_DOMAIN ),
                ],
                'limit_time'     => [
                    'type'  => 'text',
                    'std'   => '',
                    'label' => __( 'Time limit', EHD_PLUGIN_TEXT_DOMAIN ),
                    'desc'  => sprintf( __( "A date/time string, restrict to only posts within a specific time period. %s", EHD_PLUGIN_TEXT_DOMAIN ), "\n<a target='_blank' href=\"https://www.php.net/manual/en/function.strtotime.php\">php.net/manual/en/function.strtotime.php</a>" ),
                ],
                'css_class'      => [
                    'type'  => 'text',
                    'std'   => '',
                    'label' => __( 'Css class', EHD_PLUGIN_TEXT_DOMAIN ),
                ],
            ];

            parent::__construct();
        }

        /**
         * Outputs the content for the current Recent Posts widget instance.
         *
         * @param array $args
         * @param array $instance
         */
        public function widget($args, $instance)
        {
            if ($this->get_cached_widget($args)) {
                return;
            }

            $title = apply_filters( 'widget_title', $this->get_instance_title( $instance ), $instance, $this->id_base );

            $number         = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 5;
            $show_cat       = ! empty( $instance['show_cat'] );
            $show_thumbnail = ! empty( $instance['show_thumbnail'] );
            $show_date      = ! empty( $instance['show_date'] );
            $show_desc      = ! empty( $instance['show_desc'] );

            $limit_time = $instance['limit_time'] ? trim( $instance['limit_time'] ) : '';

            $query_args = [
                'update_post_meta_cache' => false,
                'update_post_term_cache' => false,
                'post_type'              => 'post',
                'post_status'            => 'publish',
                'posts_per_page'         => $number,
                'no_found_rows'          => true,
                'ignore_sticky_posts'    => true,
            ];

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

            $r = new WP_Query(
                /**
                 * Filters the arguments for the Recent Posts widget.
                 *
                 * @param array $args     An array of arguments used to retrieve the recent posts.
                 * @param array $instance Array of settings for the current widget.
                 * @see   WP_Query::get_posts()
                 */
                apply_filters(
                    'widget_recent_posts_args',
                    $query_args,
                    $instance
                )
            );

            //$_class = $this->widget_classname . ' ' . $this->id;
	        $_class = $this->widget_classname;
            $css_class = (!empty($instance['css_class'])) ? sanitize_title($instance['css_class']) : '';
            if ($css_class) {
                $_class = $_class . ' ' . $css_class;
            }

	        $uniqid = esc_attr(uniqid($this->widget_classname . '-'));

            if (!$r->have_posts()) return;

            ob_start();

            ?>
            <div class="<?php echo $_class; ?>" id="<?= $uniqid ?>">
                <?php if ($title) : ?>
                <span class="sidebar-title"><?php echo $title; ?></span>
                <?php endif;

                // The title may be filtered: Strip out HTML and make sure the aria-label is never empty.
                $title = trim(strip_tags($title));
                $aria_label = $title ?: __( 'Recent Posts', EHD_PLUGIN_TEXT_DOMAIN );

                ?>
                <nav class="<?= $uniqid ?>" aria-label="<?php echo esc_attr($aria_label); ?>">
                    <ul>
                        <?php

                        $ratio_obj = Helper::getAspectRatioClass( 'post', 'aspect_ratio__options' );
                        $ratio_class = $ratio_obj->class ?? '';

                        foreach ($r->posts as $recent_post) :
                            $post_title = get_the_title($recent_post->ID);
                            $title = (!empty($post_title)) ? $post_title : __( '(no title)', EHD_PLUGIN_TEXT_DOMAIN );
                            $post_thumbnail = get_the_post_thumbnail($recent_post, 'medium');

                            $aria_current = '';
                            if (get_queried_object_id() === $recent_post->ID) {
                                $aria_current = ' aria-current="page"';
                            }
                            ?>
                            <li>
                                <?php if ($show_thumbnail && $post_thumbnail) : ?>
                                <a class="d-block cover" href="<?php the_permalink($recent_post->ID); ?>" aria-label="<?php echo esc_attr($title); ?>" tabindex="0">
                                    <span class="after-overlay res <?= $ratio_class ?>"><?php echo $post_thumbnail; ?></span>
                                </a>
                                <?php endif; ?>
                                <div class="cover-content">
                                    <a href="<?php the_permalink($recent_post->ID); ?>" title="<?php echo esc_attr($title); ?>"<?php echo $aria_current; ?>><?php echo $title; ?></a>
                                    <?php if ($show_date || $show_cat) : ?>
                                    <div class="meta">
                                        <?php if ($show_date) : ?>
                                        <span class="post-date"><?php echo Helper::humanizeTime($recent_post); ?></span>
                                        <?php endif;
                                        if ($show_cat) echo Helper::getPrimaryTerm($recent_post);
                                        ?>
                                    </div>
                                    <?php endif; ?>
                                    <?php if ($show_desc) echo Helper::loopExcerpt($recent_post); ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </nav>
            </div>
            <?php

            echo $this->cache_widget($args, ob_get_clean()); // WPCS: XSS ok.
        }
    }
}

<?php

namespace EHD\Widgets;

use EHD\Cores\Helper;
use EHD\Cores\Widget;
use WP_Query;

\defined('ABSPATH') || die;

if (!class_exists('RecentPosts_Widget')) {
    class RecentPosts_Widget extends Widget
    {
        public function __construct()
        {
            $this->widget_description = __('Your site&#8217;s most recent Posts.');
            $this->widget_name = __('W - Recent Posts', EHD_PLUGIN_TEXT_DOMAIN);
            $this->settings = [
                'title' => [
                    'type' => 'text',
                    'std' => __('Recent Posts'),
                    'label' => __('Title'),
                ],
                'number' => [
                    'type' => 'number',
                    'min' => 0,
                    'max' => 99,
                    'std' => 5,
                    'class' => 'tiny-text',
                    'label' => __('Number of posts to show:'),
                ],
                'show_cat' => [
                    'type' => 'checkbox',
                    'std' => '',
                    'class' => 'checkbox',
                    'label' => __('Display post categories?', EHD_PLUGIN_TEXT_DOMAIN),
                ],
                'show_thumbnail' => [
                    'type' => 'checkbox',
                    'std' => '',
                    'class' => 'checkbox',
                    'label' => __('Display post thumbnails?', EHD_PLUGIN_TEXT_DOMAIN),
                ],
                'show_date' => [
                    'type' => 'checkbox',
                    'std' => '',
                    'class' => 'checkbox',
                    'label' => __('Display post date?'),
                ],
                'css_class' => [
                    'type' => 'text',
                    'std' => '',
                    'label' => __('Css class', EHD_PLUGIN_TEXT_DOMAIN),
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
            $title = apply_filters('widget_title', $this->get_instance_title($instance), $instance, $this->id_base);

            $number = (!empty($instance['number'])) ? absint($instance['number']) : 5;
            $show_cat = $instance['show_cat'] ?? false;
            $show_thumbnail = $instance['show_thumbnail'] ?? false;
            $show_date = $instance['show_date'] ?? false;

            $css_class = (!empty($instance['css_class'])) ? sanitize_title($instance['css_class']) : '';

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
                    [
                        'update_post_meta_cache' => false,
                        'update_post_term_cache' => false,

                        'post_status' => 'publish',
                        'posts_per_page' => $number,
                        'no_found_rows' => true,
                        'ignore_sticky_posts' => true,
                    ],
                    $instance
                )
            );

            // class
            $_class = $this->widget_classname . ' ' . $this->id;
            if ($css_class) {
                $_class = $_class . ' ' . $css_class;
            }

            if (!$r->have_posts()) return;

            ?>
            <div class="<?php echo $_class; ?>">
                <?php if ($title) : ?>
                <span class="sidebar-title"><?php echo $title; ?></span>
                <?php endif;

                // The title may be filtered: Strip out HTML and make sure the aria-label is never empty.
                $title = trim(strip_tags($title));
                $aria_label = $title ?: __('Recent Posts', EHD_PLUGIN_TEXT_DOMAIN);;

                ?>
                <nav aria-label="<?php echo esc_attr($aria_label); ?>">
                    <ul>
                        <?php
                        foreach ($r->posts as $recent_post) :
                            $post_title = get_the_title($recent_post->ID);
                            $title = (!empty($post_title)) ? $post_title : __('(no title)', EHD_PLUGIN_TEXT_DOMAIN);
                            $post_thumbnail = get_the_post_thumbnail($recent_post, 'post-thumbnail');

                            $aria_current = '';
                            if (get_queried_object_id() === $recent_post->ID) {
                                $aria_current = ' aria-current="page"';
                            }
                            ?>
                            <li>
                                <?php
                                if ($show_thumbnail && $post_thumbnail) :
                                    $ratio = Helper::getThemeMod('news_menu_setting');
                                    $ratio_class = $ratio;
                                    if ('default' == $ratio or !$ratio) {
                                        $ratio_class = '3-2';
                                    }
                                ?>
                                <a class="d-block" href="<?php the_permalink($recent_post->ID); ?>"
                                   aria-label="<?php echo esc_attr($title); ?>" tabindex="0">
                                    <span class="cover after-overlay res ar-<?= $ratio_class ?>"><?php echo $post_thumbnail; ?></span>
                                </a>
                                <?php endif; ?>
                                <div class="post-info">
                                    <a href="<?php the_permalink($recent_post->ID); ?>"
                                       title="<?php echo esc_attr($title); ?>"<?php echo $aria_current; ?>><?php echo $title; ?></a>
                                    <?php if ($show_date || $show_cat) : ?>
                                        <div class="meta">
                                            <?php if ($show_date) : ?>
                                            <span class="post-date"><?php echo Helper::humanizeTime($recent_post); ?></span>
                                            <?php endif;
                                            if ($show_cat) echo Helper::getPrimaryTerm($recent_post);
                                            ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </nav>
            </div>
            <?php
        }
    }
}

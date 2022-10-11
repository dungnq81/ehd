<?php

namespace EHD\Plugins\Widgets;

use EHD\Plugins\Core\Helper;
use EHD\Plugins\Core\Widget;

\defined('ABSPATH') || die;

if (!class_exists('Search_Widget')) {
    class Search_Widget extends Widget
    {
        public function __construct()
        {
            $this->widget_description = __('A search form for your site.');
            $this->widget_name = __('Search');
            $this->settings = [
                'title' => [
                    'type' => 'text',
                    'std' => __('Search'),
                    'label' => __('Title'),
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
         * Creating widget front-end
         *
         * @param array $args
         * @param array $instance
         */
        public function widget($args, $instance)
        {
            if ($this->get_cached_widget($args)) {
                return;
            }

            ob_start();

            $title = apply_filters('widget_title', $this->get_instance_title($instance), $instance, $this->id_base);
            $css_class = ( ! empty( $instance['css_class'] ) ) ? sanitize_title($instance['css_class']) : '';

            $_unique_id = esc_attr(uniqid('search-form-'));
            $title_for = __('Search for', EHD_PLUGIN_TEXT_DOMAIN);
            $placeholder_title = esc_attr(__('Search ...', EHD_PLUGIN_TEXT_DOMAIN));

            // class
            $_class = $this->widget_classname . ' ' . $this->id;
            if ($css_class) {
                $_class = $_class . ' ' . $css_class;
            }

            ?>
            <div class="inside-search <?php echo $_class; ?>">
                <form role="search" action="<?php echo Helper::home(); ?>" class="frm-search" method="get"
                      accept-charset="UTF-8" data-abide novalidate>
                    <label for="<?php echo $_unique_id; ?>" class="screen-reader-text"><?php echo $title_for; ?></label>
                    <input id="<?php echo $_unique_id; ?>" required pattern="^(.*\S+.*)$" type="search"
                           autocomplete="off" name="s" value="<?php echo get_search_query(); ?>"
                           placeholder="<?php echo $placeholder_title; ?>">
                    <button type="submit" data-glyph="">
                        <span><?php echo $title; ?></span>
                    </button>
                    <?php if (class_exists('\WooCommerce')) : ?>
                        <input type="hidden" name="post_type" value="product">
                    <?php endif; ?>
                </form>
            </div>
            <?php
            echo $this->cache_widget( $args, ob_get_clean() ); // WPCS: XSS ok.
        }
    }
}

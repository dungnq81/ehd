<?php

namespace EHD\Ins\Woocommerce;

use EHD\Plugins\Core\Helper;
use WP_Query;

\defined('\WPINC') || die;

// If plugin - 'Woocommerce' not exist then return.
if ( ! class_exists( '\WooCommerce' ) ) {
    return;
}

if (!class_exists('Woocommerce_Ins')) {
    require __DIR__ . '/woocommerce.php';

    class Woocommerce_Ins
    {
        public function __construct()
        {
            // ...
            add_action('after_setup_theme', [&$this, 'woocommerce_setup'], 31);
            add_action('wp_enqueue_scripts', [&$this, 'enqueue_scripts'], 99);

            add_action('woocommerce_email', [&$this, 'woocommerce_email_hooks'], 100, 1);

            // woocommerce use priority 20
            add_filter('body_class', [&$this, 'woocommerce_body_class']);
            add_filter('woocommerce_post_class', [&$this, 'woocommerce_post_class'], 21, 2);
            add_filter('product_cat_class', [&$this, 'product_cat_class'], 21, 2);

            // ...
            // Display featured products first and Out of stock products at last in shop page
            add_filter('posts_clauses', [&$this, 'featured_products_first'], 11, 2);

            // Visited cookies products
            add_action('wp', [&$this, 'visited_product_cookie']);

            // before shop loop
            add_action('woocommerce_before_shop_loop', function () {
                echo '<div class="woocommerce-sorting">';
            }, 7);

            add_action('woocommerce_before_shop_loop', function () {
                echo '</div>';
            }, 31);
        }

        /** ---------------------------------------- */
        /** ---------------------------------------- */

        /**
         * Woocommerce setup
         *
         * @return void
         */
        public function woocommerce_setup()
        {
            // Remove default WooCommerce wrappers.
            remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper');
            remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end');
            remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar');

            remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);

            // below add to cart
            remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);
            add_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 41);

            // Declare WooCommerce support.
            add_filter('woocommerce_get_image_size_gallery_thumbnail', function ($size) {
                return [
                    'width' => 180,
                    'height' => 180,
                    'crop' => 0,
                ];
            });

            /**
             * @param array $args
             *
             * @return array
             */
            add_filter('woocommerce_product_tag_cloud_widget_args', function (array $args) {
                $args['smallest'] = '10';
                $args['largest'] = '19';
                $args['unit'] = 'px';
                $args['number'] = 12;

                return $args;
            });

            /**
             * @param $orders
             * @return array
             */
            add_filter('woocommerce_catalog_orderby', function ($orders) {
                $orders = [
                    'menu_order' => __('Thứ tự sắp xếp', EHD_TEXT_DOMAIN),
                    'popularity' => __('Phổ biến', EHD_TEXT_DOMAIN),
                    'rating' => __('Đánh giá', EHD_TEXT_DOMAIN),
                    'date' => __('Mới nhất', EHD_TEXT_DOMAIN),
                    'price' => __('Giá thấp đến cao', EHD_TEXT_DOMAIN),
                    'price-desc' => __('Giá cao đến thấp', EHD_TEXT_DOMAIN),
                ];

                return $orders;

            }, 100, 1);

            /**
             * @param $defaults
             *
             * @return array
             */
            add_filter('woocommerce_breadcrumb_defaults', function ($defaults) {
                $defaults = [
                    'delimiter' => '',
                    'wrap_before' => '<ul id="breadcrumbs" class="breadcrumbs" aria-label="Breadcrumbs">',
                    'wrap_after' => '</ul>',
                    'before' => '<li><span property="itemListElement" typeof="ListItem">',
                    'after' => '</span></li>',
                    'home' => __('Home', EHD_TEXT_DOMAIN),
                ];

                return $defaults;

            }, 11, 1);

            // -------------------------------------------------------------

//            add_filter('woocommerce_product_single_add_to_cart_text', function () {
//                return __('Thêm vào giỏ hàng', 'hd');
//            });
//
//            add_filter('woocommerce_product_add_to_cart_text', function () {
//                return __('Thêm vào giỏ', 'hd');
//            });
        }

        /** ---------------------------------------- */
        /** ---------------------------------------- */

        /**
         * @param           $posts_clauses
         * @param WP_Query  $query
         * @return mixed
         */
        public function featured_products_first($posts_clauses, WP_Query $query)
        {
            global $wpdb;

            if ($query->is_main_query() && (is_product_tag() || is_product_taxonomy() || @is_shop())) {
                $featured_ids = wc_get_featured_product_ids();
                $posts_clauses['join'] .= " LEFT JOIN $wpdb->postmeta istockstatus ON ($wpdb->posts.ID = istockstatus.post_id AND istockstatus.meta_key = '_stock_status' AND istockstatus.meta_value <> '') ";
                $posts_clauses['orderby'] = " if($wpdb->posts.ID in (0" . (implode(",", $featured_ids)) . "), 0, 1), istockstatus.meta_value ASC, " . $posts_clauses['orderby'];
            }

            return $posts_clauses;

        }

        /** ---------------------------------------- */
        /** ---------------------------------------- */

        /**
         * @return void
         */
        public function visited_product_cookie()
        {
            if (!is_singular('product')) {
                return;
            }

            global $post;

            if (empty($_COOKIE['woocommerce_recently_viewed'])) {
                $viewed_products = [];
            } else {
                $viewed_products = wp_parse_id_list((array) explode('|', wp_unslash($_COOKIE['woocommerce_recently_viewed'])));
            }

            $keys = array_flip($viewed_products);

            if (isset($keys[$post->ID])) {
                unset($viewed_products[$keys[$post->ID]]);
            }

            $viewed_products[] = $post->ID;

            if (count($viewed_products) > 22) {
                array_shift($viewed_products);
            }

            wc_setcookie('woocommerce_recently_viewed', implode('|', $viewed_products));
        }

        /** ---------------------------------------- */
        /** ---------------------------------------- */

        /**
         * Add 'woocommerce' class to the body tag
         *
         * @param array $classes css classes applied to the body tag.
         * @return array $classes modified to include 'woocommerce' class
         */
        public function woocommerce_body_class($classes)
        {
            $classes[] = 'woocommerce';
            return $classes;
        }

        /** ---------------------------------------- */
        /** ---------------------------------------- */

        public function woocommerce_post_class($classes, $product)
        {
            if ('product' == get_post_type()) {

                // remove product_cat- classes
                foreach ($classes as $class) {
                    if (
                        str_contains($class, 'product_cat-')
                        || str_contains($class, 'product_tag-')
                    ) {
                        $classes = array_diff($classes, [$class]);
                    }
                }
            }

            return $classes;
        }

        /** ---------------------------------------- */
        /** ---------------------------------------- */

        public function product_cat_class($classes, $product)
        {
            return $classes;
        }

        /** ---------------------------------------- */
        /** ---------------------------------------- */

        /**
         * @param $mailer
         */
        public function woocommerce_email_hooks($mailer)
        {
            add_action('woocommerce_order_status_pending_to_on-hold_notification', [
                $mailer->emails['WC_Email_Customer_On_Hold_Order'],
                'trigger'
            ]);
        }

        /** ---------------------------------------- */
        /** ---------------------------------------- */

        /**
         * @return void
         */
        public function enqueue_scripts()
        {
            wp_enqueue_style("woocommerce-style", get_stylesheet_directory_uri() . '/assets/css/woocommerce.css', ["layout-style"], EHD_THEME_VERSION);
        }
    }
}

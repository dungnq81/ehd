<?php

namespace EHD\Plugins\Woocommerce;

use EHD\Themes\Func;
use WP_Query;

\defined('\WPINC') || die;

// If plugin - 'Woocommerce' not exist then return.
if ( ! class_exists( '\WooCommerce' ) ) {
    return;
}

if (!class_exists('Woocommerce_Plugin')) {
    require __DIR__ . '/woocommerce.php';

    class Woocommerce_Plugin
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

            //...
            add_action('woocommerce_product_video', [&$this, 'acf_video_product'], 21);

            // https://stackoverflow.com/questions/57321805/remove-header-from-the-woocommerce-administrator-panel
            add_action('admin_head', function () {
                remove_action('in_admin_header', ['Automattic\WooCommerce\Internal\Admin\Loader', 'embed_page_header']);
                remove_action('in_admin_header', ['Automattic\WooCommerce\Admin\Loader', 'embed_page_header']);

                echo '<style>#wpadminbar ~ #wpbody { margin-top: 0 !important; }</style>';
            });

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
            add_theme_support('woocommerce');

            // Add support for WC features.
            //add_theme_support( 'wc-product-gallery-zoom' );
            //add_theme_support( 'wc-product-gallery-lightbox' );
            //add_theme_support( 'wc-product-gallery-slider' );

            // Remove woocommerce defauly styles
            add_filter( 'woocommerce_enqueue_styles', '__return_false' );

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

            // Trim zeros in price decimals
            add_filter('woocommerce_price_trim_zeros', '__return_true');

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
                    'menu_order' => __('Thứ tự sắp xếp', 'ehd'),
                    'popularity' => __('Phổ biến', 'ehd'),
                    'rating' => __('Đánh giá', 'ehd'),
                    'date' => __('Mới nhất', 'ehd'),
                    'price' => __('Giá thấp đến cao', 'ehd'),
                    'price-desc' => __('Giá cao đến thấp', 'ehd'),
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
                    'home' => __('Home', 'ehd'),
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
         * @return void
         */
        public function acf_video_product()
        {
            global $product;

            $video_link = ewc_video_product($product->get_id(), 'video_link');
            if ($video_link) {
                echo '<div class="product-vid video-product">';
                echo '<a title="Video" href="' . $video_link . '" data-glyph="" class="_blank fcy-video"></a>';
                echo '</div>';
            }
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
         * Add 'woocommerce-active' class to the body tag
         *
         * @param array $classes css classes applied to the body tag.
         * @return array $classes modified to include 'woocommerce-active' class
         */
        public function woocommerce_body_class($classes)
        {
            $classes[] = 'woocommerce-active';
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

            $gutenberg_widgets_off = Func::getThemeMod('gutenberg_use_widgets_block_editor_setting');
            $gutenberg_off = Func::getThemeMod('use_block_editor_for_post_type_setting');
            if ($gutenberg_widgets_off && $gutenberg_off) {

                // Remove WooCommerce block CSS
                wp_deregister_style('wc-blocks-vendors-style');
                wp_deregister_style('wc-block-style');
            }
        }
    }
}
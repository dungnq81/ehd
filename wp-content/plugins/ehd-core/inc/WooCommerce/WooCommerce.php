<?php

namespace EHD\Plugins\WooCommerce;

use EHD\Plugins\Core\Helper;

use EHD\Plugins\Widgets\MiniCart_Widget;
use EHD\Plugins\Widgets\Products_Widget;
use EHD\Plugins\Widgets\ProductsCarousel_Widget;
use EHD\Plugins\Widgets\RecentProducts_Widget;

\defined('ABSPATH') || die;

final class WooCommerce
{
    public function __construct()
    {
        $this->_hooks();

        add_action('after_setup_theme', [&$this, 'after_setup_theme'], 31);
        add_action('wp_enqueue_scripts', [&$this, 'enqueue_scripts'], 99);

        add_action('widgets_init', [&$this, 'unregister_default_widgets'], 31);
        add_action('widgets_init', [&$this, 'register_widgets'], 31);
    }

    /**
     * Registers a WP_Widget widget
     *
     * @return void
     */
    public function register_widgets() : void
    {
        class_exists(MiniCart_Widget::class) && register_widget(new MiniCart_Widget());
        class_exists(Products_Widget::class) && register_widget(new Products_Widget());
        class_exists(RecentProducts_Widget::class) && register_widget(new RecentProducts_Widget());
        class_exists(ProductsCarousel_Widget::class) && register_widget(new ProductsCarousel_Widget());
    }

    /**
     * Unregisters a WP_Widget widget
     *
     * @return void
     */
    public function unregister_default_widgets() : void
    {
        unregister_widget('WC_Widget_Product_Search');
        unregister_widget('WC_Widget_Products');
    }

    /**
     * @return void
     */
    public function enqueue_scripts() : void
    {
        wp_enqueue_style('ehd-core-woocommerce-style', EHD_PLUGIN_URL . "assets/css/woocommerce.css", ["ehd-core-style"], EHD_PLUGIN_VERSION);

        $gutenberg_widgets_off = Helper::getThemeMod('gutenberg_use_widgets_block_editor_setting');
        $gutenberg_off = Helper::getThemeMod('use_block_editor_for_post_type_setting');
        if ($gutenberg_widgets_off && $gutenberg_off) {

            // Remove WooCommerce block CSS
            wp_deregister_style('wc-blocks-vendors-style');
            wp_deregister_style('wc-block-style');
        }
    }

    /**
     * @return void
     */
    public function after_setup_theme() : void
    {
        add_theme_support('woocommerce');

        // Add support for WC features.
        //add_theme_support( 'wc-product-gallery-zoom' );
        //add_theme_support( 'wc-product-gallery-lightbox' );
        //add_theme_support( 'wc-product-gallery-slider' );

        // Remove woocommerce defauly styles
        add_filter('woocommerce_enqueue_styles', '__return_false');
    }

    /**
     * @return void
     */
    protected function _hooks() : void
    {
        // https://stackoverflow.com/questions/57321805/remove-header-from-the-woocommerce-administrator-panel
        add_action('admin_head', function () {
            remove_action('in_admin_header', ['Automattic\WooCommerce\Internal\Admin\Loader', 'embed_page_header']);
            remove_action('in_admin_header', ['Automattic\WooCommerce\Admin\Loader', 'embed_page_header']);

            echo '<style>#wpadminbar ~ #wpbody { margin-top: 0 !important; }</style>';
        });

        // Trim zeros in price decimals
        add_filter('woocommerce_price_trim_zeros', '__return_true');
    }
}

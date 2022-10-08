<?php

namespace EHD\Plugins\WooCommerce;

\defined('ABSPATH') || die;

final class WooCommerce
{
    public function __construct()
    {
        add_action('widgets_init', [&$this, 'unregister_default_widgets'], 31);
    }

    /**
     * Unregisters a WP_Widget widget
     *
     * @return void
     */
    public function unregister_default_widgets()
    {
        unregister_widget('WC_Widget_Product_Search');
    }
}
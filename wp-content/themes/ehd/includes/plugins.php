<?php
/**
 * Plugins functions
 * @author WEBHD
 */

\defined( '\WPINC' ) || die;

use EHD\Plugins\Woocommerce\Woocommerce_Plugin;

class_exists( Woocommerce_Plugin::class ) && ( new Woocommerce_Plugin );
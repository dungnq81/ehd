<?php
/**
 * Plugins functions
 *
 * @author WEBHD
 */

use EHD\Sites\Plugins\ACF\ACF_Plugin;
use EHD\Sites\Plugins\CF7_Plugin;
use EHD\Sites\Plugins\Elementor\Elementor_Plugin;
use EHD\Sites\Plugins\LiteSpeed_Plugin;
use EHD\Sites\Plugins\RankMath_Plugin;
use EHD\Sites\Plugins\Woocommerce\Woocommerce_Plugin;
use EHD\Sites\Plugins\WpRocket_Plugin;

\defined('\WPINC') || die;

//...

class_exists(Woocommerce_Plugin::class) && (new Woocommerce_Plugin());
class_exists(RankMath_Plugin::class) && (new RankMath_Plugin());
class_exists(WpRocket_Plugin::class) && (new WpRocket_Plugin());
class_exists(LiteSpeed_Plugin::class) && (new LiteSpeed_Plugin());
class_exists(Elementor_Plugin::class) && (new Elementor_Plugin());
class_exists(CF7_Plugin::class) && (new CF7_Plugin());
class_exists(ACF_Plugin::class) && (new ACF_Plugin());

<?php
/**
 * Plugins functions
 *
 * @author WEBHD
 */

use EHD\Ins\ACF\ACF_Ins;
use EHD\Ins\CF7_Ins;
use EHD\Ins\Elementor\Elementor_Ins;
use EHD\Ins\LiteSpeed_Ins;
use EHD\Ins\RankMath_Ins;
use EHD\Ins\Woocommerce\Woocommerce_Ins;
use EHD\Ins\WpRocket_Ins;

\defined('\WPINC') || die;

//...

class_exists(Woocommerce_Ins::class) && (new Woocommerce_Ins);
class_exists(RankMath_Ins::class) && (new RankMath_Ins);
class_exists(WpRocket_Ins::class) && (new WpRocket_Ins);
class_exists(LiteSpeed_Ins::class) && (new LiteSpeed_Ins);
class_exists(Elementor_Ins::class) && (new Elementor_Ins);
class_exists(CF7_Ins::class) && (new CF7_Ins);
class_exists(ACF_Ins::class) && (new ACF_Ins);
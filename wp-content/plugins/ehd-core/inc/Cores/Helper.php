<?php

namespace EHD\Cores;

use EHD\Cores\Traits\File;
use EHD\Cores\Traits\Plugin;
use EHD\Cores\Traits\Wp;

use EHD\Cores\Traits\Elementor;
use EHD\Cores\Traits\WooCommerce;

\defined('ABSPATH') || die;

/**
 * Helper Class
 *
 * @author WEBHD
 */
final class Helper
{
    use File;
    use Plugin;
    use Wp;
    use WooCommerce;
    use Elementor;

    // --------------------------------------------------

    /**
     * @return string[]
     */
    public static function getSqlOperators() : array
    {
        $compare = self::getMetaCompare();
        $compare['IS NULL'] = 'IS NULL';
        $compare['IS NOT NULL'] = 'IS NOT NULL';

        return $compare;
    }

    /**
     * @return string[]
     */
    public static function getMetaCompare() : array
    {
        // meta_compare (string) - Operator to test the 'meta_value'. Possible values are '=', '!=', '>', '>=', '<', '<=', 'LIKE', 'NOT LIKE', 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN', 'NOT EXISTS', 'REGEXP', 'NOT REGEXP' or 'RLIKE'. Default value is '='.
        return ['=' => '=', '>' => '&gt;', '>=' => '&gt;=', '<' => '&lt;', '<=' => '&lt;=', '!=' => '!=', 'LIKE' => 'LIKE', 'RLIKE' => 'RLIKE', 'NOT LIKE' => 'NOT LIKE', 'IN' => 'IN (...)', 'NOT IN' => 'NOT IN (...)', 'BETWEEN' => 'BETWEEN', 'NOT BETWEEN' => 'NOT BETWEEN', 'EXISTS' => 'EXISTS', 'NOT EXISTS' => 'NOT EXISTS', 'REGEXP' => 'REGEXP', 'NOT REGEXP' => 'NOT REGEXP'];
    }

    // -------------------------------------------------------------

    /**
     * @param bool $img_wrap
     * @param bool $thumb
     * @return string
     */
    public static function placeholderSrc(bool $img_wrap = true, bool $thumb = true) : string
    {
        $src = EHD_PLUGIN_URL . 'assets/img/placeholder.png';
        if ($thumb) {
            $src = EHD_PLUGIN_URL . 'assets/img/placeholder-320x320.png';
        }
        if ($img_wrap) {
            $src = "<img loading=\"lazy\" src=\"{$src}\" alt=\"placeholder\" class=\"wp-placeholder\">";
        }

        return $src;
    }
}
